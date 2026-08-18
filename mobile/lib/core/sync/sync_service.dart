import 'dart:convert';

import 'package:drift/drift.dart';
import 'package:http/http.dart' as http;

import '../../data/db/app_database.dart' as db;
import '../session/session_store.dart';

class SyncException implements Exception {
  final String message;

  SyncException(this.message);

  @override
  String toString() => message;
}

class SyncResult {
  final int pushed;
  final int pulled;
  final String? error;

  SyncResult({this.pushed = 0, this.pulled = 0, this.error});
}

/// Two-way sync with the web API (Laravel): push first, then pull.
class SyncService {
  SyncService(this._db);

  final db.AppDatabase _db;

  /// Push order: parents before children so FK mapping stays safe.
  static const _order = [
    'clients',
    'projects',
    'invoices',
    'invoice_payments',
    'app_ideas',
    'brain_dumps',
    'notes',
    'savings_goals',
    'savings_transactions',
  ];

  static const _fkColumns = {
    'projects': 'client_id',
    'invoices': 'client_id',
    'invoice_payments': 'invoice_id',
    'savings_transactions': 'goal_id',
  };

  Map<String, String> _headers(Session s, {bool json = true}) => {
        'Authorization': 'Bearer ${s.token}',
        'Accept': 'application/json',
        if (json) 'Content-Type': 'application/json',
      };
}

extension SyncAuthHelper on SyncService {
  /// Login to the web API; throws [SyncException] on failure.
  Future<Map<String, dynamic>> login({
    required String serverUrl,
    required String email,
    required String password,
  }) {
    return _auth(serverUrl, '/api/v1/login', {'email': email, 'password': password});
  }

  Future<Map<String, dynamic>> register({
    required String serverUrl,
    required String name,
    required String email,
    required String password,
  }) {
    return _auth(serverUrl, '/api/v1/register', {
      'name': name,
      'email': email,
      'password': password,
    });
  }

  Future<Map<String, dynamic>> _auth(
      String serverUrl, String path, Map<String, dynamic> body) async {
    final http.Response res;
    try {
      res = await http
          .post(
            Uri.parse('$serverUrl$path'),
            headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
            body: jsonEncode(body),
          )
          .timeout(const Duration(seconds: 15));
    } on Exception {
      throw SyncException('Tidak bisa terhubung ke server.');
    }

    final data = jsonDecode(res.body) as Map<String, dynamic>? ?? {};
    if (res.statusCode != 200) {
      throw SyncException(
        (data['message'] as String?) ?? 'Gagal login (${res.statusCode}).',
      );
    }

    return data;
  }

  /// Push local dirty rows, then pull remote changes (LWW by updated_at).
  Future<SyncResult> sync(Session session) async {
    var pushed = 0;
    var pulled = 0;

    // 1) Push
    final payload = <String, dynamic>{};
    for (final table in SyncService._order) {
      final rows = await collectDirty(table, session.userId);
      if (rows.isNotEmpty) {
        payload[table] = rows;
        pushed += rows.length;
      }
    }
    if (payload.isNotEmpty) {
      final res = await http.post(
        Uri.parse('${session.serverUrl}/api/v1/sync/push'),
        headers: _headers(session),
        body: jsonEncode({'tables': payload}),
      ).timeout(const Duration(seconds: 20), onTimeout: () => http.Response('', 504));
      if (res.statusCode != 200) {
        return SyncResult(
          pushed: 0,
          error: 'Push gagal (${res.statusCode}): ${res.body}',
        );
      }
      final created = ((jsonDecode(res.body) as Map<String, dynamic>)['created']
              as Map<String, dynamic>?) ??
          {};
      await applyCreated(created);
      await clearAllDirty();
    }

    // 2) Pull
    try {
      pulled = await pullChanges(session);
    } on SyncException catch (e) {
      return SyncResult(pushed: pushed, error: e.message);
    }

    return SyncResult(pushed: pushed, pulled: pulled);
  }

  Future<int> pullChanges(Session session) async {
    final since = await _db.meta('last_synced_at');
    final uri = Uri.parse('${session.serverUrl}/api/v1/sync/pull'
        '${since != null ? '?since=${Uri.encodeComponent(since)}' : ''}');

    final res = await http
        .get(uri, headers: _headers(session))
        .timeout(const Duration(seconds: 20), onTimeout: () => http.Response('', 504));

    if (res.statusCode != 200) {
      throw SyncException('Pull gagal (${res.statusCode}): ${res.body}');
    }

    final data = jsonDecode(res.body) as Map<String, dynamic>;
    final tables = (data['tables'] as Map<String, dynamic>?) ?? {};
    var pulled = 0;

    for (final table in SyncService._order) {
      final rows = tables[table];
      if (rows is! List) continue;
      await applyPull(table, rows.cast<Map<String, dynamic>>());
      pulled += rows.length;
    }

    final serverTime = data['server_time'];
    if (serverTime is String) {
      await _db.setMeta('last_synced_at', serverTime);
    }

    return pulled;
  }

  Future<List<Map<String, dynamic>>> collectDirty(String table, int userId) async {
    switch (table) {
      case 'clients':
        final rows = await (_db.select(_db.clients)..where((t) => t.dirty.equals(true))).get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'name': r.name,
              'email': r.email,
              'phone': r.phone,
              'company': r.company,
              'notes': r.notes,
              'status': r.status,
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      case 'projects':
        final rows = await (_db.select(_db.projects)..where((t) => t.dirty.equals(true))).get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'client_id': r.clientId,
              'name': r.name,
              'description': r.description,
              'category': r.category,
              'status': r.status,
              'platform': r.platform,
              'tech_stack': r.techStack,
              'progress': r.progress,
              'deadline': r.deadline?.toIso8601String(),
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      case 'invoices':
        final rows = await (_db.select(_db.invoices)..where((t) => t.dirty.equals(true))).get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'client_id': r.clientId,
              'invoice_number': r.invoiceNumber,
              'items': jsonDecode(r.items),
              'subtotal': r.subtotal,
              'tax_percent': r.taxPercent,
              'tax_amount': r.taxAmount,
              'discount_percent': r.discountPercent,
              'discount_amount': r.discountAmount,
              'total': r.total,
              'paid_amount': r.paidAmount,
              'status': r.status,
              'due_date': r.dueDate?.toIso8601String(),
              'paid_at': r.paidAt?.toIso8601String(),
              'notes': r.notes,
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      case 'invoice_payments':
        final rows = await (_db.select(_db.invoicePayments)
              ..where((t) => t.dirty.equals(true)))
            .get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'invoice_id': r.invoiceId,
              'amount': r.amount,
              'remaining_after': r.remainingAfter,
              'notes': r.notes,
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      case 'app_ideas':
        final rows = await (_db.select(_db.appIdeas)..where((t) => t.dirty.equals(true))).get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'name': r.name,
              'tagline': r.tagline,
              'description': r.description,
              'features': jsonDecode(r.features),
              'tech_stack': jsonDecode(r.techStack),
              'platform': r.platform,
              'status': r.status,
              'priority': r.priority,
              'tags': jsonDecode(r.tags),
              'notes': r.notes,
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      case 'brain_dumps':
        final rows = await (_db.select(_db.brainDumps)..where((t) => t.dirty.equals(true))).get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'content': r.content,
              'is_pinned': r.isPinned,
              'is_archived': r.isArchived,
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      case 'notes':
        final rows = await (_db.select(_db.notes)..where((t) => t.dirty.equals(true))).get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'title': r.title,
              'content': r.content,
              'category': r.category,
              'tags': jsonDecode(r.tags),
              'is_pinned': r.isPinned,
              'is_favorite': r.isFavorite,
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      case 'savings_goals':
        final rows = await (_db.select(_db.savingsGoals)..where((t) => t.dirty.equals(true))).get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'name': r.name,
              'target_amount': r.targetAmount,
              'current_amount': r.currentAmount,
              'icon': r.icon,
              'color': r.color,
              'deadline': r.deadline?.toIso8601String(),
              'is_completed': r.isCompleted,
              'notes': r.notes,
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      case 'savings_transactions':
        final rows = await (_db.select(_db.savingsTransactions)
              ..where((t) => t.dirty.equals(true)))
            .get();
        return rows.map((r) => {
              'temp_id': (r.id ?? 0) < 0 ? r.id : null,
              'id': (r.id ?? 0) > 0 ? r.id : null,
              'user_id': userId,
              'goal_id': r.goalId,
              'type': r.type,
              'amount': r.amount,
              'description': r.description,
              'updated_at': r.updatedAt.toIso8601String(),
              'deleted_at': r.deletedAt?.toIso8601String(),
            }).toList();
      default:
        return [];
    }
  }
}

extension SyncWriterHelper on SyncService {
  /// Write server-created ids back: fix the row id + remap children FKs.
  Future<void> applyCreated(Map<String, dynamic> created) async {
    for (final entry in created.entries) {
      final table = entry.key;
      final mappings = (entry.value as List).cast<Map<String, dynamic>>();
      for (final m in mappings) {
        final tempId = m['temp_id'] as int?;
        final serverId = m['id'] as int?;
        if (tempId == null || serverId == null) continue;
        await _mapId(table, tempId, serverId);
        for (final child in SyncService._fkColumns.entries) {
          await _remapFk(child.key, child.value, tempId, serverId);
        }
      }
    }
  }

  Future<void> _mapId(String table, int tempId, int serverId) async {
    switch (table) {
      case 'clients':
        await (_db.update(_db.clients)..where((t) => t.id.equals(tempId)))
            .write(db.ClientsCompanion(id: Value(serverId)));
      case 'projects':
        await (_db.update(_db.projects)..where((t) => t.id.equals(tempId)))
            .write(db.ProjectsCompanion(id: Value(serverId)));
      case 'invoices':
        await (_db.update(_db.invoices)..where((t) => t.id.equals(tempId)))
            .write(db.InvoicesCompanion(id: Value(serverId)));
      case 'invoice_payments':
        await (_db.update(_db.invoicePayments)..where((t) => t.id.equals(tempId)))
            .write(db.InvoicePaymentsCompanion(id: Value(serverId)));
      case 'app_ideas':
        await (_db.update(_db.appIdeas)..where((t) => t.id.equals(tempId)))
            .write(db.AppIdeasCompanion(id: Value(serverId)));
      case 'brain_dumps':
        await (_db.update(_db.brainDumps)..where((t) => t.id.equals(tempId)))
            .write(db.BrainDumpsCompanion(id: Value(serverId)));
      case 'notes':
        await (_db.update(_db.notes)..where((t) => t.id.equals(tempId)))
            .write(db.NotesCompanion(id: Value(serverId)));
      case 'savings_goals':
        await (_db.update(_db.savingsGoals)..where((t) => t.id.equals(tempId)))
            .write(db.SavingsGoalsCompanion(id: Value(serverId)));
      case 'savings_transactions':
        await (_db.update(_db.savingsTransactions)
                  ..where((t) => t.id.equals(tempId)))
            .write(db.SavingsTransactionsCompanion(id: Value(serverId)));
    }
  }

  Future<void> _remapFk(String childTable, String fk, int tempId, int serverId) async {
    switch (childTable) {
      case 'projects':
        await (_db.update(_db.projects)..where((t) => t.clientId.equals(tempId)))
            .write(db.ProjectsCompanion(clientId: Value(serverId)));
      case 'invoices':
        await (_db.update(_db.invoices)..where((t) => t.clientId.equals(tempId)))
            .write(db.InvoicesCompanion(clientId: Value(serverId)));
      case 'invoice_payments':
        await (_db.update(_db.invoicePayments)..where((t) => t.invoiceId.equals(tempId)))
            .write(db.InvoicePaymentsCompanion(invoiceId: Value(serverId)));
      case 'savings_transactions':
        await (_db.update(_db.savingsTransactions)..where((t) => t.goalId.equals(tempId)))
            .write(db.SavingsTransactionsCompanion(goalId: Value(serverId)));
    }
  }

  Future<void> clearAllDirty() async {
    for (final table in SyncService._order) {
      switch (table) {
        case 'clients':
          await (_db.update(_db.clients)..where((t) => t.dirty.equals(true)))
              .write(db.ClientsCompanion(dirty: const Value(false)));
        case 'projects':
          await (_db.update(_db.projects)..where((t) => t.dirty.equals(true)))
              .write(db.ProjectsCompanion(dirty: const Value(false)));
        case 'invoices':
          await (_db.update(_db.invoices)..where((t) => t.dirty.equals(true)))
              .write(db.InvoicesCompanion(dirty: const Value(false)));
        case 'invoice_payments':
          await (_db.update(_db.invoicePayments)..where((t) => t.dirty.equals(true)))
              .write(db.InvoicePaymentsCompanion(dirty: const Value(false)));
        case 'app_ideas':
          await (_db.update(_db.appIdeas)..where((t) => t.dirty.equals(true)))
              .write(db.AppIdeasCompanion(dirty: const Value(false)));
        case 'brain_dumps':
          await (_db.update(_db.brainDumps)..where((t) => t.dirty.equals(true)))
              .write(db.BrainDumpsCompanion(dirty: const Value(false)));
        case 'notes':
          await (_db.update(_db.notes)..where((t) => t.dirty.equals(true)))
              .write(db.NotesCompanion(dirty: const Value(false)));
        case 'savings_goals':
          await (_db.update(_db.savingsGoals)..where((t) => t.dirty.equals(true)))
              .write(db.SavingsGoalsCompanion(dirty: const Value(false)));
        case 'savings_transactions':
          await (_db.update(_db.savingsTransactions)
                    ..where((t) => t.dirty.equals(true)))
              .write(db.SavingsTransactionsCompanion(dirty: const Value(false)));
      }
    }
  }
}

extension SyncReaderHelper on SyncService {
  /// Apply remote rows with LWW by updated_at (dirty=false on apply).
  Future<void> applyPull(String table, List<Map<String, dynamic>> rows) async {
    switch (table) {
      case 'clients':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.clients)..where((t) => t.id.equals(id)))
              .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          if (local != null) {
            await (_db.update(_db.clients)..where((t) => t.id.equals(id))).write(
              db.ClientsCompanion(
                name: Value(row['name'] as String? ?? ''),
                email: Value(row['email'] as String?),
                phone: Value(row['phone'] as String?),
                company: Value(row['company'] as String?),
                notes: Value(row['notes'] as String?),
                status: Value(row['status'] as String? ?? 'active'),
                updatedAt: Value(incoming),
                deletedAt: Value(deletedAt),
                dirty: const Value(false),
              ),
            );
          } else {
            await _db.into(_db.clients).insert(db.ClientsCompanion(
                  id: Value(id),
                  name: Value(row['name'] as String? ?? ''),
                  email: Value(row['email'] as String?),
                  phone: Value(row['phone'] as String?),
                  company: Value(row['company'] as String?),
                  notes: Value(row['notes'] as String?),
                  status: Value(row['status'] as String? ?? 'active'),
                  createdAt: Value(createdAt),
                  updatedAt: Value(incoming),
                  deletedAt: Value(deletedAt),
                  dirty: const Value(false),
                ));
          }
        }
      case 'projects':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.projects)..where((t) => t.id.equals(id)))
              .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          final c = db.ProjectsCompanion(
            clientId: Value(row['client_id'] as int?),
            name: Value(row['name'] as String? ?? ''),
            description: Value(row['description'] as String?),
            category: Value(row['category'] as String?),
            status: Value(row['status'] as String? ?? 'development'),
            platform: Value(row['platform'] as String?),
            techStack: Value(jsonEncode(row['tech_stack'] as List? ?? [])),
            progress: Value(row['progress'] as int? ?? 0),
            deadline: Value(_dateOf(row['deadline'])),
            updatedAt: Value(incoming),
            deletedAt: Value(deletedAt),
            dirty: const Value(false),
          );
          if (local != null) {
            await (_db.update(_db.projects)..where((t) => t.id.equals(id))).write(c);
          } else {
            await _db.into(_db.projects).insert(c.copyWith(createdAt: Value(createdAt)));
          }
        }
      case 'invoices':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.invoices)..where((t) => t.id.equals(id)))
              .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          final c = db.InvoicesCompanion(
            clientId: Value(row['client_id'] as int?),
            invoiceNumber: Value(row['invoice_number'] as String? ?? ''),
            items: Value(jsonEncode(row['items'] ?? [])),
            subtotal: Value((row['subtotal'] as num?)?.toDouble() ?? 0),
            taxPercent: Value((row['tax_percent'] as num?)?.toDouble() ?? 0),
            taxAmount: Value((row['tax_amount'] as num?)?.toDouble() ?? 0),
            discountPercent: Value((row['discount_percent'] as num?)?.toDouble() ?? 0),
            discountAmount: Value((row['discount_amount'] as num?)?.toDouble() ?? 0),
            total: Value((row['total'] as num?)?.toDouble() ?? 0),
            paidAmount: Value((row['paid_amount'] as num?)?.toDouble() ?? 0),
            status: Value(row['status'] as String? ?? 'hutang'),
            dueDate: Value(_dateOf(row['due_date'])),
            paidAt: Value(_dateOf(row['paid_at'])),
            notes: Value(row['notes'] as String?),
            updatedAt: Value(incoming),
            deletedAt: Value(deletedAt),
            dirty: const Value(false),
          );
          if (local != null) {
            await (_db.update(_db.invoices)..where((t) => t.id.equals(id))).write(c);
          } else {
            await _db.into(_db.invoices).insert(c.copyWith(createdAt: Value(createdAt)));
          }
        }
      case 'invoice_payments':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.invoicePayments)..where((t) => t.id.equals(id)))
              .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          final c = db.InvoicePaymentsCompanion(
            invoiceId: Value(row['invoice_id'] as int?),
            amount: Value((row['amount'] as num?)?.toDouble() ?? 0),
            remainingAfter: Value((row['remaining_after'] as num?)?.toDouble()),
            notes: Value(row['notes'] as String?),
            updatedAt: Value(incoming),
            deletedAt: Value(deletedAt),
            dirty: const Value(false),
          );
          if (local != null) {
            await (_db.update(_db.invoicePayments)..where((t) => t.id.equals(id))).write(c);
          } else {
            await _db.into(_db.invoicePayments).insert(c.copyWith(createdAt: Value(createdAt)));
          }
        }
      case 'app_ideas':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.appIdeas)..where((t) => t.id.equals(id)))
              .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          final c = db.AppIdeasCompanion(
            name: Value(row['name'] as String? ?? ''),
            tagline: Value(row['tagline'] as String?),
            description: Value(row['description'] as String?),
            features: Value(jsonEncode(row['features'] ?? [])),
            techStack: Value(jsonEncode(row['tech_stack'] ?? [])),
            platform: Value(row['platform'] as String? ?? 'mobile'),
            status: Value(row['status'] as String? ?? 'idea'),
            priority: Value(row['priority'] as String? ?? 'medium'),
            tags: Value(jsonEncode(row['tags'] ?? [])),
            notes: Value(row['notes'] as String?),
            updatedAt: Value(incoming),
            deletedAt: Value(deletedAt),
            dirty: const Value(false),
          );
          if (local != null) {
            await (_db.update(_db.appIdeas)..where((t) => t.id.equals(id))).write(c);
          } else {
            await _db.into(_db.appIdeas).insert(c.copyWith(createdAt: Value(createdAt)));
          }
        }
      case 'brain_dumps':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.brainDumps)..where((t) => t.id.equals(id)))
              .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          final c = db.BrainDumpsCompanion(
            content: Value(row['content'] as String? ?? ''),
            isPinned: Value(row['is_pinned'] as bool? ?? false),
            isArchived: Value(row['is_archived'] as bool? ?? false),
            updatedAt: Value(incoming),
            deletedAt: Value(deletedAt),
            dirty: const Value(false),
          );
          if (local != null) {
            await (_db.update(_db.brainDumps)..where((t) => t.id.equals(id))).write(c);
          } else {
            await _db.into(_db.brainDumps).insert(c.copyWith(createdAt: Value(createdAt)));
          }
        }
      case 'notes':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.notes)..where((t) => t.id.equals(id)))
              .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          final c = db.NotesCompanion(
            title: Value(row['title'] as String? ?? ''),
            content: Value(row['content'] as String? ?? ''),
            category: Value(row['category'] as String?),
            tags: Value(jsonEncode(row['tags'] ?? [])),
            isPinned: Value(row['is_pinned'] as bool? ?? false),
            isFavorite: Value(row['is_favorite'] as bool? ?? false),
            updatedAt: Value(incoming),
            deletedAt: Value(deletedAt),
            dirty: const Value(false),
          );
          if (local != null) {
            await (_db.update(_db.notes)..where((t) => t.id.equals(id))).write(c);
          } else {
            await _db.into(_db.notes).insert(c.copyWith(createdAt: Value(createdAt)));
          }
        }
      case 'savings_goals':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.savingsGoals)..where((t) => t.id.equals(id)))
              .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          final c = db.SavingsGoalsCompanion(
            name: Value(row['name'] as String? ?? ''),
            targetAmount: Value((row['target_amount'] as num?)?.toDouble() ?? 0),
            currentAmount: Value((row['current_amount'] as num?)?.toDouble() ?? 0),
            icon: Value(row['icon'] as String?),
            color: Value(row['color'] as String?),
            deadline: Value(_dateOf(row['deadline'])),
            isCompleted: Value(row['is_completed'] as bool? ?? false),
            notes: Value(row['notes'] as String?),
            updatedAt: Value(incoming),
            deletedAt: Value(deletedAt),
            dirty: const Value(false),
          );
          if (local != null) {
            await (_db.update(_db.savingsGoals)..where((t) => t.id.equals(id))).write(c);
          } else {
            await _db.into(_db.savingsGoals).insert(c.copyWith(createdAt: Value(createdAt)));
          }
        }
      case 'savings_transactions':
        for (final row in rows) {
          final id = row['id'] as int;
          final incoming = DateTime.parse(row['updated_at'] as String);
          final local = await (_db.select(_db.savingsTransactions)
                    ..where((t) => t.id.equals(id)))
                .getSingleOrNull();
          if (local != null && local.updatedAt.isAfter(incoming)) continue;
          final createdAt = _dateOf(row['created_at']) ?? incoming;
          final deletedAt = _dateOf(row['deleted_at']);
          final c = db.SavingsTransactionsCompanion(
            goalId: Value(row['goal_id'] as int?),
            type: Value(row['type'] as String? ?? 'deposit'),
            amount: Value((row['amount'] as num?)?.toDouble() ?? 0),
            description: Value(row['description'] as String?),
            updatedAt: Value(incoming),
            deletedAt: Value(deletedAt),
            dirty: const Value(false),
          );
          if (local != null) {
            await (_db.update(_db.savingsTransactions)
                      ..where((t) => t.id.equals(id)))
                .write(c);
          } else {
            await _db.into(_db.savingsTransactions)
                .insert(c.copyWith(createdAt: Value(createdAt)));
          }
        }
    }
  }

  DateTime? _dateOf(dynamic v) {
    if (v == null) return null;
    final s = v.toString();
    if (s.isEmpty) return null;
    return DateTime.tryParse(s);
  }
}
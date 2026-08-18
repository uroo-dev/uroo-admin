import 'package:drift/drift.dart';

import '../db/app_database.dart' as db;
import '../models/brain_dump.dart';

class BrainDumpRepository {
  final db.AppDatabase _db = db.AppDatabase.instance;

  Future<List<BrainDump>> list({bool includeArchived = false}) async {
    final query = _db.select(_db.brainDumps)
      ..where((t) => t.deletedAt.isNull())
      ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]);
    if (!includeArchived) {
      query.where((t) => t.isArchived.equals(false));
    }
    final rows = await query.get();

    return rows.map(_toModel).toList();
  }

  Future<BrainDump> create(BrainDump dump) async {
    final now = DateTime.now();
    final id = await _db.nextTempId();

    await _db.into(_db.brainDumps).insert(db.BrainDumpsCompanion.insert(
          id: Value(id),
          content: dump.content,
          isPinned: Value(dump.isPinned),
          isArchived: Value(dump.isArchived),
          createdAt: now,
          updatedAt: now,
          dirty: const Value(true),
        ));

    return _toModel(db.BrainDump(
      localId: 0,
      id: id,
      content: dump.content,
      isPinned: dump.isPinned,
      isArchived: dump.isArchived,
      createdAt: now,
      updatedAt: now,
      deletedAt: null,
      dirty: true,
    ));
  }

  Future<void> update(int id, BrainDump dump) async {
    await _touch(id, db.BrainDumpsCompanion(content: Value(dump.content)));
  }

  Future<void> delete(int id) async {
    final now = DateTime.now();

    await (_db.update(_db.brainDumps)..where((t) => t.id.equals(id))).write(
      db.BrainDumpsCompanion(
        deletedAt: Value(now),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<void> togglePin(int id, bool value) async {
    await _touch(id, db.BrainDumpsCompanion(isPinned: Value(value)));
  }

  Future<void> archive(int id, bool value) async {
    await _touch(id, db.BrainDumpsCompanion(isArchived: Value(value)));
  }

  Future<void> _touch(int id, db.BrainDumpsCompanion fields) async {
    await (_db.update(_db.brainDumps)..where((t) => t.id.equals(id))).write(
      fields.copyWith(
        updatedAt: Value(DateTime.now()),
        dirty: const Value(true),
      ),
    );
  }

  BrainDump _toModel(db.BrainDump r) => BrainDump(
        id: r.id,
        content: r.content,
        isPinned: r.isPinned,
        isArchived: r.isArchived,
        createdAt: r.createdAt,
      );
}
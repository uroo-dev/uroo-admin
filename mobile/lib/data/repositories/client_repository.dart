import 'package:drift/drift.dart';

import '../db/app_database.dart' as db;
import '../models/client.dart';

class ClientRepository {
  final db.AppDatabase _db = db.AppDatabase.instance;

  Future<List<Client>> list() async {
    final rows = await (_db.select(_db.clients)
          ..where((t) => t.deletedAt.isNull())
          ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]))
        .get();

    return rows.map(_toModel).toList();
  }

  Future<Client> create(Client client) async {
    final now = DateTime.now();
    final id = await _db.nextTempId();

    await _db.into(_db.clients).insert(db.ClientsCompanion.insert(
          id: Value(id),
          name: client.name,
          email: Value(client.email),
          phone: Value(client.phone),
          company: Value(client.company),
          notes: Value(client.notes),
          status: Value(client.status),
          createdAt: now,
          updatedAt: now,
          dirty: const Value(true),
        ));

    return _toModel(db.Client(
      localId: 0,
      id: id,
      name: client.name,
      email: client.email,
      phone: client.phone,
      company: client.company,
      notes: client.notes,
      status: client.status,
      createdAt: now,
      updatedAt: now,
      deletedAt: null,
      dirty: true,
    ));
  }

  Future<void> update(int id, Client client) async {
    final now = DateTime.now();

    await (_db.update(_db.clients)..where((t) => t.id.equals(id))).write(
      db.ClientsCompanion(
        name: Value(client.name),
        email: Value(client.email),
        phone: Value(client.phone),
        company: Value(client.company),
        notes: Value(client.notes),
        status: Value(client.status),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<void> delete(int id) async {
    final now = DateTime.now();

    await (_db.update(_db.clients)..where((t) => t.id.equals(id))).write(
      db.ClientsCompanion(
        deletedAt: Value(now),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Client _toModel(db.Client r) => Client(
        id: r.id,
        name: r.name,
        email: r.email,
        phone: r.phone,
        company: r.company,
        notes: r.notes,
        status: r.status,
        createdAt: r.createdAt,
      );
}
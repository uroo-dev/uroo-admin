import 'package:drift/drift.dart';
import 'package:drift_flutter/drift_flutter.dart';

part 'app_database.g.dart';

/// Server id (positif) atau temp id (negatif, belum tersinkron).
mixin ServerIdColumn on Table {
  IntColumn get id => integer().nullable().unique()();
}

/// Timestamp + status sinkronisasi untuk setiap baris lokal.
mixin SyncColumns on Table {
  DateTimeColumn get createdAt => dateTime()();
  DateTimeColumn get updatedAt => dateTime()();
  DateTimeColumn get deletedAt => dateTime().nullable()();
  BoolColumn get dirty => boolean().withDefault(const Constant(false))();
}

class Clients extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  TextColumn get name => text()();
  TextColumn get email => text().nullable()();
  TextColumn get phone => text().nullable()();
  TextColumn get company => text().nullable()();
  TextColumn get notes => text().nullable()();
  TextColumn get status => text().withDefault(const Constant('active'))();

  @override
  Set<Column> get primaryKey => {localId};
}

class Projects extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  IntColumn get clientId => integer().nullable()();
  TextColumn get name => text()();
  TextColumn get description => text().nullable()();
  TextColumn get category => text().nullable()();
  TextColumn get status => text().withDefault(const Constant('development'))();
  TextColumn get platform => text().nullable()();
  TextColumn get techStack => text().nullable()();
  IntColumn get progress => integer().withDefault(const Constant(0))();
  DateTimeColumn get deadline => dateTime().nullable()();

  @override
  Set<Column> get primaryKey => {localId};
}

class Invoices extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  IntColumn get clientId => integer().nullable()();
  TextColumn get invoiceNumber => text()();
  TextColumn get items => text().withDefault(const Constant('[]'))();
  RealColumn get subtotal => real().withDefault(const Constant(0))();
  RealColumn get taxPercent => real().withDefault(const Constant(0))();
  RealColumn get taxAmount => real().withDefault(const Constant(0))();
  RealColumn get discountPercent => real().withDefault(const Constant(0))();
  RealColumn get discountAmount => real().withDefault(const Constant(0))();
  RealColumn get total => real().withDefault(const Constant(0))();
  RealColumn get paidAmount => real().withDefault(const Constant(0))();
  TextColumn get status => text().withDefault(const Constant('hutang'))();
  DateTimeColumn get dueDate => dateTime().nullable()();
  DateTimeColumn get paidAt => dateTime().nullable()();
  TextColumn get notes => text().nullable()();

  @override
  Set<Column> get primaryKey => {localId};
}

class InvoicePayments extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  IntColumn get invoiceId => integer().nullable()();
  RealColumn get amount => real().withDefault(const Constant(0))();
  RealColumn get remainingAfter => real().nullable()();
  TextColumn get notes => text().nullable()();

  @override
  Set<Column> get primaryKey => {localId};
}

class AppIdeas extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  TextColumn get name => text()();
  TextColumn get tagline => text().nullable()();
  TextColumn get description => text().nullable()();
  TextColumn get features => text().withDefault(const Constant('[]'))();
  TextColumn get techStack => text().withDefault(const Constant('[]'))();
  TextColumn get platform => text().withDefault(const Constant('mobile'))();
  TextColumn get status => text().withDefault(const Constant('idea'))();
  TextColumn get priority => text().withDefault(const Constant('medium'))();
  TextColumn get tags => text().withDefault(const Constant('[]'))();
  TextColumn get notes => text().nullable()();

  @override
  Set<Column> get primaryKey => {localId};
}

class BrainDumps extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  TextColumn get content => text()();
  BoolColumn get isPinned => boolean().withDefault(const Constant(false))();
  BoolColumn get isArchived => boolean().withDefault(const Constant(false))();

  @override
  Set<Column> get primaryKey => {localId};
}

class Notes extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  TextColumn get title => text()();
  TextColumn get content => text()();
  TextColumn get category => text().nullable()();
  TextColumn get tags => text().withDefault(const Constant('[]'))();
  BoolColumn get isPinned => boolean().withDefault(const Constant(false))();
  BoolColumn get isFavorite => boolean().withDefault(const Constant(false))();

  @override
  Set<Column> get primaryKey => {localId};
}

class SavingsGoals extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  TextColumn get name => text()();
  RealColumn get targetAmount => real().withDefault(const Constant(0))();
  RealColumn get currentAmount => real().withDefault(const Constant(0))();
  TextColumn get icon => text().nullable()();
  TextColumn get color => text().nullable()();
  DateTimeColumn get deadline => dateTime().nullable()();
  BoolColumn get isCompleted => boolean().withDefault(const Constant(false))();
  TextColumn get notes => text().nullable()();

  @override
  Set<Column> get primaryKey => {localId};
}

class SavingsTransactions extends Table with ServerIdColumn, SyncColumns {
  IntColumn get localId => integer().autoIncrement()();
  IntColumn get goalId => integer().nullable()();
  TextColumn get type => text()();
  RealColumn get amount => real().withDefault(const Constant(0))();
  TextColumn get description => text().nullable()();

  @override
  Set<Column> get primaryKey => {localId};
}

/// Key-value store: server url, token, user, watermark, temp id counter.
class SyncMeta extends Table {
  TextColumn get key => text()();
  TextColumn get value => text().nullable()();

  @override
  Set<Column> get primaryKey => {key};
}

@DriftDatabase(tables: [
  Clients,
  Projects,
  Invoices,
  InvoicePayments,
  AppIdeas,
  BrainDumps,
  Notes,
  SavingsGoals,
  SavingsTransactions,
  SyncMeta,
])
class AppDatabase extends _$AppDatabase {
  AppDatabase._() : super(driftDatabase(name: 'uroo_admin'));

  static final AppDatabase instance = AppDatabase._();

  @override
  int get schemaVersion => 1;

  /// Baca metadata key-value (server url, token, watermark, ...).
  Future<String?> meta(String key) async {
    final row = await (select(syncMeta)..where((t) => t.key.equals(key)))
        .getSingleOrNull();

    return row?.value;
  }

  Future<void> setMeta(String key, String? value) async {
    await (into(syncMeta)).insertOnConflictUpdate(
      SyncMetaCompanion.insert(key: key, value: Value(value)),
    );
  }

  /// Temp id negatif berikutnya untuk baris yang dibuat offline.
  Future<int> nextTempId() async {
    final current = int.parse(await meta('temp_counter') ?? '-1');

    await setMeta('temp_counter', '${current - 1}');

    return current;
  }
}
import 'dart:convert';

import 'package:drift/drift.dart';

import '../db/app_database.dart' as db;
import '../models/jsonc.dart';
import '../models/note.dart';

class NoteRepository {
  final db.AppDatabase _db = db.AppDatabase.instance;

  Future<List<Note>> list({bool pinnedOnly = false}) async {
    final query = _db.select(_db.notes)
      ..where((t) => t.deletedAt.isNull())
      ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]);
    if (pinnedOnly) {
      query.where((t) => t.isPinned.equals(true));
    }
    final rows = await query.get();

    return rows.map(_toModel).toList();
  }

  Future<Note> create(Note note) async {
    final now = DateTime.now();
    final id = await _db.nextTempId();

    await _db.into(_db.notes).insert(db.NotesCompanion.insert(
          id: Value(id),
          title: note.title,
          content: note.content,
          category: Value(note.category),
          tags: Value(jsonEncode(note.tags)),
          isPinned: Value(note.isPinned),
          isFavorite: Value(note.isFavorite),
          createdAt: now,
          updatedAt: now,
          dirty: const Value(true),
        ));

    return _toModel(db.Note(
      localId: 0,
      id: id,
      title: note.title,
      content: note.content,
      category: note.category,
      tags: jsonEncode(note.tags),
      isPinned: note.isPinned,
      isFavorite: note.isFavorite,
      createdAt: now,
      updatedAt: now,
      deletedAt: null,
      dirty: true,
    ));
  }

  Future<void> update(int id, Note note) async {
    final now = DateTime.now();

    await (_db.update(_db.notes)..where((t) => t.id.equals(id))).write(
      db.NotesCompanion(
        title: Value(note.title),
        content: Value(note.content),
        category: Value(note.category),
        tags: Value(jsonEncode(note.tags)),
        isPinned: Value(note.isPinned),
        isFavorite: Value(note.isFavorite),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<void> delete(int id) async {
    final now = DateTime.now();

    await (_db.update(_db.notes)..where((t) => t.id.equals(id))).write(
      db.NotesCompanion(
        deletedAt: Value(now),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<void> togglePin(int id, bool value) async {
    await _touch(id, db.NotesCompanion(isPinned: Value(value)));
  }

  Future<void> toggleFavorite(int id, bool value) async {
    await _touch(id, db.NotesCompanion(isFavorite: Value(value)));
  }

  Future<void> _touch(int id, db.NotesCompanion fields) async {
    await (_db.update(_db.notes)..where((t) => t.id.equals(id))).write(
      fields.copyWith(
        updatedAt: Value(DateTime.now()),
        dirty: const Value(true),
      ),
    );
  }

  Note _toModel(db.Note r) => Note(
        id: r.id,
        title: r.title,
        content: r.content,
        category: r.category,
        tags: Jsonc.toStringList(jsonDecode(r.tags)),
        isPinned: r.isPinned,
        isFavorite: r.isFavorite,
        createdAt: r.createdAt,
        updatedAt: r.updatedAt,
      );
}
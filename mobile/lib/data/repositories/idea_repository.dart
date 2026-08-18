import 'dart:convert';

import 'package:drift/drift.dart';

import '../db/app_database.dart' as db;
import '../models/app_idea.dart';
import '../models/jsonc.dart';

class IdeaRepository {
  final db.AppDatabase _db = db.AppDatabase.instance;

  Future<List<AppIdea>> list() async {
    final rows = await (_db.select(_db.appIdeas)
          ..where((t) => t.deletedAt.isNull())
          ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]))
        .get();

    return rows.map(_toModel).toList();
  }

  Future<AppIdea> create(AppIdea idea) async {
    final now = DateTime.now();
    final id = await _db.nextTempId();

    await _db.into(_db.appIdeas).insert(db.AppIdeasCompanion.insert(
          id: Value(id),
          name: idea.name,
          tagline: Value(idea.tagline),
          description: Value(idea.description),
          features: Value(jsonEncode(idea.features)),
          techStack: Value(jsonEncode(idea.techStack)),
          platform: Value(idea.platform),
          status: Value(idea.status),
          priority: Value(idea.priority),
          tags: Value(jsonEncode(idea.tags)),
          notes: Value(idea.notes),
          createdAt: now,
          updatedAt: now,
          dirty: const Value(true),
        ));

    return _toModel(db.AppIdea(
      localId: 0,
      id: id,
      name: idea.name,
      tagline: idea.tagline,
      description: idea.description,
      features: jsonEncode(idea.features),
      techStack: jsonEncode(idea.techStack),
      platform: idea.platform,
      status: idea.status,
      priority: idea.priority,
      tags: jsonEncode(idea.tags),
      notes: idea.notes,
      createdAt: now,
      updatedAt: now,
      deletedAt: null,
      dirty: true,
    ));
  }

  Future<void> update(int id, AppIdea idea) async {
    final now = DateTime.now();

    await (_db.update(_db.appIdeas)..where((t) => t.id.equals(id))).write(
      db.AppIdeasCompanion(
        name: Value(idea.name),
        tagline: Value(idea.tagline),
        description: Value(idea.description),
        features: Value(jsonEncode(idea.features)),
        techStack: Value(jsonEncode(idea.techStack)),
        platform: Value(idea.platform),
        status: Value(idea.status),
        priority: Value(idea.priority),
        tags: Value(jsonEncode(idea.tags)),
        notes: Value(idea.notes),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<void> delete(int id) async {
    final now = DateTime.now();

    await (_db.update(_db.appIdeas)..where((t) => t.id.equals(id))).write(
      db.AppIdeasCompanion(
        deletedAt: Value(now),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  AppIdea _toModel(db.AppIdea r) => AppIdea(
        id: r.id,
        name: r.name,
        tagline: r.tagline,
        description: r.description,
        features: Jsonc.toStringList(jsonDecode(r.features)),
        techStack: Jsonc.toStringList(jsonDecode(r.techStack)),
        platform: r.platform,
        status: r.status,
        priority: r.priority,
        tags: Jsonc.toStringList(jsonDecode(r.tags)),
        notes: r.notes,
        createdAt: r.createdAt,
      );
}
import 'dart:convert';

import 'package:drift/drift.dart';

import '../db/app_database.dart' as db;
import '../models/jsonc.dart';
import '../models/project.dart';

class ProjectRepository {
  final db.AppDatabase _db = db.AppDatabase.instance;

  Future<List<Project>> list() async {
    final rows = await (_db.select(_db.projects)
          ..where((t) => t.deletedAt.isNull())
          ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]))
        .get();

    return Future.wait(rows.map(_toModel));
  }

  Future<Project> create(Project project) async {
    final now = DateTime.now();
    final id = await _db.nextTempId();

    await _db.into(_db.projects).insert(db.ProjectsCompanion.insert(
          id: Value(id),
          clientId: Value(project.clientId),
          name: project.name,
          description: Value(project.description),
          category: Value(project.category),
          status: Value(project.status),
          platform: Value(project.platform),
          techStack: Value(jsonEncode(project.techStack)),
          progress: Value(project.progress),
          deadline: Value(project.deadline),
          createdAt: now,
          updatedAt: now,
          dirty: const Value(true),
        ));

    return Project(
      id: id,
      clientId: project.clientId,
      name: project.name,
      description: project.description,
      category: project.category,
      status: project.status,
      progress: project.progress,
      platform: project.platform,
      techStack: project.techStack,
      clientName: await _clientName(project.clientId),
      deadline: project.deadline,
      createdAt: now,
    );
  }

  Future<void> update(int id, Project project) async {
    final now = DateTime.now();

    await (_db.update(_db.projects)..where((t) => t.id.equals(id))).write(
      db.ProjectsCompanion(
        clientId: Value(project.clientId),
        name: Value(project.name),
        description: Value(project.description),
        category: Value(project.category),
        status: Value(project.status),
        platform: Value(project.platform),
        techStack: Value(jsonEncode(project.techStack)),
        progress: Value(project.progress),
        deadline: Value(project.deadline),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<void> delete(int id) async {
    final now = DateTime.now();

    await (_db.update(_db.projects)..where((t) => t.id.equals(id))).write(
      db.ProjectsCompanion(
        deletedAt: Value(now),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<Project> _toModel(db.Project r) async => Project(
        id: r.id,
        clientId: r.clientId,
        name: r.name,
        description: r.description,
        category: r.category,
        status: r.status,
        progress: r.progress,
        platform: r.platform,
        techStack: Jsonc.toStringList(jsonDecode(r.techStack ?? '[]')),
        clientName: await _clientName(r.clientId),
        deadline: r.deadline,
        createdAt: r.createdAt,
      );

  Future<String?> _clientName(int? clientId) async {
    if (clientId == null) return null;
    final client = await (_db.select(_db.clients)
          ..where((t) => t.id.equals(clientId)))
        .getSingleOrNull();

    return client?.name;
  }
}
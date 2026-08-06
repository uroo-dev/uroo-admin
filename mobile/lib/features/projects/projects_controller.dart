import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/project.dart';
import '../../data/repositories/project_repository.dart';

class ProjectsController extends AsyncNotifier<List<Project>> {
  final _repo = ProjectRepository();

  @override
  Future<List<Project>> build() => _repo.list();

  Future<void> create(Project project) async {
    await _repo.create(project);
    ref.invalidateSelf();
  }

  Future<void> update(int id, Project project) async {
    await _repo.update(id, project);
    ref.invalidateSelf();
  }

  Future<void> delete(int id) async {
    await _repo.delete(id);
    ref.invalidateSelf();
  }
}

final projectsProvider =
    AsyncNotifierProvider<ProjectsController, List<Project>>(ProjectsController.new);
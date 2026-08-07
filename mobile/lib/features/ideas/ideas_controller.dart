import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/app_idea.dart';
import '../../data/repositories/idea_repository.dart';

class IdeasController extends AsyncNotifier<List<AppIdea>> {
  final _repo = IdeaRepository();

  @override
  Future<List<AppIdea>> build() => _repo.list();

  Future<void> create(AppIdea idea) async {
    await _repo.create(idea);
    ref.invalidateSelf();
  }

  Future<void> updateItem(int id, AppIdea idea) async {
    await _repo.update(id, idea);
    ref.invalidateSelf();
  }

  Future<void> delete(int id) async {
    await _repo.delete(id);
    ref.invalidateSelf();
  }
}

final ideasProvider =
    AsyncNotifierProvider<IdeasController, List<AppIdea>>(IdeasController.new);
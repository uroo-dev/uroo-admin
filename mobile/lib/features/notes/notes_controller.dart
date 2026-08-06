import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/note.dart';
import '../../data/repositories/note_repository.dart';

class NotesController extends AsyncNotifier<List<Note>> {
  final _repo = NoteRepository();

  @override
  Future<List<Note>> build() => _repo.list();

  Future<void> create(Note note) async {
    await _repo.create(note);
    ref.invalidateSelf();
  }

  Future<void> update(int id, Note note) async {
    await _repo.update(id, note);
    ref.invalidateSelf();
  }

  Future<void> delete(int id) async {
    await _repo.delete(id);
    ref.invalidateSelf();
  }

  Future<void> togglePin(int id, bool value) async {
    await _repo.togglePin(id, value);
    ref.invalidateSelf();
  }

  Future<void> toggleFavorite(int id, bool value) async {
    await _repo.toggleFavorite(id, value);
    ref.invalidateSelf();
  }
}

final notesProvider =
    AsyncNotifierProvider<NotesController, List<Note>>(NotesController.new);
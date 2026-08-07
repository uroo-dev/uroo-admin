import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/brain_dump.dart';
import '../../data/repositories/brain_dump_repository.dart';

class BrainDumpsController extends AsyncNotifier<List<BrainDump>> {
  final _repo = BrainDumpRepository();

  @override
  Future<List<BrainDump>> build() => _repo.list();

  Future<void> create(BrainDump dump) async {
    await _repo.create(dump);
    ref.invalidateSelf();
  }

  Future<void> updateItem(int id, BrainDump dump) async {
    await _repo.update(id, dump);
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

  Future<void> archive(int id, bool value) async {
    await _repo.archive(id, value);
    ref.invalidateSelf();
  }
}

final brainDumpsProvider =
    AsyncNotifierProvider<BrainDumpsController, List<BrainDump>>(
  BrainDumpsController.new,
);
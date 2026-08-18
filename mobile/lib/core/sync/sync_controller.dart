import 'dart:async';

import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/db/app_database.dart';
import '../session/session_store.dart';
import 'sync_service.dart';

/// Menjalankan sinkronisasi manual + otomatis (tiap 30 detik saat ada sesi).
class SyncController extends StateNotifier<AsyncValue<SyncResult?>> {
  SyncController(this._ref) : super(const AsyncValue.data(null)) {
    if (currentSession.value != null) _schedule();
  }

  final Ref _ref;
  Timer? _timer;
  bool _running = false;

  Future<SyncResult?> syncNow() async {
    final session = currentSession.value;
    if (session == null) return null;
    if (_running) return state.valueOrNull;

    _running = true;
    state = const AsyncValue.loading();
    final result = await SyncService(AppDatabase.instance).sync(session);
    _running = false;
    state = AsyncValue.data(result);
    return result;
  }

  void _schedule() {
    _timer?.cancel();
    _timer = Timer.periodic(const Duration(seconds: 30), (_) => syncNow());
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }
}

final syncProvider =
    StateNotifierProvider<SyncController, AsyncValue<SyncResult?>>(
  (ref) => SyncController(ref),
);
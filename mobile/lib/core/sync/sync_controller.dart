import 'dart:async';

import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/db/app_database.dart';
import '../session/session_store.dart';
import 'sync_service.dart';

/// Fase koneksi server — dipakai badge status di shell/dashboard/settings.
enum ConnectionPhase { notConnected, offline, syncing, online }

/// Status sinkronisasi + koneksi terakhir.
class SyncState {
  final ConnectionPhase phase;
  final SyncResult? result;

  const SyncState({
    this.phase = ConnectionPhase.notConnected,
    this.result,
  });

  bool get syncing => phase == ConnectionPhase.syncing;

  String? get error => result?.error;
}

/// Menjalankan sinkronisasi manual + otomatis (tiap 30 detik saat ada sesi)
/// dan melacak status koneksi server.
class SyncController extends StateNotifier<SyncState> {
  SyncController(Ref ref) : super(const SyncState()) {
    if (currentSession.value != null) _schedule();
  }

  Timer? _timer;
  bool _running = false;

  Future<SyncResult?> syncNow() async {
    final session = currentSession.value;
    if (session == null) {
      // Logout / belum login: hentikan jadwal dan tandai terputus.
      _timer?.cancel();
      _timer = null;
      state = const SyncState();
      return null;
    }
    if (_running) return state.result;

    // Pastikan auto-sync aktif (misal baru saja selesai login).
    if (_timer == null) _schedule();

    _running = true;
    state = state.copyWith(phase: ConnectionPhase.syncing);
    final result = await SyncService(AppDatabase.instance).sync(session);
    _running = false;
    state = SyncState(
      phase: result.error == null ? ConnectionPhase.online : ConnectionPhase.offline,
      result: result,
    );
    return result;
  }

  /// Tes koneksi ke URL ngrok tanpa perlu sesi/login.
  /// Mengembalikan null jika sukses, atau pesan galat.
  Future<String?> testConnection(String serverUrl) async {
    try {
      await SyncService(AppDatabase.instance).ping(serverUrl);
      return null;
    } on SyncException catch (e) {
      return e.message;
    }
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

extension _SyncStateCopy on SyncState {
  SyncState copyWith({ConnectionPhase? phase}) => SyncState(
        phase: phase ?? this.phase,
        result: result,
      );
}

final syncProvider =
    StateNotifierProvider<SyncController, SyncState>(
  (ref) => SyncController(ref),
);

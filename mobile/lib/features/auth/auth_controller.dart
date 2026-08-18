import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/session/session_store.dart';
import '../../core/sync/sync_service.dart';
import '../../data/db/app_database.dart';

class AuthController extends StateNotifier<AsyncValue<void>> {
  AuthController(this._ref) : super(const AsyncValue.data(null));

  final Ref _ref;

  Future<bool> signIn({required String email, required String password}) async {
    state = const AsyncValue.loading();
    try {
      final db = AppDatabase.instance;
      final serverUrl = await db.meta('server_url');
      if (serverUrl == null || serverUrl.isEmpty) {
        throw SyncException('URL server belum diatur. Isi dulu di layar login.');
      }

      final res = await SyncService(db).login(
        serverUrl: serverUrl,
        email: email,
        password: password,
      );

      await _saveSession(
        db: db,
        serverUrl: serverUrl,
        res: res,
        fallbackEmail: email,
      );

      state = const AsyncValue.data(null);
      return true;
    } on SyncException catch (e) {
      state = AsyncValue.error(e.message, StackTrace.current);
      return false;
    } catch (e) {
      state = AsyncValue.error(e.toString(), StackTrace.current);
      return false;
    }
  }

  Future<bool> signUp({
    required String name,
    required String email,
    required String password,
  }) async {
    state = const AsyncValue.loading();
    try {
      final db = AppDatabase.instance;
      final serverUrl = await db.meta('server_url');
      if (serverUrl == null || serverUrl.isEmpty) {
        throw SyncException('URL server belum diatur. Isi dulu di layar login.');
      }

      final res = await SyncService(db).register(
        serverUrl: serverUrl,
        name: name,
        email: email,
        password: password,
      );

      await _saveSession(
        db: db,
        serverUrl: serverUrl,
        res: res,
        fallbackEmail: email,
        fallbackName: name,
      );

      state = const AsyncValue.data(null);
      return true;
    } on SyncException catch (e) {
      state = AsyncValue.error(e.message, StackTrace.current);
      return false;
    } catch (e) {
      state = AsyncValue.error(e.toString(), StackTrace.current);
      return false;
    }
  }

  Future<void> signOut() async {
    await _ref.watch(sessionProvider.notifier).clear();
  }

  Future<void> _saveSession({
    required AppDatabase db,
    required String serverUrl,
    required Map<String, dynamic> res,
    required String fallbackEmail,
    String? fallbackName,
  }) async {
    final user = (res['user'] as Map?)?.cast<String, dynamic>() ?? {};

    await _ref.watch(sessionProvider.notifier).save(Session(
          serverUrl: serverUrl,
          token: res['token'] as String,
          userId: (user['id'] as num).toInt(),
          name: (user['name'] as String?) ?? fallbackName ?? '',
          email: (user['email'] as String?) ?? fallbackEmail,
        ));
  }
}

final authControllerProvider =
    StateNotifierProvider<AuthController, AsyncValue<void>>(
  (ref) => AuthController(ref),
);
import 'package:flutter/foundation.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/db/app_database.dart';

/// Sesi lokal yang dipakai app (hasil login ke API web).
class Session {
  final String serverUrl;
  final String token;
  final int userId;
  final String name;
  final String email;

  const Session({
    required this.serverUrl,
    required this.token,
    required this.userId,
    required this.name,
    required this.email,
  });
}

/// Diberitahukan ke router saat sesi berubah (pengganti Supabase auth stream).
final ValueNotifier<Session?> currentSession = ValueNotifier(null);

const _kServerUrl = 'server_url';
const _kToken = 'auth_token';
const _kUserId = 'user_id';
const _kUserName = 'user_name';
const _kUserEmail = 'user_email';

class SessionController extends StateNotifier<Session?> {
  SessionController(this._db) : super(null) {
    _load();
  }

  final AppDatabase _db;

  Future<void> _load() async {
    final token = await _db.meta(_kToken);
    final serverUrl = await _db.meta(_kServerUrl);

    if (token == null || serverUrl == null) return;

    final userId = int.tryParse(await _db.meta(_kUserId) ?? '');
    if (userId == null) return;

    state = Session(
      serverUrl: serverUrl,
      token: token,
      userId: userId,
      name: await _db.meta(_kUserName) ?? '',
      email: await _db.meta(_kUserEmail) ?? '',
    );
    currentSession.value = state;
  }

  Future<void> save(Session session) async {
    await _db.setMeta(_kServerUrl, session.serverUrl);
    await _db.setMeta(_kToken, session.token);
    await _db.setMeta(_kUserId, '${session.userId}');
    await _db.setMeta(_kUserName, session.name);
    await _db.setMeta(_kUserEmail, session.email);
    state = session;
    currentSession.value = session;
  }

  Future<void> clear() async {
    await _db.setMeta(_kToken, null);
    state = null;
    currentSession.value = null;
  }
}

final databaseProvider = Provider<AppDatabase>((ref) => AppDatabase.instance);

final sessionProvider =
    StateNotifierProvider<SessionController, Session?>(
  (ref) => SessionController(ref.watch(databaseProvider)),
);
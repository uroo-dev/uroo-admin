import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'app.dart';
import 'core/session/session_store.dart';
import 'data/db/app_database.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Pulihkan sesi tersimpan agar router langsung mendarat di halaman utama.
  final db = AppDatabase.instance;
  final token = await db.meta('auth_token');
  final serverUrl = await db.meta('server_url');
  final userId = int.tryParse(await db.meta('user_id') ?? '');
  if (token != null && serverUrl != null && userId != null) {
    currentSession.value = Session(
      serverUrl: serverUrl,
      token: token,
      userId: userId,
      name: await db.meta('user_name') ?? '',
      email: await db.meta('user_email') ?? '',
    );
  }

  runApp(const ProviderScope(child: App()));
}
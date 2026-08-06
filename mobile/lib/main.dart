import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

import 'app.dart';
import 'core/config/app_constants.dart';
import 'core/supabase/supabase_client.dart';
import 'routing/app_router.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();

  await Supabase.initialize(
    url: AppConstants.supabaseUrl,
    anonKey: AppConstants.supabaseAnonKey,
  );

  // Refresh router redirects when auth state changes.
  supabase.auth.onAuthStateChange.listen((_) => authRefresh.value++);

  runApp(const ProviderScope(child: App()));
}
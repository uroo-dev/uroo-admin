import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

import '../config/app_constants.dart';

/// Singleton Supabase client. Initialized once in main().
final supabase = Supabase.instance.client;

/// Simple boolean indicator of whether the user is signed in. The actual
/// auth state is exposed as a stream via [SupabaseAuthState].
final authStateProvider = StreamProvider<AuthState>(
  (ref) => supabase.auth.onAuthStateChange,
);
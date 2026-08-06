/// App-wide constants, including Supabase credentials injected at build time
/// via `--dart-define=SUPABASE_URL=... --dart-define=SUPABASE_ANON_KEY=...`.
class AppConstants {
  AppConstants._();

  static const String appName = 'UROO.Admin';

  /// Supabase project URL (https://xyz.supabase.co)
  static const String supabaseUrl = String.fromEnvironment(
    'SUPABASE_URL',
    defaultValue: 'https://placeholder.supabase.co',
  );

  /// Supabase public anon key (safe to embed, protected by RLS)
  static const String supabaseAnonKey = String.fromEnvironment(
    'SUPABASE_ANON_KEY',
    defaultValue: 'placeholder-anon-key',
  );

  /// Minimal DB connection timeout (seconds).
  static const int networkTimeoutSeconds = 20;
}

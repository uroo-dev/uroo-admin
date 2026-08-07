import 'supabase_client.dart';

/// Helpers for interacting with Supabase (bigint PK, auth-based RLS).
class Supa {
  Supa._();

  static int? _currentUserId;

  /// The current user's integer id from the `users` table (used as `user_id`
  /// FK when inserting rows). Resolved once and cached per session.
  static Future<int> currentUserId() async {
    if (_currentUserId != null) return _currentUserId!;
    final user = supabase.auth.currentUser;
    if (user == null) throw Exception('Not authenticated');

    final res = await supabase
        .from('users')
        .select('id')
        .eq('supabase_uid', user.id)
        .maybeSingle();
    final id = (res?['id'] as num?)?.toInt();
    if (id == null) throw StateError('User record not found for current user');
    _currentUserId = id;
    return id;
  }

  /// Resets the cached user id (call on sign-out / sign-in as another user).
  static void resetCurrentUserId() => _currentUserId = null;
}

/// Convert a nested Postgrest timestamp to null-safe ISO string.
String? iso(DateTime? value) => value?.toIso8601String();
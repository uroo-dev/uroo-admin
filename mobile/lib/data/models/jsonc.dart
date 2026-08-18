import 'dart:convert';

/// SQLite-friendly helpers for JSON columns.
class Jsonc {
  static List<String> toStringList(dynamic v) {
    if (v is List) return v.map((e) => e.toString()).toList();
    if (v is String) {
      final d = jsonDecode(v);
      if (d is List) return d.map((e) => e.toString()).toList();
    }
    return [];
  }
}
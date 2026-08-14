import 'package:flutter/material.dart';

/// UROO.Admin color palette, ported from DESIGN.MD (Neo Brutalism).
abstract class AppColors {
  static const Color background = Color(0xFFF8F8F8);
  static const Color surface = Color(0xFFFFFFFF);
  static const Color surfaceAlt = Color(0xFFF0F0F0);

  static const Color primary = Color(0xFF4F46E5);
  static const Color secondary = Color(0xFF3B82F6);

  static const Color success = Color(0xFF22C55E);
  static const Color warning = Color(0xFFF59E0B);
  static const Color danger = Color(0xFFEF4444);

  static const Color pinkAccent = Color(0xFFFF66C4);
  static const Color yellowAccent = Color(0xFFFFD93D);
  static const Color cyanAccent = Color(0xFF67E8F9);
  static const Color purpleAccent = Color(0xFFA855F7);

  /// Border & primary text color.
  static const Color borderDark = Color(0xFF111827);
  static const Color txtPrimary = Color(0xFF111827);
  static const Color txtSecondary = Color(0xFF6B7280);

  /// Status badge colors (DESIGN.MD section 16, aligned with the web badges).
  static const Map<String, Color> statusColors = {
    'development': secondary,
    'testing': warning,
    'revision': Color(0xFFF97316),
    'completed': success,
    'archived': Color(0xFF9CA3AF),
    'production': purpleAccent,
    'hutang': warning,
    'lunas': success,
    'research': warning,
    'draft': Color(0xFF9CA3AF),
    'idea': purpleAccent,
    'high': danger,
    'medium': warning,
    'low': success,
    'deal': warning,
    'pending': secondary,
    'canceled': danger,
  };

  static Color statusColor(String status) =>
      statusColors[status] ?? const Color(0xFF9CA3AF);
}

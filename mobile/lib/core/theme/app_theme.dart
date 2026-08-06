import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

import 'app_colors.dart';

/// Global theme implementing the Neo Brutalism design system from DESIGN.MD.
abstract class AppTheme {
  /// Hard shadow, no blur (8px 8px 0 #111827).
  static List<BoxShadow> hardShadow({double offset = 8}) => [
        BoxShadow(
          offset: Offset(offset, offset),
          color: AppColors.borderDark,
          blurRadius: 0,
        ),
      ];

  /// Hard shadow for pressed state (4px 4px 0 #111827).
  static List<BoxShadow> pressedShadow() => [
        BoxShadow(
          offset: Offset(4, 4),
          color: AppColors.borderDark,
          blurRadius: 0,
        ),
      ];

  static ThemeData light() {
    final base = ThemeData(
      useMaterial3: true,
      fontFamily: GoogleFonts.inter().fontFamily,
      scaffoldBackgroundColor: AppColors.background,
      colorScheme: const ColorScheme.light(
        primary: AppColors.primary,
        secondary: AppColors.secondary,
        surface: AppColors.surface,
        error: AppColors.danger,
      ),
    );

    return base.copyWith(
      textTheme: base.textTheme
          .apply(bodyColor: AppColors.txtPrimary, displayColor: AppColors.txtPrimary),
      dividerColor: AppColors.borderDark,
    );
  }
}
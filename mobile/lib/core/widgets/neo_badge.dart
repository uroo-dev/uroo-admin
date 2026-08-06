import 'package:flutter/material.dart';

import '../theme/app_colors.dart';

class NeoBadge extends StatelessWidget {
  final String label;
  final Color color;
  final bool filled;

  const NeoBadge({
    super.key,
    required this.label,
    required this.color,
    this.filled = true,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: filled ? color : AppColors.surface,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.borderDark, width: 3),
        boxShadow: const [
          BoxShadow(offset: Offset(3, 3), color: AppColors.borderDark, blurRadius: 0),
        ],
      ),
      child: Text(
        label,
        style: TextStyle(
          color: filled ? Colors.white : color,
          fontWeight: FontWeight.w800,
          fontSize: 12,
        ),
      ),
    );
  }
}
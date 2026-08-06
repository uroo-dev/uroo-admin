import 'package:flutter/material.dart';

import '../theme/app_colors.dart';
import '../theme/app_theme.dart';

enum NeoButtonVariant { primary, secondary, danger, success, warning }

class NeoButton extends StatefulWidget {
  final String label;
  final VoidCallback onPressed;
  final NeoButtonVariant variant;
  final IconData? icon;
  final bool loading;
  final bool expanded;
  final double? height;

  const NeoButton({
    super.key,
    required this.label,
    required this.onPressed,
    this.variant = NeoButtonVariant.primary,
    this.icon,
    this.loading = false,
    this.expanded = false,
    this.height = 56,
  });

  @override
  State<NeoButton> createState() => _NeoButtonState();
}

class _NeoButtonState extends State<NeoButton> {
  bool _pressed = false;

  Color get _bg => switch (widget.variant) {
        NeoButtonVariant.primary => AppColors.primary,
        NeoButtonVariant.secondary => AppColors.surface,
        NeoButtonVariant.danger => AppColors.danger,
        NeoButtonVariant.success => AppColors.success,
        NeoButtonVariant.warning => AppColors.warning,
      };

  Color get _fg => widget.variant == NeoButtonVariant.secondary
      ? AppColors.txtPrimary
      : Colors.white;

  @override
  Widget build(BuildContext context) {
    final button = AnimatedScale(
      scale: _pressed ? 0.98 : 1,
      duration: const Duration(milliseconds: 100),
      child: Material(
        color: _bg,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        child: InkWell(
          onTap: widget.loading ? null : widget.onPressed,
          borderRadius: BorderRadius.circular(18),
          onHighlightChanged: (v) => setState(() => _pressed = v),
          child: Container(
            width: widget.expanded ? double.infinity : null,
            height: widget.height,
            padding: const EdgeInsets.symmetric(horizontal: 24),
            alignment: Alignment.center,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              boxShadow: _pressed ? AppTheme.pressedShadow() : AppTheme.hardShadow(),
            ),
            child: widget.loading
                ? const SizedBox(
                    width: 22,
                    height: 22,
                    child: CircularProgressIndicator(
                      strokeWidth: 3,
                      color: Colors.white,
                    ),
                  )
                : Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      if (widget.icon != null) ...[
                        Icon(widget.icon, color: _fg, size: 22),
                        const SizedBox(width: 8),
                      ],
                      Text(
                        widget.label,
                        style: TextStyle(
                          fontWeight: FontWeight.w800,
                          fontSize: 16,
                          color: _fg,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ],
                  ),
          ),
        ),
      ),
    );

    return widget.expanded ? SizedBox(width: double.infinity, child: button) : button;
  }
}
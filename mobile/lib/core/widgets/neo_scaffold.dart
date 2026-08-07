import 'package:flutter/material.dart';

import '../theme/app_colors.dart';

/// App-wide scaffold with Neo Brutalism chrome (title bar + hard shadow border).
class NeoScaffold extends StatelessWidget {
  final String title;
  final Widget body;
  final List<Widget>? actions;
  final Widget? floatingActionButton;
  final PreferredSizeWidget? bottom;
  final bool showBack;

  const NeoScaffold({
    super.key,
    required this.title,
    required this.body,
    this.actions,
    this.floatingActionButton,
    this.bottom,
    this.showBack = true,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        toolbarHeight: 68,
        leading: showBack
            ? IconButton(
                onPressed: () => Navigator.of(context).pop(),
                icon: const Icon(Icons.arrow_back_ios_new, size: 22),
                color: AppColors.txtPrimary,
              )
            : null,
        title: Text(
          title,
          style: const TextStyle(
            fontWeight: FontWeight.w800,
            fontSize: 20,
            color: AppColors.txtPrimary,
          ),
        ),
        actions: actions,
        shape: const Border(
          bottom: BorderSide(color: AppColors.borderDark, width: 4),
        ),
      ),
      bottomNavigationBar: bottom,
      floatingActionButton: floatingActionButton,
      body: SafeArea(
        top: false,
        child: body,
      ),
    );
  }
}

/// Wraps content with standard page padding.
class NeoPage extends StatelessWidget {
  final Widget child;

  const NeoPage({super.key, required this.child});

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: child,
    );
  }
}

/// Big section heading.
class NeoSectionTitle extends StatelessWidget {
  final String text;
  final IconData? icon;

  const NeoSectionTitle(this.text, {super.key, this.icon});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        if (icon != null) ...[
          Icon(icon, color: AppColors.primary, size: 22),
          const SizedBox(width: 8),
        ],
        Text(
          text,
          style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
        ),
      ],
    );
  }
}
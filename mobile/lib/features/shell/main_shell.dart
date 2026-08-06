import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../core/theme/app_colors.dart';
import '../core/theme/app_theme.dart';
import '../features/auth/auth_controller.dart';

/// Main scaffold with bottom navigation + drawer (Neo brutalism).
class MainShell extends ConsumerWidget {
  final StatefulNavigationShell navigationShell;

  const MainShell({super.key, required this.navigationShell});

  void _switchTab(BuildContext context, int index) {
    navigationShell.goBranch(
      index,
      initialLocation: index == navigationShell.currentIndex,
    );
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      backgroundColor: AppColors.background,
      drawer: _buildDrawer(context, ref),
      body: navigationShell,
      bottomNavigationBar: NavigationBar(
        backgroundColor: AppColors.surface,
        height: 72,
        selectedIndex: navigationShell.currentIndex,
        onDestinationSelected: (i) => _switchTab(context, i),
        labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
        indicatorColor: AppColors.primary,
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.space_dashboard_outlined),
            selectedIcon: Icon(Icons.space_dashboard, color: Colors.white),
            label: 'Dashboard',
          ),
          NavigationDestination(
            icon: Icon(Icons.sticky_note_2_outlined),
            selectedIcon: Icon(Icons.sticky_note_2, color: Colors.white),
            label: 'Catatan',
          ),
          NavigationDestination(
            icon: Icon(Icons.lightbulb_outline),
            selectedIcon: Icon(Icons.lightbulb, color: Colors.white),
            label: 'Ide',
          ),
          NavigationDestination(
            icon: Icon(Icons.savings_outlined),
            selectedIcon: Icon(Icons.savings, color: Colors.white),
            label: 'Tabungan',
          ),
          NavigationDestination(
            icon: Icon(Icons.receipt_long_outlined),
            selectedIcon: Icon(Icons.receipt_long, color: Colors.white),
            label: 'Invoice',
          ),
        ],
      ),
    );
  }

  Widget _buildDrawer(BuildContext context, WidgetRef ref) {
    final tabs = ['dashboard', 'notes', 'ideas', 'savings', 'invoices'];
    final extra = [
      ('Brain Dumps', Icons.psychology_outlined, '/brain-dumps'),
      ('Clients', Icons.people_alt_outlined, '/clients'),
      ('Projects', Icons.folder_copy_outlined, '/projects'),
    ];

    return Drawer(
      backgroundColor: AppColors.surface,
      shape: const Border(right: BorderSide(color: AppColors.borderDark, width: 4)),
      child: SafeArea(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Padding(
              padding: EdgeInsets.fromLTRB(20, 24, 20, 16),
              child: Row(
                children: [
                  Icon(Icons.rocket_launch, color: AppColors.primary, size: 28),
                  SizedBox(width: 10),
                  Text(
                    'UROO.Admin',
                    style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800),
                  ),
                ],
              ),
            ),
            const Divider(color: AppColors.borderDark, height: 1),
            Expanded(
              child: ListView(
                children: [
                  for (var i = 0; i < tabs.length; i++)
                    _drawerItem(
                      context,
                      icon: ['dashboard', 'notes', 'ideas', 'savings', 'invoices']
                              [i],
                      label: ['Dashboard', 'Catatan', 'Ide', 'Tabungan', 'Invoice'][i],
                      active: i == navigationShell.currentIndex,
                      onTap: () {
                        Navigator.of(context).pop();
                        _switchTab(context, i);
                      },
                    ),
                  const Divider(color: AppColors.borderDark, height: 16),
                  for (final e in extra)
                    _drawerItem(
                      context,
                      icon: e.$2,
                      label: e.$1,
                      onTap: () {
                        Navigator.of(context).pop();
                        context.go(e.$3);
                      },
                    ),
                ],
              ),
            ),
            const Divider(color: AppColors.borderDark, height: 1),
            _drawerItem(
              context,
              icon: Icons.logout,
              label: 'Keluar',
              onTap: () async {
                Navigator.of(context).pop();
                await ref.read(authControllerProvider.notifier).signOut();
                context.go('/login');
              },
            ),
            const SizedBox(height: 8),
          ],
        ),
      ),
    );
  }

  Widget _drawerItem(BuildContext context, {
    required IconData icon,
    required String label,
    required VoidCallback onTap,
    bool active = false,
  }) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Material(
        color: active ? AppColors.primary : AppColors.surface,
        borderRadius: BorderRadius.circular(18),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: BorderSide(
            color: AppColors.borderDark,
            width: active ? 0 : 4,
          ),
        ),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(18),
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(18),
              boxShadow: active ? AppTheme.pressedShadow() : AppTheme.hardShadow(),
            ),
            child: Row(
              children: [
                Icon(icon, color: active ? Colors.white : AppColors.txtPrimary,
                    size: 24),
                const SizedBox(width: 14),
                Text(
                  label,
                  style: TextStyle(
                    fontWeight: FontWeight.w800,
                    fontSize: 16,
                    color: active ? Colors.white : AppColors.txtPrimary,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
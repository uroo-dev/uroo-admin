import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../sync/sync_controller.dart';
import '../theme/app_colors.dart';
import 'neo_badge.dart';

/// Chip status koneksi/sinkronisasi server (Neo brutalism).
/// Dipasang di drawer shell, dashboard, dan layar pengaturan.
class SyncStatusBadge extends ConsumerWidget {
  const SyncStatusBadge({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final state = ref.watch(syncProvider);

    switch (state.phase) {
      case ConnectionPhase.online:
        return const NeoBadge(label: 'Online', color: AppColors.success);
      case ConnectionPhase.syncing:
        return const NeoBadge(label: 'Sinkron…', color: AppColors.secondary);
      case ConnectionPhase.offline:
        return const NeoBadge(label: 'Offline', color: AppColors.warning);
      case ConnectionPhase.notConnected:
        return const NeoBadge(label: 'Belum tersambung', color: AppColors.txtSecondary);
    }
  }
}

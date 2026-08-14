import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_card.dart';
import 'dashboard_controller.dart';

class DashboardScreen extends ConsumerWidget {
  const DashboardScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dashboard = ref.watch(dashboardProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.surface,
        elevation: 0,
        title: const Text(
          'UROO.Admin',
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 22),
        ),
        actions: [
          Builder(
            builder: (context) => IconButton(
              onPressed: () => Scaffold.of(context).openDrawer(),
              icon: const Icon(Icons.menu),
            ),
          ),
        ],
        shape: const Border(
          bottom: BorderSide(color: AppColors.borderDark, width: 4),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: () async {
          ref.invalidate(dashboardProvider);
          await ref.read(dashboardProvider.future);
        },
        child: dashboard.when(
          loading: () => const Center(child: CircularProgressIndicator()),
          error: (e, _) => ListView(
            padding: const EdgeInsets.all(24),
            children: [
              Text('Gagal memuat dashboard: $e'),
            ],
          ),
          data: (d) => ListView(
            padding: const EdgeInsets.all(16),
            children: [
              _statsGrid(d),
              const SizedBox(height: 24),
              const _SectionTitle('Aksi Cepat'),
              const SizedBox(height: 12),
              _quickActions(context),
              const SizedBox(height: 24),
              const _SectionTitle('Invoice Terbaru'),
              const SizedBox(height: 12),
              if (d.recentInvoices.isEmpty)
                const _EmptyHint('Belum ada invoice.')
              else
                for (final inv in d.recentInvoices)
                  NeoCard(
                    padding: const EdgeInsets.all(16),
                    onTap: () => context.push('/invoices/${inv.id}'),
                    child: Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('#${inv.invoiceNumber}',
                                  style: const TextStyle(fontWeight: FontWeight.w800)),
                              Text(
                                inv.clientName ?? 'Tanpa client',
                                style: const TextStyle(
                                    color: AppColors.txtSecondary, fontSize: 12),
                              ),
                            ],
                          ),
                        ),
                        NeoBadge(
                          label: inv.status,
                          color: AppColors.statusColor(inv.status),
                        ),
                        const SizedBox(width: 12),
                        Text(FormatUtil.rupiah(inv.total),
                            style: const TextStyle(fontWeight: FontWeight.w800)),
                      ],
                    ),
                  ),
              const SizedBox(height: 24),
              const _SectionTitle('Target Tabungan'),
              const SizedBox(height: 12),
              if (d.recentSavings.isEmpty)
                const _EmptyHint('Belum ada target tabungan.')
              else
                for (final goal in d.recentSavings)
                  NeoCard(
                    padding: const EdgeInsets.all(16),
                    onTap: () => context.push('/savings/${goal.id}'),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Expanded(
                              child: Text(goal.name,
                                  style: const TextStyle(fontWeight: FontWeight.w800)),
                            ),
                            Text(
                              '${(goal.progress * 100).round()}%',
                              style: const TextStyle(
                                  color: AppColors.primary,
                                  fontWeight: FontWeight.w800),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        ClipRRect(
                          borderRadius: BorderRadius.circular(10),
                          child: LinearProgressIndicator(
                            value: goal.progress,
                            minHeight: 14,
                            backgroundColor: AppColors.surfaceAlt,
                            valueColor: const AlwaysStoppedAnimation(
                                AppColors.primary),
                          ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          '${FormatUtil.rupiah(goal.currentAmount)} / ${FormatUtil.rupiah(goal.targetAmount)}',
                          style: const TextStyle(
                              color: AppColors.txtSecondary, fontSize: 12),
                        ),
                      ],
                    ),
                  ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _statsGrid(DashboardData d) {
    final stats = [
      ('Project Aktif', d.activeProjects, Icons.folder_copy,
          AppColors.primary),
      ('Invoice Pending', d.pendingInvoices, Icons.receipt_long,
          AppColors.success),
      ('Clients', d.totalClients, Icons.people_alt, AppColors.warning),
      ('Tabungan', d.totalSavings, Icons.savings, AppColors.purpleAccent),
    ];

    return Column(
      children: [
        Row(
          children: [
            _statCard(stats[0], 0),
            const SizedBox(width: 12),
            _statCard(stats[1], 1),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            _statCard(stats[2], 2),
            const SizedBox(width: 12),
            _statCard(stats[3], 3),
          ],
        ),
      ],
    );
  }

  Widget _statCard((String, num, IconData, Color) stat, int index) {
    return Expanded(
      child: NeoCard(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                color: stat.$4,
                borderRadius: BorderRadius.circular(18),
                boxShadow: const [
                  BoxShadow(
                    offset: Offset(4, 4),
                    color: AppColors.borderDark,
                    blurRadius: 0,
                  ),
                ],
              ),
              child: Icon(stat.$3, color: Colors.white, size: 26),
            ),
            const SizedBox(height: 12),
            Text(
              '${stat.$2}',
              style: const TextStyle(fontSize: 30, fontWeight: FontWeight.w800),
            ),
            Text(
              stat.$1,
              style: const TextStyle(
                  color: AppColors.txtSecondary, fontSize: 14),
            ),
          ],
        ),
      ),
    );
  }

  Widget _quickActions(BuildContext context) {
    final actions = [
      ('Catatan', Icons.sticky_note_2_outlined, AppColors.primary, '/notes'),
      ('Ide', Icons.lightbulb_outline, AppColors.pinkAccent, '/ideas'),
      ('Brain', Icons.psychology_outlined, AppColors.warning, '/brain-dumps'),
      ('Tabungan', Icons.savings_outlined, AppColors.success, '/savings'),
      ('Invoice', Icons.receipt_long_outlined, AppColors.secondary, '/invoices'),
      ('Clients', Icons.people_alt_outlined, AppColors.purpleAccent, '/clients'),
      ('Projects', Icons.folder_copy_outlined, AppColors.cyanAccent, '/projects'),
    ];

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 4,
        mainAxisSpacing: 12,
        crossAxisSpacing: 12,
        childAspectRatio: 0.82,
      ),
      itemCount: actions.length,
      itemBuilder: (context, i) => _QuickAction(
        label: actions[i].$1,
        icon: actions[i].$2,
        color: actions[i].$3,
        onTap: () => context.push(actions[i].$4),
      ),
    );
  }
}

class _QuickAction extends StatelessWidget {
  final String label;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  const _QuickAction({
    required this.label,
    required this.icon,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return NeoCard(
      padding: const EdgeInsets.all(10),
      onTap: onTap,
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(icon, color: color, size: 28),
          const SizedBox(height: 6),
          Text(
            label,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700),
          ),
        ],
      ),
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String text;
  const _SectionTitle(this.text);

  @override
  Widget build(BuildContext context) {
    return Text(
      text,
      style: const TextStyle(fontSize: 17, fontWeight: FontWeight.w800),
    );
  }
}

class _EmptyHint extends StatelessWidget {
  final String text;
  const _EmptyHint(this.text);

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(8),
      child: Text(
        text,
        style: const TextStyle(color: AppColors.txtSecondary),
      ),
    );
  }
}
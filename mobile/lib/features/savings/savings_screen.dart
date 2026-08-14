import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/savings_goal.dart';
import 'savings_controller.dart';

class SavingsScreen extends ConsumerWidget {
  const SavingsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final goals = ref.watch(savingsProvider);

    return NeoScaffold(
      title: 'Tabungan',
      showBack: false,
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.success,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        onPressed: () => context.push('/savings/new'),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
      body: goals.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) =>
            NeoEmptyState(title: 'Gagal memuat', message: '$e', icon: Icons.error_outline),
        data: (list) {
          if (list.isEmpty) {
            return const NeoEmptyState(
              title: 'Belum ada target tabungan',
              message: 'Buat target keuanganmu dan raih satu per satu.',
              icon: Icons.savings_outlined,
            );
          }
          return ListView.builder(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
            itemCount: list.length,
            itemBuilder: (context, i) {
              final goal = list[i];
              return _GoalCard(
                goal: goal,
                onTap: () => context.push('/savings/${goal.id}'),
              );
            },
          );
        },
      ),
    );
  }
}

class _GoalCard extends StatelessWidget {
  final SavingsGoal goal;
  final VoidCallback onTap;

  const _GoalCard({required this.goal, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final pct = (goal.progress * 100).round();
    final color = _progressColor();
    final overdue = goal.deadline != null &&
        !goal.isCompleted &&
        goal.deadline!.isBefore(DateTime.now());

    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: NeoCard(onTap: onTap, child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              _iconBadge(goal.icon, _goalColor),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      goal.name,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                          fontSize: 18, fontWeight: FontWeight.w800),
                    ),
                    if (goal.deadline != null)
                      Text(
                        'Tenggat ${FormatUtil.date(goal.deadline)}',
                        style: TextStyle(
                          color: overdue ? AppColors.danger : AppColors.txtSecondary,
                          fontWeight: overdue ? FontWeight.w800 : FontWeight.w400,
                          fontSize: 12,
                        ),
                      ),
                  ],
                ),
              ),
              if (goal.isCompleted)
                const NeoBadge(label: 'Selesai', color: AppColors.success)
              else
                const NeoBadge(label: 'In Progress', color: AppColors.warning),
            ],
          ),
          const SizedBox(height: 16),
          ClipRRect(
            borderRadius: BorderRadius.circular(999),
            child: LinearProgressIndicator(
              value: goal.progress,
              minHeight: 16,
              backgroundColor: const Color(0xFFF3F4F6),
              valueColor: AlwaysStoppedAnimation(color),
            ),
          ),
          const SizedBox(height: 10),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                FormatUtil.rupiah(goal.currentAmount),
                style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 15),
              ),
              Text(
                'dari ${FormatUtil.rupiah(goal.targetAmount)} ($pct%)',
                style: const TextStyle(
                    color: AppColors.txtSecondary, fontSize: 12),
              ),
            ],
          ),
          const SizedBox(height: 12),
          const Divider(color: AppColors.borderDark, height: 1),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: NeoButton(
                  label: 'Simpan +',
                  variant: NeoButtonVariant.success,
                  height: 48,
                  icon: Icons.arrow_downward,
                  onPressed: () => onTap(),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: NeoButton(
                  label: 'Tarik -',
                  variant: NeoButtonVariant.warning,
                  height: 48,
                  icon: Icons.arrow_upward,
                  onPressed: () => onTap(),
                ),
              ),
            ],
          ),
        ],
      )),
    );
  }

  Color get _goalColor {
    final hex = goal.color;
    if (hex != null && hex.isNotEmpty) {
      return Color(int.parse('0xFF${hex.replaceFirst('#', '')}'));
    }

    return AppColors.primary;
  }

  /// Web progress thresholds: done → green, ≥75% → yellow, ≥40% → blue, else primary.
  Color _progressColor() {
    if (goal.isCompleted || goal.progress >= 1) return AppColors.success;
    if (goal.progress >= 0.75) return AppColors.warning;
    if (goal.progress >= 0.40) return AppColors.secondary;

    return AppColors.primary;
  }

  Widget _iconBadge(String? icon, Color color) {
    return Container(
      width: 52,
      height: 52,
      decoration: BoxDecoration(
        color: color,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: AppColors.borderDark, width: 3),
      ),
      child: const Icon(Icons.savings, color: Colors.white, size: 26),
    );
  }
}
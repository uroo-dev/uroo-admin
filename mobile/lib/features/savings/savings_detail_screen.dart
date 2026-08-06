import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_input.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/savings_goal.dart';
import 'savings_controller.dart';

class SavingsDetailScreen extends ConsumerStatefulWidget {
  final int id;

  const SavingsDetailScreen({super.key, required this.id});

  @override
  ConsumerState<SavingsDetailScreen> createState() => _SavingsDetailScreenState();
}

class _SavingsDetailScreenState extends ConsumerState<SavingsDetailScreen> {
  final _amount = TextEditingController();
  final _description = TextEditingController();

  SavingsGoal? _goal;

  @override
  void initState() {
    super.initState();
    _goal = (ref.read(savingsProvider).value ?? [])
        .where((e) => e.id == widget.id)
        .firstOrNull;
  }

  Future<void> _transact(String type) async {
    final goal = _goal;
    if (goal == null) return;
    final amount = double.tryParse(_amount.text.trim());
    if (amount == null || amount <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Nominal tidak valid')),
      );
      return;
    }
    await ref
        .read(savingsProvider.notifier)
        .transact(goal, type, amount, _description.text.trim());
    if (mounted) {
      _amount.clear();
      _description.clear();
      setState(() {
        _goal = (ref.read(savingsProvider).value ?? [])
            .where((e) => e.id == widget.id)
            .firstOrNull;
      });
    }
  }

  Future<void> _deleteGoal() async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Hapus target?'),
        actions: [
          TextButton(onPressed: () => c.pop(false), child: const Text('Batal')),
          TextButton(onPressed: () => c.pop(true), child: const Text('Hapus')),
        ],
      ),
    );
    if (ok == true) {
      await ref.read(savingsProvider.notifier).deleteGoal(widget.id);
      if (mounted) context.pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    final goal = _goal;
    final transactions = ref.watch(savingsTransactionsProvider(widget.id));

    if (goal == null) {
      return const NeoScaffold(
        title: 'Target',
        body: Center(child: Text('Data tidak ditemukan')),
      );
    }

    return NeoScaffold(
      title: goal.name,
      actions: [
        IconButton(
          onPressed: _deleteGoal,
          icon: const Icon(Icons.delete_outline, color: AppColors.danger),
        ),
      ],
      body: NeoPage(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            NeoCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    '${FormatUtil.rupiah(goal.currentAmount)} / ${FormatUtil.rupiah(goal.targetAmount)}',
                    style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800),
                  ),
                  const SizedBox(height: 12),
                  ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: LinearProgressIndicator(
                      value: goal.progress,
                      minHeight: 16,
                      backgroundColor: AppColors.surfaceAlt,
                      valueColor: const AlwaysStoppedAnimation(AppColors.primary),
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    'Sisa ${FormatUtil.rupiah(goal.remaining)} (${(goal.progress * 100).round()}%)',
                    style: const TextStyle(
                        color: AppColors.txtSecondary, fontSize: 13),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            NeoCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Text('Transaksi',
                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
                  const SizedBox(height: 12),
                  NeoInput(
                    controller: _amount,
                    label: 'Nominal',
                    keyboardType: TextInputType.number,
                    hint: 'Rp ...',
                  ),
                  const SizedBox(height: 12),
                  NeoInput(
                    controller: _description,
                    label: 'Keterangan (opsional)',
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      Expanded(
                        child: NeoButton(
                          label: 'Simpan +',
                          variant: NeoButtonVariant.success,
                          onPressed: () => _transact('deposit'),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: NeoButton(
                          label: 'Tarik -',
                          variant: NeoButtonVariant.danger,
                          onPressed: () => _transact('withdraw'),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
            const Text('Riwayat', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            const SizedBox(height: 8),
            transactions.when(
              loading: () => const Padding(
                padding: EdgeInsets.all(24),
                child: Center(child: CircularProgressIndicator()),
              ),
              error: (e, _) => Text('$e'),
              data: (list) => list.isEmpty
                  ? const NeoEmptyState(
                      title: 'Belum ada transaksi',
                      message: 'Mulai simpan atau tarik dana.',
                      icon: Icons.receipt_long_outlined,
                    )
                  : Column(
                      children: [
                        for (final t in list)
                          Padding(
                            padding: const EdgeInsets.only(bottom: 10),
                            child: NeoCard(
                              padding: const EdgeInsets.all(14),
                              child: Row(
                                children: [
                                  Icon(
                                    t.type == 'deposit'
                                        ? Icons.arrow_downward
                                        : Icons.arrow_upward,
                                    color: t.type == 'deposit'
                                        ? AppColors.success
                                        : AppColors.danger,
                                  ),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    child: Text(
                                      t.description ?? (t.type == 'deposit' ? 'Deposit' : 'Penarikan'),
                                      style: const TextStyle(fontWeight: FontWeight.w600),
                                    ),
                                  ),
                                  Text(
                                    '${t.type == 'deposit' ? '+' : '-'}${FormatUtil.rupiah(t.amount)}',
                                    style: TextStyle(
                                      fontWeight: FontWeight.w800,
                                      color: t.type == 'deposit'
                                          ? AppColors.success
                                          : AppColors.danger,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                      ],
                    ),
            ),
          ],
        ),
      ),
    );
  }
}
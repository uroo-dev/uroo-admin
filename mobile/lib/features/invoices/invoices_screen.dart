import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import 'invoices_controller.dart';

class InvoicesScreen extends ConsumerWidget {
  const InvoicesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final invoices = ref.watch(invoicesProvider);

    return NeoScaffold(
      title: 'Invoice',
      showBack: false,
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.primary,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        onPressed: () => context.push('/invoices/new'),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
      body: invoices.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) =>
            NeoEmptyState(title: 'Gagal memuat', message: '$e', icon: Icons.error_outline),
        data: (list) {
          if (list.isEmpty) {
            return const NeoEmptyState(
              title: 'Belum ada invoice',
              message: 'Buat invoice untuk client-mu.',
              icon: Icons.receipt_long_outlined,
            );
          }
          return ListView.builder(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
            itemCount: list.length,
            itemBuilder: (context, i) {
              final inv = list[i];
              final overdue = inv.dueDate != null &&
                  !inv.isPaid &&
                  inv.dueDate!.isBefore(DateTime.now().subtract(const Duration(days: 1)));
              final remaining = inv.total - inv.paidAmount;
              return Padding(
                padding: const EdgeInsets.only(bottom: 16),
                child: NeoCard(
                  onTap: () => context.push('/invoices/${inv.id}'),
                  child: Row(
                    children: [
                      Container(
                        width: 52,
                        height: 52,
                        decoration: BoxDecoration(
                          color: inv.isPaid
                              ? AppColors.success
                              : AppColors.warning,
                          borderRadius: BorderRadius.circular(18),
                          border: Border.all(color: AppColors.borderDark, width: 3),
                        ),
                        child: const Icon(Icons.receipt,
                            color: Colors.white, size: 26),
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              '#${inv.invoiceNumber}',
                              style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w800,
                                  color: AppColors.primary),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              inv.clientName ?? 'Tanpa client',
                              style: const TextStyle(
                                  color: AppColors.txtSecondary, fontSize: 13),
                            ),
                            if (inv.dueDate != null)
                              Text(
                                'Jatuh tempo ${FormatUtil.date(inv.dueDate)}${overdue ? ' • Terlambat' : ''}',
                                style: TextStyle(
                                    color: overdue
                                        ? AppColors.danger
                                        : AppColors.txtSecondary,
                                    fontWeight: overdue
                                        ? FontWeight.w800
                                        : FontWeight.w400,
                                    fontSize: 12),
                              ),
                          ],
                        ),
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(
                            FormatUtil.rupiah(inv.total),
                            style: const TextStyle(
                                fontWeight: FontWeight.w800, fontSize: 15),
                          ),
                          Text(
                            'Terbayar ${FormatUtil.rupiah(inv.paidAmount)}',
                            style: const TextStyle(
                                color: AppColors.success,
                                fontSize: 11,
                                fontWeight: FontWeight.w700),
                          ),
                          if (remaining > 0)
                            Text(
                              'Sisa ${FormatUtil.rupiah(remaining)}',
                              style: const TextStyle(
                                  color: AppColors.danger,
                                  fontSize: 11,
                                  fontWeight: FontWeight.w700),
                            ),
                          const SizedBox(height: 6),
                          NeoBadge(
                            label: inv.status,
                            color: AppColors.statusColor(inv.status),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }
}
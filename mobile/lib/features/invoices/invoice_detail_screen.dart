import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/invoice.dart';
import 'invoices_controller.dart';

class InvoiceDetailScreen extends ConsumerWidget {
  final int id;

  const InvoiceDetailScreen({super.key, required this.id});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final invoices = ref.watch(invoicesProvider);
    final invoice = invoices.value?.where((e) => e.id == id).firstOrNull;

    if (invoice == null) {
      return const NeoScaffold(
        title: 'Invoice',
        body: Center(child: Text('Data tidak ditemukan')),
      );
    }

    return NeoScaffold(
      title: 'Invoice #${invoice.invoiceNumber}',
      actions: [
        IconButton(
          onPressed: () => _confirmDelete(context, ref, invoice),
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
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          invoice.clientName ?? 'Tanpa client',
                          style: const TextStyle(
                              fontSize: 18, fontWeight: FontWeight.w800),
                        ),
                      ),
                      NeoBadge(
                        label: invoice.status,
                        color: AppColors.statusColor(invoice.status),
                      ),
                    ],
                  ),
                  if (invoice.dueDate != null) ...[
                    const SizedBox(height: 6),
                    Text(
                      'Jatuh tempo: ${FormatUtil.date(invoice.dueDate)}',
                      style: const TextStyle(
                          color: AppColors.txtSecondary, fontSize: 13),
                    ),
                  ],
                  const SizedBox(height: 16),
                  const Text('Item',
                      style: TextStyle(fontSize: 15, fontWeight: FontWeight.w800)),
                  const SizedBox(height: 8),
                  for (final item in invoice.items)
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: 4),
                      child: Row(
                        children: [
                          Expanded(
                            child: Text(item.name,
                                style: const TextStyle(
                                    fontWeight: FontWeight.w600)),
                          ),
                          Text(
                            '${item.qty} x ${FormatUtil.rupiah(item.price)}',
                            style: const TextStyle(
                                color: AppColors.txtSecondary, fontSize: 13),
                          ),
                          const SizedBox(width: 8),
                          Text(
                            FormatUtil.rupiah(item.subtotal),
                            style: const TextStyle(fontWeight: FontWeight.w700),
                          ),
                        ],
                      ),
                    ),
                  const Divider(color: AppColors.borderDark, height: 24),
                  _row('Subtotal', FormatUtil.rupiah(invoice.subtotal)),
                  _row('Pajak', FormatUtil.rupiah(invoice.taxAmount)),
                  _row('Diskon', '-${FormatUtil.rupiah(invoice.discountAmount)}'),
                  const SizedBox(height: 8),
                  _row('Total', FormatUtil.rupiah(invoice.total), bold: true),
                  _row('Dibayar', FormatUtil.rupiah(invoice.paidAmount)),
                ],
              ),
            ),
            const SizedBox(height: 16),
            if (!invoice.isPaid)
              NeoButton(
                label: 'Tandai Lunas',
                icon: Icons.check_circle_outline,
                variant: NeoButtonVariant.success,
                expanded: true,
                onPressed: () async {
                  await ref
                      .read(invoicesProvider.notifier)
                      .markPaid(id, invoice.total);
                },
              ),
            if (invoice.isPaid)
              const NeoBadge(label: 'Invoice lunas', color: AppColors.success),
          ],
        ),
      ),
    );
  }

  Widget _row(String label, String value, {bool bold = false}) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 3),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(label,
                style: TextStyle(
                    color: AppColors.txtSecondary,
                    fontWeight: bold ? FontWeight.w700 : FontWeight.w400)),
            Text(value,
                style: TextStyle(
                    fontWeight: bold ? FontWeight.w800 : FontWeight.w600)),
          ],
        ),
      );

  Future<void> _confirmDelete(
      BuildContext context, WidgetRef ref, Invoice invoice) async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Hapus invoice?'),
        actions: [
          TextButton(onPressed: () => c.pop(false), child: const Text('Batal')),
          TextButton(onPressed: () => c.pop(true), child: const Text('Hapus')),
        ],
      ),
    );
    if (ok == true) {
      await ref.read(invoicesProvider.notifier).delete(invoice.id!);
      if (context.mounted) context.pop();
    }
  }
}
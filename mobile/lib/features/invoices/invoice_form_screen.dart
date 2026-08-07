import 'dart:math';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_input.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/client.dart';
import '../../data/models/invoice.dart';
import '../../data/repositories/client_repository.dart';
import 'invoices_controller.dart';

class InvoiceFormScreen extends ConsumerStatefulWidget {
  const InvoiceFormScreen({super.key});

  @override
  ConsumerState<InvoiceFormScreen> createState() => _InvoiceFormScreenState();
}

class _ItemRow {
  final TextEditingController name = TextEditingController();
  final TextEditingController qty = TextEditingController(text: '1');
  final TextEditingController price = TextEditingController();
}

class _InvoiceFormScreenState extends ConsumerState<InvoiceFormScreen> {
  final _items = <_ItemRow>[_ItemRow()];
  final _tax = TextEditingController();
  final _discount = TextEditingController();
  int? _clientId;
  bool _saving = false;
  List<Client> _clients = [];

  @override
  void initState() {
    super.initState();
    _loadClients();
  }

  Future<void> _loadClients() async {
    final list = await ClientRepository().list();
    if (mounted) setState(() => _clients = list);
  }

  @override
  void dispose() {
    for (final r in _items) {
      r.name.dispose();
      r.qty.dispose();
      r.price.dispose();
    }
    _tax.dispose();
    _discount.dispose();
    super.dispose();
  }

  double get _subtotal => _items.fold(
      0,
      (sum, r) =>
          sum + (double.tryParse(r.price.text) ?? 0) * (int.tryParse(r.qty.text) ?? 1));

  double get _taxAmount =>
      _subtotal * ((double.tryParse(_tax.text) ?? 0) / 100);

  double get _discountAmount =>
      _subtotal * ((double.tryParse(_discount.text) ?? 0) / 100);

  double get _total => max(0, _subtotal + _taxAmount - _discountAmount);

  String _generateNumber() {
    final now = DateTime.now();
    final rand = Random().nextInt(9000) + 1000;
    return 'INV-${now.year}-${now.month.toString().padLeft(2, '0')}-$rand';
  }

  Future<void> _save() async {
    if (_clientId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih client terlebih dahulu')),
      );
      return;
    }
    final validItems = _items.where((r) => r.name.text.trim().isNotEmpty).toList();
    if (validItems.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Minimal satu item')),
      );
      return;
    }

    setState(() => _saving = true);
    final invoice = Invoice(
      clientId: _clientId,
      invoiceNumber: _generateNumber(),
      items: validItems
          .map((r) => InvoiceItem(
                name: r.name.text.trim(),
                qty: int.tryParse(r.qty.text) ?? 1,
                price: double.tryParse(r.price.text) ?? 0,
                subtotal: (double.tryParse(r.price.text) ?? 0) *
                    (int.tryParse(r.qty.text) ?? 1),
              ))
          .toList(),
      subtotal: _subtotal,
      taxPercent: double.tryParse(_tax.text) ?? 0,
      taxAmount: _taxAmount,
      discountPercent: double.tryParse(_discount.text) ?? 0,
      discountAmount: _discountAmount,
      total: _total,
      status: 'hutang',
    );
    try {
      await ref.read(invoicesProvider.notifier).create(invoice);
      if (mounted) context.pop();
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: 'Invoice Baru',
      body: NeoPage(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            const Text('Client',
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
            const SizedBox(height: 8),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12),
              decoration: BoxDecoration(
                color: AppColors.surface,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppColors.borderDark, width: 4),
              ),
              child: DropdownButtonHideUnderline(
                child: DropdownButton<int?>(
                  value: _clientId,
                  isExpanded: true,
                  hint: const Text('Pilih client'),
                  items: _clients
                      .map((c) => DropdownMenuItem<int?>(
                            value: c.id,
                            child: Text(c.name),
                          ))
                      .toList(),
                  onChanged: (v) => setState(() => _clientId = v),
                ),
              ),
            ),
            const SizedBox(height: 24),
            const Text('Item',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800)),
            const SizedBox(height: 12),
            for (var i = 0; i < _items.length; i++) ...[
              Row(
                children: [
                  Expanded(
                    flex: 2,
                    child: NeoInput(
                      controller: _items[i].name,
                      hint: 'Nama item',
                    ),
                  ),
                  const SizedBox(width: 8),
                  SizedBox(
                    width: 64,
                    child: NeoInput(
                      controller: _items[i].qty,
                      hint: 'Qty',
                      keyboardType: TextInputType.number,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: NeoInput(
                      controller: _items[i].price,
                      hint: 'Harga',
                      keyboardType: TextInputType.number,
                    ),
                  ),
                  IconButton(
                    onPressed: _items.length > 1
                        ? () => setState(() => _items.removeAt(i))
                        : null,
                    icon: const Icon(Icons.remove_circle_outline,
                        color: AppColors.danger),
                  ),
                ],
              ),
              const SizedBox(height: 8),
            ],
            TextButton.icon(
              onPressed: () => setState(() => _items.add(_ItemRow())),
              icon: const Icon(Icons.add),
              label: const Text('Tambah item'),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: NeoInput(
                    controller: _tax,
                    label: 'Pajak (%)',
                    keyboardType: TextInputType.number,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: NeoInput(
                    controller: _discount,
                    label: 'Diskon (%)',
                    keyboardType: TextInputType.number,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            NeoCard(
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  _summaryRow('Subtotal', FormatUtil.rupiah(_subtotal)),
                  _summaryRow('Pajak', FormatUtil.rupiah(_taxAmount)),
                  _summaryRow('Diskon', '-${FormatUtil.rupiah(_discountAmount)}'),
                  const Divider(color: AppColors.borderDark, height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Total',
                          style: TextStyle(
                              fontSize: 16, fontWeight: FontWeight.w800)),
                      Text(
                        FormatUtil.rupiah(_total),
                        style: const TextStyle(
                            fontSize: 18, fontWeight: FontWeight.w800),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),
            NeoButton(
              label: 'Buat Invoice',
              expanded: true,
              loading: _saving,
              onPressed: _save,
            ),
          ],
        ),
      ),
    );
  }

  Widget _summaryRow(String label, String value) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 4),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(label,
                style: const TextStyle(
                    color: AppColors.txtSecondary, fontSize: 14)),
            Text(value,
                style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
          ],
        ),
      );
}
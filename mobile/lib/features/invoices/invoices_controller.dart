import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/invoice.dart';
import '../../data/repositories/invoice_repository.dart';

class InvoicesController extends AsyncNotifier<List<Invoice>> {
  final _repo = InvoiceRepository();

  @override
  Future<List<Invoice>> build() => _repo.list();

  Future<void> create(Invoice invoice) async {
    await _repo.create(invoice);
    ref.invalidateSelf();
  }

  Future<void> update(int id, Invoice invoice) async {
    await _repo.update(id, invoice);
    ref.invalidateSelf();
  }

  Future<void> markPaid(int id, double paidAmount) async {
    await _repo.markPaid(id, paidAmount);
    ref.invalidateSelf();
  }

  Future<void> delete(int id) async {
    await _repo.delete(id);
    ref.invalidateSelf();
  }
}

final invoicesProvider =
    AsyncNotifierProvider<InvoicesController, List<Invoice>>(
  InvoicesController.new,
);
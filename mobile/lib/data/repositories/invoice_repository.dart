import 'dart:convert';

import 'package:drift/drift.dart';

import '../db/app_database.dart' as db;
import '../models/invoice.dart';

class InvoiceRepository {
  final db.AppDatabase _db = db.AppDatabase.instance;

  Future<List<Invoice>> list() async {
    final rows = await (_db.select(_db.invoices)
          ..where((t) => t.deletedAt.isNull())
          ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]))
        .get();

    return Future.wait(rows.map(_toModel));
  }

  Future<Invoice?> get(int id) async {
    final row = await (_db.select(_db.invoices)
          ..where((t) => t.id.equals(id) & t.deletedAt.isNull()))
        .getSingleOrNull();

    return row == null ? null : _toModel(row);
  }

  Future<Invoice> create(Invoice invoice) async {
    final now = DateTime.now();
    final id = await _db.nextTempId();

    await _db.into(_db.invoices).insert(db.InvoicesCompanion.insert(
          id: Value(id),
          clientId: Value(invoice.clientId),
          invoiceNumber: invoice.invoiceNumber,
          items: Value(jsonEncode(invoice.items.map((e) => e.toMap()).toList())),
          subtotal: Value(invoice.subtotal),
          taxPercent: Value(invoice.taxPercent),
          taxAmount: Value(invoice.taxAmount),
          discountPercent: Value(invoice.discountPercent),
          discountAmount: Value(invoice.discountAmount),
          total: Value(invoice.total),
          paidAmount: Value(invoice.paidAmount),
          status: Value(invoice.status),
          dueDate: Value(invoice.dueDate),
          notes: Value(invoice.notes),
          createdAt: now,
          updatedAt: now,
          dirty: const Value(true),
        ));

    return Invoice(
      id: id,
      clientId: invoice.clientId,
      invoiceNumber: invoice.invoiceNumber,
      items: invoice.items,
      subtotal: invoice.subtotal,
      taxPercent: invoice.taxPercent,
      taxAmount: invoice.taxAmount,
      discountPercent: invoice.discountPercent,
      discountAmount: invoice.discountAmount,
      total: invoice.total,
      paidAmount: invoice.paidAmount,
      status: invoice.status,
      dueDate: invoice.dueDate,
      notes: invoice.notes,
      clientName: await _clientName(invoice.clientId),
      createdAt: now,
    );
  }

  Future<void> update(int id, Invoice invoice) async {
    final now = DateTime.now();

    await (_db.update(_db.invoices)..where((t) => t.id.equals(id))).write(
      db.InvoicesCompanion(
        clientId: Value(invoice.clientId),
        invoiceNumber: Value(invoice.invoiceNumber),
        items: Value(jsonEncode(invoice.items.map((e) => e.toMap()).toList())),
        subtotal: Value(invoice.subtotal),
        taxPercent: Value(invoice.taxPercent),
        taxAmount: Value(invoice.taxAmount),
        discountPercent: Value(invoice.discountPercent),
        discountAmount: Value(invoice.discountAmount),
        total: Value(invoice.total),
        paidAmount: Value(invoice.paidAmount),
        status: Value(invoice.status),
        dueDate: Value(invoice.dueDate),
        notes: Value(invoice.notes),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<void> markPaid(int id, double paidAmount) async {
    final now = DateTime.now();
    final invoice = await get(id);
    if (invoice == null) return;

    final status = paidAmount >= invoice.total ? 'lunas' : 'hutang';

    await (_db.update(_db.invoices)..where((t) => t.id.equals(id))).write(
      db.InvoicesCompanion(
        paidAmount: Value(paidAmount),
        status: Value(status),
        paidAt: Value(status == 'lunas' ? now : null),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );

    await _db.into(_db.invoicePayments).insert(
          db.InvoicePaymentsCompanion.insert(
            id: Value(await _db.nextTempId()),
            invoiceId: Value(id),
            amount: Value(paidAmount),
            remainingAfter: Value((invoice.total - paidAmount).clamp(0, double.infinity)),
            notes: const Value(null),
            createdAt: now,
            updatedAt: now,
            dirty: const Value(true),
          ),
        );
  }

  Future<void> delete(int id) async {
    final now = DateTime.now();

    await (_db.update(_db.invoices)..where((t) => t.id.equals(id))).write(
      db.InvoicesCompanion(
        deletedAt: Value(now),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<Invoice> _toModel(db.Invoice r) async => Invoice(
        id: r.id,
        clientId: r.clientId,
        invoiceNumber: r.invoiceNumber,
        items: (jsonDecode(r.items) as List)
            .map((e) => InvoiceItem.fromJson(e))
            .toList(),
        subtotal: r.subtotal,
        taxPercent: r.taxPercent,
        taxAmount: r.taxAmount,
        discountPercent: r.discountPercent,
        discountAmount: r.discountAmount,
        total: r.total,
        paidAmount: r.paidAmount,
        status: r.status,
        dueDate: r.dueDate,
        notes: r.notes,
        clientName: await _clientName(r.clientId),
        createdAt: r.createdAt,
      );

  Future<String?> _clientName(int? clientId) async {
    if (clientId == null) return null;
    final client = await (_db.select(_db.clients)
          ..where((t) => t.id.equals(clientId)))
        .getSingleOrNull();

    return client?.name;
  }
}
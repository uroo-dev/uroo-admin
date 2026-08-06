class InvoiceItem {
  final String name;
  final int qty;
  final double price;
  final double subtotal;

  const InvoiceItem({
    required this.name,
    required this.qty,
    required this.price,
    this.subtotal = 0,
  });

  factory InvoiceItem.fromJson(dynamic json) {
    final m = (json is Map) ? json.cast<String, dynamic>() : <String, dynamic>{};
    final price = (m['price'] as num?)?.toDouble() ?? 0;
    final qty = (m['qty'] as num?)?.toInt() ?? 1;
    return InvoiceItem(
      name: (m['name'] as String?) ?? '',
      qty: qty,
      price: price,
      subtotal: (m['subtotal'] as num?)?.toDouble() ?? (price * qty),
    );
  }

  Map<String, dynamic> toMap() => {
        'name': name,
        'qty': qty,
        'price': price,
        'subtotal': subtotal,
      };
}

class Invoice {
  final int? id;
  final int? clientId;
  final String invoiceNumber;
  final List<InvoiceItem> items;
  final double subtotal;
  final double taxPercent;
  final double taxAmount;
  final double discountPercent;
  final double discountAmount;
  final double total;
  final double paidAmount;
  final String status; // hutang | lunas
  final DateTime? dueDate;
  final String? notes;
  final String? clientName;
  final DateTime? createdAt;

  const Invoice({
    this.id,
    this.clientId,
    required this.invoiceNumber,
    this.items = const [],
    this.subtotal = 0,
    this.taxPercent = 0,
    this.taxAmount = 0,
    this.discountPercent = 0,
    this.discountAmount = 0,
    this.total = 0,
    this.paidAmount = 0,
    this.status = 'hutang',
    this.dueDate,
    this.notes,
    this.clientName,
    this.createdAt,
  });

  bool get isPaid => paidAmount >= total;

  factory Invoice.fromJson(Map<String, dynamic> json) {
    final itemsRaw = json['items'];
    final items = <InvoiceItem>[];
    if (itemsRaw is List) {
      items.addAll(itemsRaw.map((e) => InvoiceItem.fromJson(e)));
    }

    return Invoice(
      id: json['id'] as int?,
      clientId: (json['client_id'] as num?)?.toInt(),
      invoiceNumber: (json['invoice_number'] as String?) ?? '',
      items: items,
      subtotal: (json['subtotal'] as num?)?.toDouble() ?? 0,
      taxPercent: (json['tax_percent'] as num?)?.toDouble() ?? 0,
      taxAmount: (json['tax_amount'] as num?)?.toDouble() ?? 0,
      discountPercent: (json['discount_percent'] as num?)?.toDouble() ?? 0,
      discountAmount: (json['discount_amount'] as num?)?.toDouble() ?? 0,
      total: (json['total'] as num?)?.toDouble() ?? 0,
      paidAmount: (json['paid_amount'] as num?)?.toDouble() ?? 0,
      status: (json['status'] as String?) ?? 'hutang',
      dueDate: json['due_date'] is DateTime
          ? json['due_date'] as DateTime?
          : DateTime.tryParse((json['due_date'] as String?) ?? ''),
      notes: json['notes'] as String?,
      clientName: (json['client'] is Map)
          ? ((json['client'] as Map)['name'] as String?) ?? ''
          : null,
      createdAt: json['created_at'] is DateTime
          ? json['created_at'] as DateTime?
          : DateTime.tryParse((json['created_at'] as String?) ?? ''),
    );
  }

  Map<String, dynamic> toMap() => {
        'client_id': clientId,
        'invoice_number': invoiceNumber,
        'items': items.map((e) => e.toMap()).toList(),
        'subtotal': subtotal,
        'tax_percent': taxPercent,
        'tax_amount': taxAmount,
        'discount_percent': discountPercent,
        'discount_amount': discountAmount,
        'total': total,
        'paid_amount': paidAmount,
        'status': status,
        'due_date': dueDate?.toIso8601String(),
        'notes': notes,
      };
}
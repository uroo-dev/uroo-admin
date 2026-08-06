class Client {
  final int? id;
  final String name;
  final String? email;
  final String? phone;
  final String? company;
  final String? notes;
  final String status;
  final DateTime? createdAt;

  const Client({
    this.id,
    required this.name,
    this.email,
    this.phone,
    this.company,
    this.notes,
    this.status = 'active',
    this.createdAt,
  });

  factory Client.fromJson(Map<String, dynamic> json) => Client(
        id: json['id'] as int?,
        name: (json['name'] as String?) ?? '',
        email: json['email'] as String?,
        phone: json['phone'] as String?,
        company: json['company'] as String?,
        notes: json['notes'] as String?,
        status: (json['status'] as String?) ?? 'active',
        createdAt: json['created_at'] is DateTime
            ? json['created_at'] as DateTime?
            : DateTime.tryParse((json['created_at'] as String?) ?? ''),
      );

  Map<String, dynamic> toMap() => {
        'name': name,
        'email': email,
        'phone': phone,
        'company': company,
        'notes': notes,
        'status': status,
      };
}
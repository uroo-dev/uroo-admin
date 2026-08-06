class SavingsGoal {
  final int? id;
  final String name;
  final double targetAmount;
  final double currentAmount;
  final String? icon;
  final String? color;
  final DateTime? deadline;
  final bool isCompleted;
  final String? notes;
  final DateTime? createdAt;

  const SavingsGoal({
    this.id,
    required this.name,
    required this.targetAmount,
    this.currentAmount = 0,
    this.icon,
    this.color,
    this.deadline,
    this.isCompleted = false,
    this.notes,
    this.createdAt,
  });

  static DateTime? _dt(dynamic v) =>
      v is DateTime ? v : DateTime.tryParse((v as String?) ?? '');

  double get progress =>
      targetAmount <= 0 ? 0 : (currentAmount / targetAmount).clamp(0, 1).toDouble();

  double get remaining => (targetAmount - currentAmount).clamp(0, double.infinity).toDouble();

  factory SavingsGoal.fromJson(Map<String, dynamic> json) => SavingsGoal(
        id: json['id'] as int?,
        name: (json['name'] as String?) ?? '',
        targetAmount: (json['target_amount'] as num?)?.toDouble() ?? 0,
        currentAmount: (json['current_amount'] as num?)?.toDouble() ?? 0,
        icon: json['icon'] as String?,
        color: json['color'] as String?,
        deadline: SavingsGoal._dt(json['deadline']),
        isCompleted: (json['is_completed'] as bool?) ?? false,
        notes: json['notes'] as String?,
        createdAt: SavingsGoal._dt(json['created_at']),
      );

  Map<String, dynamic> toMap() => {
        'name': name,
        'target_amount': targetAmount,
        'current_amount': currentAmount,
        'icon': icon,
        'color': color,
        'deadline': deadline?.toIso8601String(),
        'is_completed': isCompleted,
        'notes': notes,
      };
}

class SavingsTransaction {
  final int? id;
  final int goalId;
  final String type; // deposit | withdraw
  final double amount;
  final String? description;
  final DateTime? createdAt;

  const SavingsTransaction({
    this.id,
    required this.goalId,
    required this.type,
    required this.amount,
    this.description,
    this.createdAt,
  });

  factory SavingsTransaction.fromJson(Map<String, dynamic> json) => SavingsTransaction(
        id: json['id'] as int?,
        goalId: (json['goal_id'] as num?)?.toInt() ?? 0,
        type: (json['type'] as String?) ?? 'deposit',
        amount: (json['amount'] as num?)?.toDouble() ?? 0,
        description: json['description'] as String?,
        createdAt: json['created_at'] is DateTime
            ? json['created_at'] as DateTime?
            : DateTime.tryParse((json['created_at'] as String?) ?? ''),
      );
}
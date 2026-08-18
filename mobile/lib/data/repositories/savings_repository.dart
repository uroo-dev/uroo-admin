import 'package:drift/drift.dart';

import '../db/app_database.dart' as db;
import '../models/savings_goal.dart';

class SavingsRepository {
  final db.AppDatabase _db = db.AppDatabase.instance;

  Future<List<SavingsGoal>> goals() async {
    final rows = await (_db.select(_db.savingsGoals)
          ..where((t) => t.deletedAt.isNull())
          ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]))
        .get();

    return rows.map(_toModel).toList();
  }

  Future<SavingsGoal> createGoal(SavingsGoal goal) async {
    final now = DateTime.now();
    final id = await _db.nextTempId();

    await _db.into(_db.savingsGoals).insert(db.SavingsGoalsCompanion.insert(
          id: Value(id),
          name: goal.name,
          targetAmount: Value(goal.targetAmount),
          currentAmount: Value(goal.currentAmount),
          icon: Value(goal.icon),
          color: Value(goal.color),
          deadline: Value(goal.deadline),
          isCompleted: Value(goal.isCompleted),
          notes: Value(goal.notes),
          createdAt: now,
          updatedAt: now,
          dirty: const Value(true),
        ));

    return _toModel(db.SavingsGoal(
      localId: 0,
      id: id,
      name: goal.name,
      targetAmount: goal.targetAmount,
      currentAmount: goal.currentAmount,
      icon: goal.icon,
      color: goal.color,
      deadline: goal.deadline,
      isCompleted: goal.isCompleted,
      notes: goal.notes,
      createdAt: now,
      updatedAt: now,
      deletedAt: null,
      dirty: true,
    ));
  }

  Future<void> updateGoal(int id, SavingsGoal goal) async {
    final now = DateTime.now();

    await (_db.update(_db.savingsGoals)..where((t) => t.id.equals(id))).write(
      db.SavingsGoalsCompanion(
        name: Value(goal.name),
        targetAmount: Value(goal.targetAmount),
        currentAmount: Value(goal.currentAmount),
        icon: Value(goal.icon),
        color: Value(goal.color),
        deadline: Value(goal.deadline),
        isCompleted: Value(goal.isCompleted),
        notes: Value(goal.notes),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<void> deleteGoal(int id) async {
    final now = DateTime.now();

    await (_db.update(_db.savingsGoals)..where((t) => t.id.equals(id))).write(
      db.SavingsGoalsCompanion(
        deletedAt: Value(now),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  Future<List<SavingsTransaction>> transactions(int goalId) async {
    final rows = await (_db.select(_db.savingsTransactions)
          ..where((t) => t.goalId.equals(goalId) & t.deletedAt.isNull())
          ..orderBy([(t) => OrderingTerm.desc(t.createdAt)]))
        .get();

    return rows.map(_toTransaction).toList();
  }

  /// Deposits or withdraws from a goal, updating the running balance.
  Future<void> addTransaction(
      SavingsGoal goal, String type, double amount, String? description) async {
    final now = DateTime.now();
    final goalId = goal.id;

    await _db.into(_db.savingsTransactions).insert(
          db.SavingsTransactionsCompanion.insert(
            id: Value(await _db.nextTempId()),
            goalId: Value(goalId),
            type: type,
            amount: Value(amount),
            description: Value(description),
            createdAt: now,
            updatedAt: now,
            dirty: const Value(true),
          ),
        );

    final newBalance = type == 'withdraw'
        ? goal.currentAmount - amount
        : goal.currentAmount + amount;
    final completed = type == 'deposit' && newBalance >= goal.targetAmount;

    await (_db.update(_db.savingsGoals)..where((t) => t.id.equals(goalId!))).write(
      db.SavingsGoalsCompanion(
        currentAmount: Value(newBalance),
        isCompleted: Value(completed),
        updatedAt: Value(now),
        dirty: const Value(true),
      ),
    );
  }

  SavingsGoal _toModel(db.SavingsGoal r) => SavingsGoal(
        id: r.id,
        name: r.name,
        targetAmount: r.targetAmount,
        currentAmount: r.currentAmount,
        icon: r.icon,
        color: r.color,
        deadline: r.deadline,
        isCompleted: r.isCompleted,
        notes: r.notes,
        createdAt: r.createdAt,
      );

  SavingsTransaction _toTransaction(db.SavingsTransaction r) =>
      SavingsTransaction(
        id: r.id,
        goalId: r.goalId ?? 0,
        type: r.type,
        amount: r.amount,
        description: r.description,
        createdAt: r.createdAt,
      );
}
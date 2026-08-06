import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/savings_goal.dart';
import '../../data/repositories/savings_repository.dart';

class SavingsController extends AsyncNotifier<List<SavingsGoal>> {
  final _repo = SavingsRepository();

  @override
  Future<List<SavingsGoal>> build() => _repo.goals();

  Future<void> createGoal(SavingsGoal goal) async {
    await _repo.createGoal(goal);
    ref.invalidateSelf();
  }

  Future<void> updateGoal(int id, SavingsGoal goal) async {
    await _repo.updateGoal(id, goal);
    ref.invalidateSelf();
  }

  Future<void> deleteGoal(int id) async {
    await _repo.deleteGoal(id);
    ref.invalidateSelf();
  }

  Future<void> transact(SavingsGoal goal, String type, double amount,
      String? description) async {
    await _repo.addTransaction(goal, type, amount, description);
    ref.invalidateSelf();
  }
}

final savingsProvider =
    AsyncNotifierProvider<SavingsController, List<SavingsGoal>>(
  SavingsController.new,
);

final savingsTransactionsProvider =
    FutureProvider.family<List<SavingsTransaction>, int>((ref, goalId) async {
  return SavingsRepository().transactions(goalId);
});
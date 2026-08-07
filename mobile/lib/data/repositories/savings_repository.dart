import '../../core/supabase/supa.dart';
import '../../core/supabase/supabase_client.dart';
import '../models/savings_goal.dart';

class SavingsRepository {
  Future<List<SavingsGoal>> goals() async {
    final res = await supabase
        .from('savings_goals')
        .select()
        .order('created_at', ascending: false);
    return res.map((e) => SavingsGoal.fromJson(e)).toList();
  }

  Future<SavingsGoal> createGoal(SavingsGoal goal) async {
    final uid = await Supa.currentUserId();
    final res = await supabase
        .from('savings_goals')
        .insert({...goal.toMap(), 'user_id': uid})
        .select()
        .single();
    return SavingsGoal.fromJson(res);
  }

  Future<void> updateGoal(int id, SavingsGoal goal) async {
    await supabase.from('savings_goals').update(goal.toMap()).eq('id', id);
  }

  Future<void> deleteGoal(int id) async {
    await supabase.from('savings_goals').delete().eq('id', id);
  }

  Future<List<SavingsTransaction>> transactions(int goalId) async {
    final res = await supabase
        .from('savings_transactions')
        .select()
        .eq('goal_id', goalId)
        .order('created_at', ascending: false);
    return res.map((e) => SavingsTransaction.fromJson(e)).toList();
  }

  /// Deposits or withdraws from a goal, updating the running balance.
  Future<void> addTransaction(SavingsGoal goal, String type, double amount,
      String? description) async {
    await supabase.from('savings_transactions').insert({
      'goal_id': goal.id,
      'type': type,
      'amount': amount,
      'description': description,
    });

    final newBalance = type == 'withdraw'
        ? goal.currentAmount - amount
        : goal.currentAmount + amount;
    final completed =
        type == 'deposit' && newBalance >= goal.targetAmount;

    await supabase.from('savings_goals').update({
      'current_amount': newBalance,
      'is_completed': completed,
    }).eq('id', goal.id!);
  }
}
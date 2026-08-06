import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../data/models/invoice.dart';
import '../../data/models/savings_goal.dart';
import '../../features/brain_dumps/brain_dumps_controller.dart';
import '../../features/clients/clients_controller.dart';
import '../../features/ideas/ideas_controller.dart';
import '../../features/invoices/invoices_controller.dart';
import '../../features/notes/notes_controller.dart';
import '../../features/projects/projects_controller.dart';
import '../../features/savings/savings_controller.dart';

class DashboardData {
  final int activeProjects;
  final int pendingInvoices;
  final int totalClients;
  final int totalNotes;
  final int totalIdeas;
  final double totalSavings;
  final List<Invoice> recentInvoices;
  final List<SavingsGoal> recentSavings;

  DashboardData({
    required this.activeProjects,
    required this.pendingInvoices,
    required this.totalClients,
    required this.totalNotes,
    required this.totalIdeas,
    required this.totalSavings,
    required this.recentInvoices,
    required this.recentSavings,
  });
}

final dashboardProvider = FutureProvider<DashboardData>((ref) async {
  final projects = await ref.watch(projectsProvider.future);
  final invoices = await ref.watch(invoicesProvider.future);
  final clients = await ref.watch(clientsProvider.future);
  final notes = await ref.watch(notesProvider.future);
  final ideas = await ref.watch(ideasProvider.future);
  final savings = await ref.watch(savingsProvider.future);

  final activeProjects = projects
      .where((p) => p.status != 'completed' && p.status != 'archived')
      .length;
  final pendingInvoices =
      invoices.where((i) => i.status == 'hutang').length;
  final totalSavings =
      savings.fold(0.0, (sum, g) => sum + g.currentAmount);

  final recentInvoices = [...invoices].take(5).toList();
  final recentSavings = [...savings].take(5).toList();

  return DashboardData(
    activeProjects: activeProjects,
    pendingInvoices: pendingInvoices,
    totalClients: clients.length,
    totalNotes: notes.length,
    totalIdeas: ideas.length,
    totalSavings: totalSavings,
    recentInvoices: recentInvoices,
    recentSavings: recentSavings,
  );
});
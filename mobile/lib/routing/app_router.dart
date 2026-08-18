import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../core/session/session_store.dart';
import '../features/auth/login_screen.dart';
import '../features/auth/register_screen.dart';
import '../features/brain_dumps/brain_dump_form_screen.dart';
import '../features/brain_dumps/brain_dumps_screen.dart';
import '../features/clients/client_form_screen.dart';
import '../features/clients/clients_screen.dart';
import '../features/dashboard/dashboard_screen.dart';
import '../features/ideas/idea_form_screen.dart';
import '../features/ideas/ideas_screen.dart';
import '../features/invoices/invoice_detail_screen.dart';
import '../features/invoices/invoice_form_screen.dart';
import '../features/invoices/invoices_screen.dart';
import '../features/notes/note_form_screen.dart';
import '../features/notes/notes_screen.dart';
import '../features/projects/project_form_screen.dart';
import '../features/projects/projects_screen.dart';
import '../features/savings/savings_detail_screen.dart';
import '../features/savings/savings_form_screen.dart';
import '../features/savings/savings_screen.dart';
import '../features/settings/settings_screen.dart';
import '../features/shell/main_shell.dart';

/// Fires whenever auth state changes so the router re-evaluates redirects.
final ValueNotifier<int> authRefresh = ValueNotifier<int>(0);

final _shellBranches = [
  StatefulShellBranch(routes: [GoRoute(path: '/', builder: (_, __) => const DashboardScreen())]),
  StatefulShellBranch(routes: [GoRoute(path: '/notes', builder: (_, __) => const NotesScreen())]),
  StatefulShellBranch(routes: [GoRoute(path: '/ideas', builder: (_, __) => const IdeasScreen())]),
  StatefulShellBranch(routes: [GoRoute(path: '/savings', builder: (_, __) => const SavingsScreen())]),
  StatefulShellBranch(routes: [GoRoute(path: '/invoices', builder: (_, __) => const InvoicesScreen())]),
];

GoRouter buildRouter() {
  return GoRouter(
    initialLocation: '/',
    refreshListenable: currentSession,
    redirect: (context, state) {
      final session = currentSession.value;
      final at =
          state.matchedLocation == '/login' || state.matchedLocation == '/register';
      if (session == null) return at ? null : '/login';
      return at ? '/' : null;
    },
    routes: [
      GoRoute(path: '/login', builder: (_, __) => const LoginScreen()),
      GoRoute(path: '/register', builder: (_, __) => const RegisterScreen()),
      StatefulShellRoute.indexedStack(
        builder: (context, state, navigationShell) =>
            MainShell(navigationShell: navigationShell),
        branches: _shellBranches,
      ),
      GoRoute(path: '/brain-dumps', builder: (_, __) => const BrainDumpsScreen()),
      GoRoute(path: '/brain-dumps/new', builder: (_, __) => const BrainDumpFormScreen()),
      GoRoute(
          path: '/brain-dumps/edit/:id',
          builder: (_, s) => BrainDumpFormScreen(id: int.parse(s.pathParameters['id']!))),
      GoRoute(path: '/clients', builder: (_, __) => const ClientsScreen()),
      GoRoute(path: '/clients/new', builder: (_, __) => const ClientFormScreen()),
      GoRoute(
          path: '/clients/edit/:id',
          builder: (_, s) => ClientFormScreen(id: int.parse(s.pathParameters['id']!))),
      GoRoute(path: '/projects', builder: (_, __) => const ProjectsScreen()),
      GoRoute(path: '/projects/new', builder: (_, __) => const ProjectFormScreen()),
      GoRoute(
          path: '/projects/edit/:id',
          builder: (_, s) => ProjectFormScreen(id: int.parse(s.pathParameters['id']!))),
      GoRoute(path: '/notes/new', builder: (_, __) => const NoteFormScreen()),
      GoRoute(
          path: '/notes/edit/:id',
          builder: (_, s) => NoteFormScreen(id: int.parse(s.pathParameters['id']!))),
      GoRoute(path: '/ideas/new', builder: (_, __) => const IdeaFormScreen()),
      GoRoute(
          path: '/ideas/edit/:id',
          builder: (_, s) => IdeaFormScreen(id: int.parse(s.pathParameters['id']!))),
      GoRoute(path: '/savings/new', builder: (_, __) => const SavingsFormScreen()),
      GoRoute(
          path: '/savings/:id',
          builder: (_, s) => SavingsDetailScreen(id: int.parse(s.pathParameters['id']!))),
      GoRoute(path: '/invoices/new', builder: (_, __) => const InvoiceFormScreen()),
      GoRoute(
          path: '/invoices/:id',
          builder: (_, s) => InvoiceDetailScreen(id: int.parse(s.pathParameters['id']!))),
      GoRoute(path: '/settings', builder: (_, __) => const SettingsScreen()),
    ],
  );
}
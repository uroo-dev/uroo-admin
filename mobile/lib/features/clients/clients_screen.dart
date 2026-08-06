import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import 'clients_controller.dart';

class ClientsScreen extends ConsumerWidget {
  const ClientsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final clients = ref.watch(clientsProvider);

    return NeoScaffold(
      title: 'Clients',
      showBack: false,
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.purpleAccent,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        onPressed: () => context.push('/clients/new'),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
      body: clients.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) =>
            NeoEmptyState(title: 'Gagal memuat', message: '$e', icon: Icons.error_outline),
        data: (list) {
          if (list.isEmpty) {
            return const NeoEmptyState(
              title: 'Belum ada client',
              message: 'Tambahkan client pertama kamu.',
              icon: Icons.people_alt_outlined,
            );
          }
          return ListView.builder(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
            itemCount: list.length,
            itemBuilder: (context, i) {
              final client = list[i];
              return Padding(
                padding: const EdgeInsets.only(bottom: 16),
                child: NeoCard(
                  onTap: () => context.push('/clients/edit/${client.id}'),
                  child: Row(
                    children: [
                      Container(
                        width: 52,
                        height: 52,
                        decoration: BoxDecoration(
                          color: AppColors.purpleAccent,
                          shape: BoxShape.circle,
                          border: Border.all(color: AppColors.borderDark, width: 3),
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          client.name.isNotEmpty
                              ? client.name[0].toUpperCase()
                              : '?',
                          style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w800,
                              fontSize: 20),
                        ),
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              client.name,
                              style: const TextStyle(
                                  fontSize: 16, fontWeight: FontWeight.w800),
                            ),
                            if (client.company != null &&
                                client.company!.isNotEmpty)
                              Text(
                                client.company!,
                                style: const TextStyle(
                                    color: AppColors.txtSecondary, fontSize: 13),
                              ),
                            if (client.phone != null && client.phone!.isNotEmpty)
                              Text(
                                client.phone!,
                                style: const TextStyle(
                                    color: AppColors.txtSecondary, fontSize: 12),
                              ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }
}
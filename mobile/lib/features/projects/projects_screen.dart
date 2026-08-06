import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import 'projects_controller.dart';

class ProjectsScreen extends ConsumerWidget {
  const ProjectsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final projects = ref.watch(projectsProvider);

    return NeoScaffold(
      title: 'Projects',
      showBack: false,
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.cyanAccent,
        foregroundColor: AppColors.borderDark,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        onPressed: () => context.push('/projects/new'),
        child: const Icon(Icons.add, size: 28),
      ),
      body: projects.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) =>
            NeoEmptyState(title: 'Gagal memuat', message: '$e', icon: Icons.error_outline),
        data: (list) {
          if (list.isEmpty) {
            return const NeoEmptyState(
              title: 'Belum ada project',
              message: 'Mulai project pertamamu.',
              icon: Icons.folder_copy_outlined,
            );
          }
          return ListView.builder(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
            itemCount: list.length,
            itemBuilder: (context, i) {
              final p = list[i];
              return Padding(
                padding: const EdgeInsets.only(bottom: 16),
                child: NeoCard(
                  onTap: () => context.push('/projects/edit/${p.id}'),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              p.name,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                  fontSize: 17, fontWeight: FontWeight.w800),
                            ),
                          ),
                          NeoBadge(
                            label: p.status,
                            color: AppColors.statusColor(p.status),
                          ),
                        ],
                      ),
                      if (p.clientName != null && p.clientName!.isNotEmpty) ...[
                        const SizedBox(height: 4),
                        Text(
                          'Client: ${p.clientName}',
                          style: const TextStyle(
                              color: AppColors.txtSecondary, fontSize: 13),
                        ),
                      ],
                      const SizedBox(height: 12),
                      Row(
                        children: [
                          Expanded(
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(10),
                              child: LinearProgressIndicator(
                                value: p.progress / 100,
                                minHeight: 14,
                                backgroundColor: AppColors.surfaceAlt,
                                valueColor: const AlwaysStoppedAnimation(
                                    AppColors.primary),
                              ),
                            ),
                          ),
                          const SizedBox(width: 10),
                          Text('${p.progress}%',
                              style: const TextStyle(fontWeight: FontWeight.w700)),
                        ],
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
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import 'ideas_controller.dart';

class IdeasScreen extends ConsumerWidget {
  const IdeasScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final ideas = ref.watch(ideasProvider);

    return NeoScaffold(
      title: 'Ide Aplikasi',
      showBack: false,
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.primary,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        onPressed: () => context.push('/ideas/new'),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
      body: refreshable(ideas, (list) {
        if (list.isEmpty) {
          return const NeoEmptyState(
            title: 'Belum ada ide',
            message: 'Tangkap ide aplikasi kamu, lalu wujudkan!',
            icon: Icons.lightbulb_outline,
          );
        }
        return ListView.builder(
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
          itemCount: list.length,
          itemBuilder: (context, i) {
            final idea = list[i];
            return Padding(
              padding: const EdgeInsets.only(bottom: 16),
              child: NeoCard(
                padding: const EdgeInsets.all(18),
                onTap: () => context.push('/ideas/edit/${idea.id}'),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: Text(
                            idea.name,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                                fontSize: 18, fontWeight: FontWeight.w800),
                          ),
                        ),
                        NeoBadge(
                          label: idea.priority,
                          color: AppColors.statusColor(idea.priority),
                        ),
                      ],
                    ),
                    if (idea.tagline != null && idea.tagline!.isNotEmpty) ...[
                      const SizedBox(height: 6),
                      Text(
                        idea.tagline!,
                        style: const TextStyle(
                            color: AppColors.txtSecondary, fontSize: 14),
                      ),
                    ],
                    if (idea.description != null && idea.description!.isNotEmpty) ...[
                      const SizedBox(height: 6),
                      Text(
                        idea.description!,
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                            color: AppColors.txtSecondary, fontSize: 13),
                      ),
                    ],
                    const SizedBox(height: 10),
                    Wrap(
                      spacing: 6,
                      runSpacing: 6,
                      children: [
                        NeoBadge(label: idea.status, color: AppColors.statusColor(idea.status)),
                        NeoBadge(label: idea.platform, color: AppColors.secondary),
                        for (final t in idea.techStack.take(2)) _TechChip(label: t),
                      ],
                    ),
                  ],
                ),
              ),
            );
          },
        );
      }),
    );
  }
}

/// Tiny helper to render AsyncValue with pull-to-refresh.
Widget refreshable<T>(
  AsyncValue<T> value,
  Widget Function(T data) builder,
) {
  return value.when(
    loading: () => const Center(child: CircularProgressIndicator()),
    error: (e, _) => NeoEmptyState(
      title: 'Gagal memuat',
      message: '$e',
      icon: Icons.error_outline,
    ),
    data: builder,
  );
}

class _TechChip extends StatelessWidget {
  final String label;

  const _TechChip({required this.label});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: AppColors.primary.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        label,
        style: const TextStyle(
          color: AppColors.primary,
          fontSize: 11,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}
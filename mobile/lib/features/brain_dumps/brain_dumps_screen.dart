import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import 'brain_dumps_controller.dart';

class BrainDumpsScreen extends ConsumerWidget {
  const BrainDumpsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dumps = ref.watch(brainDumpsProvider);

    return NeoScaffold(
      title: 'Brain Dumps',
      showBack: false,
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.pinkAccent,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        onPressed: () => context.push('/brain-dumps/new'),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
      body: dumps.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, _) =>
            NeoEmptyState(title: 'Gagal memuat', message: '$e', icon: Icons.error_outline),
        data: (list) {
          final sorted = [...list]..sort((a, b) {
              if (a.isPinned != b.isPinned) return a.isPinned ? -1 : 1;
              return (b.createdAt ?? DateTime(0))
                  .compareTo(a.createdAt ?? DateTime(0));
            });

          if (sorted.isEmpty) {
            return const NeoEmptyState(
              title: 'Pikiran berantakan?',
              message: 'Tuangkan semua isi kepalamu di sini.',
              icon: Icons.psychology_outlined,
            );
          }

          return ListView.builder(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
            itemCount: sorted.length,
            itemBuilder: (context, i) {
              final d = sorted[i];
              return Padding(
                padding: const EdgeInsets.only(bottom: 16),
                child: NeoCard(
                  padding: const EdgeInsets.all(18),
                  onTap: () => context.push('/brain-dumps/edit/${d.id}'),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          if (d.isPinned) ...[
                            const Icon(Icons.push_pin,
                                color: AppColors.warning, size: 20),
                            const SizedBox(width: 8),
                          ],
                          Expanded(
                            child: Text(
                              d.content,
                              maxLines: 4,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                  fontSize: 15, fontWeight: FontWeight.w600),
                            ),
                          ),
                          IconButton(
                            icon: const Icon(Icons.inventory_2_outlined,
                                color: AppColors.txtSecondary),
                            onPressed: () =>
                                ref.read(brainDumpsProvider.notifier).archive(d.id!, true),
                          ),
                        ],
                      ),
                      const SizedBox(height: 6),
                      Text(
                        FormatUtil.relative(d.createdAt),
                        style: const TextStyle(
                            color: AppColors.txtSecondary, fontSize: 12),
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
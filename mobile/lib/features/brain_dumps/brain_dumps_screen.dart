import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/brain_dump.dart';
import 'brain_dumps_controller.dart';

class BrainDumpsScreen extends ConsumerStatefulWidget {
  const BrainDumpsScreen({super.key});

  @override
  ConsumerState<BrainDumpsScreen> createState() => _BrainDumpsScreenState();
}

class _BrainDumpsScreenState extends ConsumerState<BrainDumpsScreen> {
  final _quick = TextEditingController();
  final _search = TextEditingController();

  @override
  void dispose() {
    _quick.dispose();
    _search.dispose();
    super.dispose();
  }

  Future<void> _quickAdd() async {
    final content = _quick.text.trim();
    if (content.isEmpty) return;
    setState(() => _quick.clear());
    await ref.read(brainDumpsProvider.notifier).create(BrainDump(content: content));
  }

  @override
  Widget build(BuildContext context) {
    final dumps = ref.watch(brainDumpsProvider);

    return NeoScaffold(
      title: 'Brain Dumps',
      showBack: false,
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.primary,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        onPressed: () => context.push('/brain-dumps/new'),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                NeoCard(
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      TextField(
                        controller: _quick,
                        maxLines: 3,
                        minLines: 1,
                        decoration: InputDecoration(
                          hintText: 'Langsung tulis isi kepalamu di sini...',
                          filled: true,
                          fillColor: AppColors.surface,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: const BorderSide(
                                color: AppColors.borderDark, width: 4),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: const BorderSide(
                                color: AppColors.borderDark, width: 4),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(16),
                            borderSide: const BorderSide(
                                color: AppColors.primary, width: 4),
                          ),
                        ),
                      ),
                      const SizedBox(height: 10),
                      Align(
                        alignment: Alignment.centerRight,
                        child: NeoButton(
                          label: 'Dump it!',
                          variant: NeoButtonVariant.primary,
                          icon: Icons.psychology,
                          height: 44,
                          onPressed: _quickAdd,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                TextField(
                  controller: _search,
                  onChanged: (_) => setState(() {}),
                  decoration: InputDecoration(
                    hintText: 'Cari...',
                    prefixIcon: const Icon(Icons.search),
                    filled: true,
                    fillColor: AppColors.surface,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: const BorderSide(color: AppColors.borderDark, width: 4),
                    ),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: const BorderSide(color: AppColors.borderDark, width: 4),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: const BorderSide(color: AppColors.primary, width: 4),
                    ),
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: dumps.when(
              loading: () => const Center(child: CircularProgressIndicator()),
              error: (e, _) => NeoEmptyState(
                  title: 'Gagal memuat',
                  message: '$e',
                  icon: Icons.error_outline),
              data: (list) {
                final q = _search.text.toLowerCase();
                final sorted = list.where((d) {
                  return q.isEmpty || d.content.toLowerCase().contains(q);
                }).toList()
                  ..sort((a, b) {
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
                  padding: const EdgeInsets.fromLTRB(16, 0, 16, 96),
                  itemCount: sorted.length,
                  itemBuilder: (context, i) {
                    final d = sorted[i];
                    final pinned = d.isPinned;
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 16),
                      child: NeoCard(
                        color: pinned ? const Color(0xFFFFFBEB) : AppColors.surface,
                        padding: const EdgeInsets.all(18),
                        onTap: () => context.push('/brain-dumps/edit/${d.id}'),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    d.content,
                                    maxLines: 4,
                                    overflow: TextOverflow.ellipsis,
                                    style: const TextStyle(
                                        fontSize: 14, fontWeight: FontWeight.w600),
                                  ),
                                ),
                                if (pinned) ...[
                                  const NeoBadge(
                                      label: 'Pinned', color: AppColors.warning),
                                  const SizedBox(width: 4),
                                ],
                                IconButton(
                                  icon: Icon(
                                    pinned
                                        ? Icons.push_pin
                                        : Icons.push_pin_outlined,
                                    color: pinned
                                        ? AppColors.warning
                                        : AppColors.txtSecondary,
                                  ),
                                  onPressed: () => ref
                                      .read(brainDumpsProvider.notifier)
                                      .togglePin(d.id!, !pinned),
                                ),
                                IconButton(
                                  icon: const Icon(Icons.inventory_2_outlined,
                                      color: AppColors.txtSecondary),
                                  onPressed: () => ref
                                      .read(brainDumpsProvider.notifier)
                                      .archive(d.id!, true),
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
          ),
        ],
      ),
    );
  }
}
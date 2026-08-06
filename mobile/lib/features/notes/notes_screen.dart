import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import 'notes_controller.dart';

class NotesScreen extends ConsumerStatefulWidget {
  const NotesScreen({super.key});

  @override
  ConsumerState<NotesScreen> createState() => _NotesScreenState();
}

class _NotesScreenState extends ConsumerState<NotesScreen> {
  final _search = TextEditingController();
  bool _showPinnedOnly = false;

  @override
  void dispose() {
    _search.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final notes = ref.watch(notesProvider);

    return NeoScaffold(
      title: 'Catatan',
      showBack: false,
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.primary,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(18),
          side: const BorderSide(color: AppColors.borderDark, width: 4),
        ),
        onPressed: () => context.push('/notes/new'),
        child: const Icon(Icons.add, color: Colors.white, size: 28),
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              controller: _search,
              onChanged: (_) => setState(() {}),
              decoration: InputDecoration(
                hintText: 'Cari catatan...',
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
              ),
            ),
          ),
          Expanded(
            child: notes.when(
              loading: () => const Center(child: CircularProgressIndicator()),
              error: (e, _) => Center(
                child: NeoEmptyState(
                  title: 'Gagal memuat',
                  message: '$e',
                  icon: Icons.error_outline,
                ),
              ),
              data: (list) {
                final filtered = list
                    .where((n) => !_showPinnedOnly || n.isPinned)
                    .where((n) {
                  final q = _search.text.toLowerCase();
                  return q.isEmpty ||
                      n.title.toLowerCase().contains(q) ||
                      n.content.toLowerCase().contains(q);
                }).toList();

                if (filtered.isEmpty) {
                  return const NeoEmptyState(
                    title: 'Belum ada catatan',
                    message: 'Tekan + untuk membuat catatan baru.',
                    icon: Icons.sticky_note_2_outlined,
                  );
                }

                return ListView.builder(
                  padding: const EdgeInsets.fromLTRB(16, 0, 16, 96),
                  itemCount: filtered.length,
                  itemBuilder: (context, i) {
                    final note = filtered[i];
                    return _NoteCard(
                      title: note.title,
                      content: note.content,
                      tags: note.tags,
                      pinned: note.isPinned,
                      favorite: note.isFavorite,
                      onTap: () => context.push('/notes/edit/${note.id}'),
                      onTogglePin: () => ref
                          .read(notesProvider.notifier)
                          .togglePin(note.id!, !note.isPinned),
                      onToggleFavorite: () => ref
                          .read(notesProvider.notifier)
                          .toggleFavorite(note.id!, !note.isFavorite),
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

class _NoteCard extends StatelessWidget {
  final String title;
  final String content;
  final List<String> tags;
  final bool pinned;
  final bool favorite;
  final VoidCallback onTap;
  final VoidCallback onTogglePin;
  final VoidCallback onToggleFavorite;

  const _NoteCard({
    required this.title,
    required this.content,
    required this.tags,
    required this.pinned,
    required this.favorite,
    required this.onTap,
    required this.onTogglePin,
    required this.onToggleFavorite,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: NeoCard(
        onTap: onTap,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(
                    title,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800),
                  ),
                ),
                IconButton(
                  onPressed: onTogglePin,
                  icon: Icon(
                    pinned ? Icons.push_pin : Icons.push_pin_outlined,
                    color: pinned ? AppColors.warning : AppColors.txtSecondary,
                  ),
                ),
                IconButton(
                  onPressed: onToggleFavorite,
                  icon: Icon(
                    favorite ? Icons.star : Icons.star_outline,
                    color: favorite ? AppColors.warning : AppColors.txtSecondary,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 6),
            Text(
              content,
              maxLines: 3,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(color: AppColors.txtSecondary, fontSize: 14),
            ),
            if (tags.isNotEmpty) ...[
              const SizedBox(height: 10),
              Wrap(
                spacing: 6,
                children: tags
                    .map((t) => NeoBadge(label: t, color: AppColors.secondary))
                    .toList(),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
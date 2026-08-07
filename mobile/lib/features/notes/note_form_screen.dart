import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_input.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/note.dart';
import 'notes_controller.dart';

class NoteFormScreen extends ConsumerStatefulWidget {
  final int? id;

  const NoteFormScreen({super.key, this.id});

  @override
  ConsumerState<NoteFormScreen> createState() => _NoteFormScreenState();
}

class _NoteFormScreenState extends ConsumerState<NoteFormScreen> {
  final _title = TextEditingController();
  final _content = TextEditingController();
  final _tags = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  bool _pinned = false;
  bool _favorite = false;
  bool _saving = false;

  bool get _isEdit => widget.id != null;

  @override
  void initState() {
    super.initState();
    if (_isEdit) {
      final notes = ref.read(notesProvider).value ?? [];
      final note = notes.where((n) => n.id == widget.id).firstOrNull;
      if (note != null) {
        _title.text = note.title;
        _content.text = note.content;
        _tags.text = note.tags.join(', ');
        _pinned = note.isPinned;
        _favorite = note.isFavorite;
      }
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);

    final note = Note(
      id: widget.id,
      title: _title.text.trim(),
      content: _content.text.trim(),
      tags: _tags.text
          .split(',')
          .map((e) => e.trim())
          .where((e) => e.isNotEmpty)
          .toList(),
      isPinned: _pinned,
      isFavorite: _favorite,
    );

    final controller = ref.read(notesProvider.notifier);
    try {
      if (_isEdit) {
        await controller.updateItem(widget.id!, note);
      } else {
        await controller.create(note);
      }
      if (mounted) context.pop();
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  Future<void> _delete() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus catatan?'),
        content: const Text('Tindakan ini tidak bisa dibatalkan.'),
        actions: [
          TextButton(onPressed: () => context.pop(false), child: const Text('Batal')),
          TextButton(onPressed: () => context.pop(true), child: const Text('Hapus')),
        ],
      ),
    );
    if (confirmed == true && widget.id != null) {
      await ref.read(notesProvider.notifier).delete(widget.id!);
      if (mounted) context.pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: _isEdit ? 'Edit Catatan' : 'Catatan Baru',
      actions: _isEdit
          ? [
              IconButton(
                onPressed: _delete,
                icon: const Icon(Icons.delete_outline, color: AppColors.danger),
              ),
            ]
          : null,
      body: NeoPage(
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              NeoInput(
                label: 'Judul',
                controller: _title,
                validator: (v) => (v == null || v.trim().isEmpty) ? 'Judul wajib diisi' : null,
              ),
              const SizedBox(height: 16),
              NeoInput(
                label: 'Isi catatan',
                controller: _content,
                maxLines: 8,
              ),
              const SizedBox(height: 16),
              NeoInput(
                label: 'Tags (pisahkan dengan koma)',
                controller: _tags,
              ),
              const SizedBox(height: 20),
              Wrap(
                spacing: 24,
                children: [
                  FilterChip(
                    selected: _pinned,
                    label: const Text('Pin'),
                    onSelected: (v) => setState(() => _pinned = v),
                  ),
                  FilterChip(
                    selected: _favorite,
                    label: const Text('Favorit'),
                    onSelected: (v) => setState(() => _favorite = v),
                  ),
                ],
              ),
              const SizedBox(height: 28),
              NeoButton(
                label: _isEdit ? 'Simpan Perubahan' : 'Simpan',
                icon: Icons.save,
                expanded: true,
                loading: _saving,
                onPressed: _save,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
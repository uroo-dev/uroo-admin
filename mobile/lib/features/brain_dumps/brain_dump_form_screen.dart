import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_input.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/brain_dump.dart';
import 'brain_dumps_controller.dart';

class BrainDumpFormScreen extends ConsumerStatefulWidget {
  final int? id;

  const BrainDumpFormScreen({super.key, this.id});

  @override
  ConsumerState<BrainDumpFormScreen> createState() => _BrainDumpFormScreenState();
}

class _BrainDumpFormScreenState extends ConsumerState<BrainDumpFormScreen> {
  final _content = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  bool _pinned = false;
  bool _saving = false;

  bool get _isEdit => widget.id != null;

  @override
  void initState() {
    super.initState();
    if (_isEdit) {
      final dumps = ref.read(brainDumpsProvider).value ?? [];
      final d = dumps.where((e) => e.id == widget.id).firstOrNull;
      if (d != null) {
        _content.text = d.content;
        _pinned = d.isPinned;
      }
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    final dump = BrainDump(
      id: widget.id,
      content: _content.text.trim(),
      isPinned: _pinned,
    );
    final controller = ref.read(brainDumpsProvider.notifier);
    try {
      if (_isEdit) {
        await controller.update(widget.id!, dump);
      } else {
        await controller.create(dump);
      }
      if (mounted) context.pop();
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  Future<void> _delete() async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Hapus?'),
        actions: [
          TextButton(onPressed: () => c.pop(false), child: const Text('Batal')),
          TextButton(onPressed: () => c.pop(true), child: const Text('Hapus')),
        ],
      ),
    );
    if (ok == true && widget.id != null) {
      await ref.read(brainDumpsProvider.notifier).delete(widget.id!);
      if (mounted) context.pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: _isEdit ? 'Edit Brain Dump' : 'Brain Dump',
      actions: _isEdit
          ? [
              IconButton(
                onPressed: _delete,
                icon: const Icon(Icons.delete_outline),
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
                label: 'Isi pikiranmu',
                controller: _content,
                maxLines: 8,
                validator: (v) =>
                    (v == null || v.trim().isEmpty) ? 'Tidak boleh kosong' : null,
              ),
              const SizedBox(height: 20),
              FilterChip(
                selected: _pinned,
                label: const Text('Pin di atas'),
                onSelected: (v) => setState(() => _pinned = v),
              ),
              const SizedBox(height: 28),
              NeoButton(
                label: _isEdit ? 'Simpan Perubahan' : 'Simpan',
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
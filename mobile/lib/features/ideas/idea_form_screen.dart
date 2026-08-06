import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_input.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/app_idea.dart';
import 'ideas_controller.dart';

class IdeaFormScreen extends ConsumerStatefulWidget {
  final int? id;

  const IdeaFormScreen({super.key, this.id});

  @override
  ConsumerState<IdeaFormScreen> createState() => _IdeaFormScreenState();
}

class _IdeaFormScreenState extends ConsumerState<IdeaFormScreen> {
  final _name = TextEditingController();
  final _tagline = TextEditingController();
  final _description = TextEditingController();
  final _techStack = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  bool _saving = false;

  String _status = 'draft';
  String _priority = 'medium';
  String _platform = 'web';

  bool get _isEdit => widget.id != null;

  @override
  void initState() {
    super.initState();
    if (_isEdit) {
      final ideas = ref.read(ideasProvider).value ?? [];
      final idea = ideas.where((e) => e.id == widget.id).firstOrNull;
      if (idea != null) {
        _name.text = idea.name;
        _tagline.text = idea.tagline ?? '';
        _description.text = idea.description ?? '';
        _techStack.text = idea.techStack.join(', ');
        _status = idea.status;
        _priority = idea.priority;
        _platform = idea.platform;
      }
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);

    final idea = AppIdea(
      id: widget.id,
      name: _name.text.trim(),
      tagline: _tagline.text.trim().isEmpty ? null : _tagline.text.trim(),
      description:
          _description.text.trim().isEmpty ? null : _description.text.trim(),
      techStack: _techStack.text
          .split(',')
          .map((e) => e.trim())
          .where((e) => e.isNotEmpty)
          .toList(),
      status: _status,
      priority: _priority,
      platform: _platform,
    );

    final controller = ref.read(ideasProvider.notifier);
    try {
      if (_isEdit) {
        await controller.update(widget.id!, idea);
      } else {
        await controller.create(idea);
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
        title: const Text('Hapus ide?'),
        content: const Text('Tindakan ini tidak bisa dibatalkan.'),
        actions: [
          TextButton(onPressed: () => c.pop(false), child: const Text('Batal')),
          TextButton(onPressed: () => c.pop(true), child: const Text('Hapus')),
        ],
      ),
    );
    if (ok == true && widget.id != null) {
      await ref.read(ideasProvider.notifier).delete(widget.id!);
      if (mounted) context.pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: _isEdit ? 'Edit Ide' : 'Ide Baru',
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
                label: 'Nama aplikasi',
                controller: _name,
                validator: (v) => (v == null || v.trim().isEmpty) ? 'Nama wajib diisi' : null,
              ),
              const SizedBox(height: 16),
              NeoInput(label: 'Tagline', controller: _tagline),
              const SizedBox(height: 16),
              NeoInput(label: 'Deskripsi', controller: _description, maxLines: 4),
              const SizedBox(height: 16),
              NeoInput(
                label: 'Tech stack (pisahkan dengan koma)',
                controller: _techStack,
              ),
              const SizedBox(height: 20),
              _dropdownRow('Status', ['draft', 'idea', 'production'], _status,
                  (v) => setState(() => _status = v!)),
              const SizedBox(height: 16),
              _dropdownRow('Prioritas', ['low', 'medium', 'high'], _priority,
                  (v) => setState(() => _priority = v!)),
              const SizedBox(height: 16),
              _dropdownRow('Platform', ['web', 'mobile', 'desktop', 'cli'], _platform,
                  (v) => setState(() => _platform = v!)),
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

  Widget _dropdownRow(String label, List<String> options, String current,
      ValueChanged<String?> onChanged) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        const SizedBox(height: 8),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12),
          decoration: BoxDecoration(
            color: AppColors.surface,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppColors.borderDark, width: 4),
          ),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<String>(
              value: current,
              isExpanded: true,
              items: options
                  .map((o) => DropdownMenuItem(value: o, child: Text(o)))
                  .toList(),
              onChanged: onChanged,
            ),
          ),
        ),
      ],
    );
  }
}
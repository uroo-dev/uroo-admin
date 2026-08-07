import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_input.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/client.dart';
import '../../data/models/project.dart';
import '../../data/repositories/client_repository.dart';
import 'projects_controller.dart';

class ProjectFormScreen extends ConsumerStatefulWidget {
  final int? id;

  const ProjectFormScreen({super.key, this.id});

  @override
  ConsumerState<ProjectFormScreen> createState() => _ProjectFormScreenState();
}

class _ProjectFormScreenState extends ConsumerState<ProjectFormScreen> {
  final _name = TextEditingController();
  final _description = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  int? _clientId;
  String _status = 'development';
  int _progress = 0;
  bool _saving = false;
  List<Client> _clients = [];

  bool get _isEdit => widget.id != null;

  @override
  void initState() {
    super.initState();
    ClientRepository().list().then((list) {
      if (mounted) setState(() => _clients = list);
    });
    if (_isEdit) {
      final projects = ref.read(projectsProvider).value ?? [];
      final p = projects.where((e) => e.id == widget.id).firstOrNull;
      if (p != null) {
        _name.text = p.name;
        _description.text = p.description ?? '';
        _clientId = p.clientId;
        _status = p.status;
        _progress = p.progress;
      }
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    final project = Project(
      id: widget.id,
      clientId: _clientId,
      name: _name.text.trim(),
      description:
          _description.text.trim().isEmpty ? null : _description.text.trim(),
      status: _status,
      progress: _progress,
    );
    final controller = ref.read(projectsProvider.notifier);
    try {
      if (_isEdit) {
        await controller.updateItem(widget.id!, project);
      } else {
        await controller.create(project);
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
        title: const Text('Hapus project?'),
        actions: [
          TextButton(onPressed: () => c.pop(false), child: const Text('Batal')),
          TextButton(onPressed: () => c.pop(true), child: const Text('Hapus')),
        ],
      ),
    );
    if (ok == true && widget.id != null) {
      await ref.read(projectsProvider.notifier).delete(widget.id!);
      if (mounted) context.pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: _isEdit ? 'Edit Project' : 'Project Baru',
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
                label: 'Nama project',
                controller: _name,
                validator: (v) => (v == null || v.trim().isEmpty) ? 'Nama wajib diisi' : null,
              ),
              const SizedBox(height: 16),
              NeoInput(label: 'Deskripsi', controller: _description, maxLines: 4),
              const SizedBox(height: 16),
              const Text('Client',
                  style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12),
                decoration: BoxDecoration(
                  color: AppColors.surface,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: AppColors.borderDark, width: 4),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<int?>(
                    value: _clientId,
                    isExpanded: true,
                    hint: const Text('Pilih client (opsional)'),
                    items: _clients
                        .map((c) => DropdownMenuItem<int?>(
                              value: c.id,
                              child: Text(c.name),
                            ))
                        .toList(),
                    onChanged: (v) => setState(() => _clientId = v),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              const Text('Status',
                  style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
              const SizedBox(height: 8),
              Wrap(
                spacing: 8,
                children: ['development', 'testing', 'revision', 'completed']
                    .map((s) => FilterChip(
                          selected: _status == s,
                          label: Text(s),
                          onSelected: (_) => setState(() => _status = s),
                        ))
                    .toList(),
              ),
              const SizedBox(height: 16),
              Text('Progress: $_progress%',
                  style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
              Slider(
                value: _progress.toDouble(),
                max: 100,
                divisions: 20,
                activeColor: AppColors.primary,
                onChanged: (v) => setState(() => _progress = v.round()),
              ),
              const SizedBox(height: 20),
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
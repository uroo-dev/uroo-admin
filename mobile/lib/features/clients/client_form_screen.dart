import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_input.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/client.dart';
import 'clients_controller.dart';

class ClientFormScreen extends ConsumerStatefulWidget {
  final int? id;

  const ClientFormScreen({super.key, this.id});

  @override
  ConsumerState<ClientFormScreen> createState() => _ClientFormScreenState();
}

class _ClientFormScreenState extends ConsumerState<ClientFormScreen> {
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _phone = TextEditingController();
  final _company = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  bool _saving = false;

  bool get _isEdit => widget.id != null;

  @override
  void initState() {
    super.initState();
    if (_isEdit) {
      final clients = ref.read(clientsProvider).value ?? [];
      final c = clients.where((e) => e.id == widget.id).firstOrNull;
      if (c != null) {
        _name.text = c.name;
        _email.text = c.email ?? '';
        _phone.text = c.phone ?? '';
        _company.text = c.company ?? '';
      }
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    final client = Client(
      id: widget.id,
      name: _name.text.trim(),
      email: _email.text.trim().isEmpty ? null : _email.text.trim(),
      phone: _phone.text.trim().isEmpty ? null : _phone.text.trim(),
      company: _company.text.trim().isEmpty ? null : _company.text.trim(),
    );
    final controller = ref.read(clientsProvider.notifier);
    try {
      if (_isEdit) {
        await controller.update(widget.id!, client);
      } else {
        await controller.create(client);
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
        title: const Text('Hapus client?'),
        actions: [
          TextButton(onPressed: () => c.pop(false), child: const Text('Batal')),
          TextButton(onPressed: () => c.pop(true), child: const Text('Hapus')),
        ],
      ),
    );
    if (ok == true && widget.id != null) {
      await ref.read(clientsProvider.notifier).delete(widget.id!);
      if (mounted) context.pop();
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: _isEdit ? 'Edit Client' : 'Client Baru',
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
                label: 'Nama',
                controller: _name,
                validator: (v) => (v == null || v.trim().isEmpty) ? 'Nama wajib diisi' : null,
              ),
              const SizedBox(height: 16),
              NeoInput(label: 'Email', controller: _email,
                  keyboardType: TextInputType.emailAddress),
              const SizedBox(height: 16),
              NeoInput(label: 'No. HP / WA', controller: _phone,
                  keyboardType: TextInputType.phone),
              const SizedBox(height: 16),
              NeoInput(label: 'Perusahaan', controller: _company),
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
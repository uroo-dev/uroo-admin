import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_button.dart';
import '../../core/widgets/neo_input.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/models/savings_goal.dart';
import 'savings_controller.dart';

class SavingsFormScreen extends ConsumerStatefulWidget {
  const SavingsFormScreen({super.key});

  @override
  ConsumerState<SavingsFormScreen> createState() => _SavingsFormScreenState();
}

class _SavingsFormScreenState extends ConsumerState<SavingsFormScreen> {
  final _name = TextEditingController();
  final _target = TextEditingController();
  final _current = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  bool _saving = false;
  String _color = '#4F46E5';

  static const _palette = [
    '#4F46E5',
    '#22C55E',
    '#F59E0B',
    '#EF4444',
    '#FF66C4',
    '#A855F7',
    '#67E8F9',
    '#111827',
  ];

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _saving = true);
    final goal = SavingsGoal(
      name: _name.text.trim(),
      targetAmount: double.parse(_target.text.trim()),
      currentAmount:
          _current.text.trim().isEmpty ? 0 : double.parse(_current.text.trim()),
      color: _color,
      isCompleted: false,
    );
    try {
      await ref.read(savingsProvider.notifier).createGoal(goal);
      if (mounted) context.pop();
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: 'Target Baru',
      body: NeoPage(
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              NeoInput(
                label: 'Nama target',
                controller: _name,
                validator: (v) =>
                    (v == null || v.trim().isEmpty) ? 'Nama wajib diisi' : null,
              ),
              const SizedBox(height: 16),
              NeoInput(
                label: 'Target nominal (Rp)',
                controller: _target,
                keyboardType: TextInputType.number,
                validator: (v) =>
                    (v == null || double.tryParse(v) == null || double.parse(v) <= 0)
                        ? 'Nominal tidak valid'
                        : null,
              ),
              const SizedBox(height: 16),
              NeoInput(
                label: 'Saldo awal (opsional)',
                controller: _current,
                keyboardType: TextInputType.number,
              ),
              const SizedBox(height: 20),
              const Text('Warna',
                  style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
              const SizedBox(height: 8),
              Wrap(
                spacing: 10,
                children: _palette
                    .map((c) => GestureDetector(
                          onTap: () => setState(() => _color = c),
                          child: Container(
                            width: 44,
                            height: 44,
                            decoration: BoxDecoration(
                              color: Color(int.parse('0xFF${c.substring(1)}')),
                              shape: BoxShape.circle,
                              border: Border.all(
                                color: _color == c
                                    ? AppColors.borderDark
                                    : AppColors.borderDark,
                                width: _color == c ? 5 : 3,
                              ),
                            ),
                          ),
                        ))
                    .toList(),
              ),
              const SizedBox(height: 28),
              NeoButton(
                label: 'Simpan',
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
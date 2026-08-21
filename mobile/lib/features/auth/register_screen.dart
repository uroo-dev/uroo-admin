import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import 'auth_controller.dart';
import 'auth_widgets.dart';

class RegisterScreen extends ConsumerStatefulWidget {
  const RegisterScreen({super.key});

  @override
  ConsumerState<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends ConsumerState<RegisterScreen> {
  final _name = TextEditingController();
  final _email = TextEditingController();
  final _password = TextEditingController();
  final _confirm = TextEditingController();
  final _formKey = GlobalKey<FormState>();

  @override
  void dispose() {
    _name.dispose();
    _email.dispose();
    _password.dispose();
    _confirm.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    final ok = await ref.read(authControllerProvider.notifier).signUp(
          name: _name.text.trim(),
          email: _email.text.trim(),
          password: _password.text,
        );
    if (ok && mounted) context.go('/');
  }

  @override
  Widget build(BuildContext context) {
    final auth = ref.watch(authControllerProvider);

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 420),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const AuthLogoBlock(subtitle: 'Buat akun baru'),
                  const SizedBox(height: 32),
                  AuthCard(
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          AuthField(
                            controller: _name,
                            label: 'Nama',
                            hint: 'Nama lengkap',
                            validator: (v) => (v == null || v.trim().isEmpty)
                                ? 'Nama wajib diisi'
                                : null,
                          ),
                          const SizedBox(height: 20),
                          AuthField(
                            controller: _email,
                            label: 'Email',
                            hint: 'nama@email.com',
                            keyboardType: TextInputType.emailAddress,
                            validator: (v) =>
                                (v == null || !v.contains('@'))
                                    ? 'Email tidak valid'
                                    : null,
                          ),
                          const SizedBox(height: 20),
                          AuthField(
                            controller: _password,
                            label: 'Password',
                            hint: 'Minimal 8 karakter',
                            obscure: true,
                            validator: (v) => (v == null || v.length < 8)
                                ? 'Minimal 8 karakter'
                                : null,
                          ),
                          const SizedBox(height: 20),
                          AuthField(
                            controller: _confirm,
                            label: 'Konfirmasi Password',
                            hint: 'Ulangi password',
                            obscure: true,
                            validator: (v) => (v != _password.text)
                                ? 'Password tidak sama'
                                : null,
                          ),
                          const SizedBox(height: 20),
                          if (auth.hasError) ...[
                            Text(
                              '${auth.error}',
                              style: const TextStyle(
                                color: AppColors.danger,
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                            const SizedBox(height: 20),
                          ],
                          AuthButton(
                            label: 'Daftar',
                            loading: auth.isLoading,
                            onPressed: _submit,
                          ),
                          const SizedBox(height: 20),
                          const AuthFooterLink(
                            label: 'Sudah punya akun? ',
                            linkLabel: 'Masuk',
                            route: '/login',
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

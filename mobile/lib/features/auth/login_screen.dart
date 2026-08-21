import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../data/db/app_database.dart';
import 'auth_controller.dart';
import 'auth_widgets.dart';

/// Kredensial default dari database seeder (database/seeders/DatabaseSeeder.php)
/// agar first-run tinggal tap "Masuk".
const _seededEmail = 'dimas@gmail.com';
const _seededPassword = 'password';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _serverUrl = TextEditingController();
  final _email = TextEditingController(text: _seededEmail);
  final _password = TextEditingController(text: _seededPassword);
  final _formKey = GlobalKey<FormState>();
  bool _remember = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadServerUrl());
  }

  Future<void> _loadServerUrl() async {
    final saved = await AppDatabase.instance.meta('server_url');
    if (!mounted || saved == null) return;
    setState(() => _serverUrl.text = saved);
  }

  String? _validateServerUrl(String? v) {
    final url = v?.trim() ?? '';
    if (url.isEmpty) return 'URL server (ngrok) wajib diisi';
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
      return 'URL harus diawali http:// atau https://';
    }
    return null;
  }

  void _normalizeUrl() {
    _serverUrl.text = _serverUrl.text.trim();
    while (_serverUrl.text.endsWith('/')) {
      _serverUrl.text = _serverUrl.text.substring(0, _serverUrl.text.length - 1);
    }
  }

  @override
  void dispose() {
    _serverUrl.dispose();
    _email.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    _normalizeUrl();
    await AppDatabase.instance.setMeta('server_url', _serverUrl.text);

    final ok = await ref.read(authControllerProvider.notifier).signIn(
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
                  const AuthLogoBlock(subtitle: 'Workspace — Masuk ke akunmu'),
                  const SizedBox(height: 32),
                  AuthCard(
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          AuthField(
                            controller: _serverUrl,
                            label: 'URL Server (ngrok)',
                            hint: 'https://xxxx.ngrok-free.app',
                            keyboardType: TextInputType.url,
                            validator: _validateServerUrl,
                          ),
                          const SizedBox(height: 8),
                          const Text(
                            'Jalankan "composer uroo" di web lalu salin URL ngrok-nya.',
                            style: TextStyle(
                              fontSize: 11,
                              color: AppColors.txtSecondary,
                            ),
                          ),
                          const SizedBox(height: 20),
                          AuthField(
                            controller: _email,
                            label: 'Email',
                            hint: 'nama@email.com',
                            keyboardType: TextInputType.emailAddress,
                            validator: (v) => (v == null || v.trim().isEmpty)
                                ? 'Email wajib diisi'
                                : null,
                          ),
                          const SizedBox(height: 20),
                          AuthField(
                            controller: _password,
                            label: 'Password',
                            hint: '••••••••',
                            obscure: true,
                            validator: (v) => (v == null || v.isEmpty)
                                ? 'Password wajib diisi'
                                : null,
                          ),
                          const SizedBox(height: 20),
                          Row(
                            children: [
                              SizedBox(
                                width: 20,
                                height: 20,
                                child: Checkbox(
                                  value: _remember,
                                  onChanged: (v) =>
                                      setState(() => _remember = v ?? false),
                                  activeColor: AppColors.primary,
                                  checkColor: Colors.white,
                                  side: const BorderSide(
                                    color: AppColors.borderDark,
                                    width: 4,
                                  ),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(2),
                                  ),
                                  materialTapTargetSize:
                                      MaterialTapTargetSize.shrinkWrap,
                                  visualDensity: VisualDensity.compact,
                                ),
                              ),
                              const SizedBox(width: 8),
                              const Text(
                                'Ingat saya',
                                style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                            ],
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
                            label: 'Masuk',
                            loading: auth.isLoading,
                            onPressed: _submit,
                          ),
                          const SizedBox(height: 20),
                          const AuthFooterLink(
                            label: 'Belum punya akun? ',
                            linkLabel: 'Daftar',
                            route: '/register',
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

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/session/session_store.dart';
import '../../core/sync/sync_controller.dart';
import '../../core/sync/sync_service.dart';
import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/db/app_database.dart';
import '../auth/auth_controller.dart';

class SettingsScreen extends ConsumerStatefulWidget {
  const SettingsScreen({super.key});

  @override
  ConsumerState<SettingsScreen> createState() => _SettingsScreenState();
}

class _SettingsScreenState extends ConsumerState<SettingsScreen> {
  final _serverUrl = TextEditingController();
  final _githubUsername = TextEditingController();
  final _githubToken = TextEditingController();
  bool _urlLoaded = false;
  bool _githubLoaded = false;

  @override
  void dispose() {
    _serverUrl.dispose();
    _githubUsername.dispose();
    _githubToken.dispose();
    super.dispose();
  }

  Future<void> _loadServerUrl() async {
    final saved = await AppDatabase.instance.meta('server_url');
    if (!mounted) return;
    setState(() {
      _serverUrl.text = saved ?? '';
      _urlLoaded = true;
    });
  }

  Future<void> _loadGithub() async {
    final db = AppDatabase.instance;
    final username = await db.meta('github_username');
    final token = await db.meta('github_token');
    if (!mounted) return;
    setState(() {
      _githubUsername.text = username ?? '';
      _githubToken.text = token ?? '';
      _githubLoaded = true;
    });
  }

  Future<void> _saveServerUrl() async {
    final url = _serverUrl.text.trim();
    if (url.isEmpty) return;

    await AppDatabase.instance.setMeta('server_url', url);
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('URL server disimpan. Login ulang agar diterapkan.')),
    );
  }

  Future<void> _saveGithub() async {
    final db = AppDatabase.instance;
    await db.setMeta('github_username', _githubUsername.text.trim());
    await db.setMeta('github_token', _githubToken.text.trim());
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Pengaturan GitHub disimpan.')),
    );
  }

  @override
  Widget build(BuildContext context) {
    final session = currentSession.value;
    final sync = ref.watch(syncProvider);

    return NeoScaffold(
      title: 'Pengaturan',
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          FutureBuilder<void>(
            future: _urlLoaded ? null : _loadServerUrl(),
            builder: (context, _) => NeoCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Text(
                    'Server',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'URL web UROO.Admin untuk sinkronisasi',
                    style: TextStyle(fontSize: 12, color: AppColors.txtSecondary),
                  ),
                  const SizedBox(height: 14),
                  TextField(
                    controller: _serverUrl,
                    keyboardType: TextInputType.url,
                    autocorrect: false,
                    decoration: InputDecoration(
                      hintText: 'http://192.168.1.10:8000',
                      filled: true,
                      fillColor: AppColors.surfaceAlt,
                      contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 14),
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
                    ),
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    height: 46,
                    child: ElevatedButton(
                      onPressed: _saveServerUrl,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.primary,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(18),
                          side: const BorderSide(
                              color: AppColors.borderDark, width: 4),
                        ),
                        elevation: 0,
                      ),
                      child: const Text(
                        'Simpan URL',
                        style: TextStyle(fontWeight: FontWeight.w800),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),
          NeoCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const Text(
                  'Sinkronisasi',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 4),
                Text(
                  _syncStatusLine(sync),
                  style: const TextStyle(
                      fontSize: 12, color: AppColors.txtSecondary),
                ),
                const SizedBox(height: 14),
                SizedBox(
                  height: 46,
                  child: ElevatedButton(
                    onPressed:
                        sync.isLoading ? null : () => ref.read(syncProvider.notifier).syncNow(),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.secondary,
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: AppColors.surfaceAlt,
                      disabledForegroundColor: AppColors.txtSecondary,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(18),
                        side: const BorderSide(
                            color: AppColors.borderDark, width: 4),
                      ),
                      elevation: 0,
                    ),
                    child: sync.isLoading
                        ? const SizedBox(
                            width: 22,
                            height: 22,
                            child: CircularProgressIndicator(
                                strokeWidth: 3, color: Colors.white),
                          )
                        : const Text(
                            'Sinkronkan Sekarang',
                            style: TextStyle(fontWeight: FontWeight.w800),
                          ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
          FutureBuilder<void>(
            future: _githubLoaded ? null : _loadGithub(),
            builder: (context, _) => NeoCard(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const Text(
                    'GitHub',
                    style:
                        TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Username & token (PAT) untuk lihat repositori/commit di HP',
                    style: TextStyle(
                        fontSize: 12, color: AppColors.txtSecondary),
                  ),
                  const SizedBox(height: 14),
                  TextField(
                    controller: _githubUsername,
                    autocorrect: false,
                    decoration: InputDecoration(
                      hintText: 'username',
                      labelText: 'Username',
                      filled: true,
                      fillColor: AppColors.surfaceAlt,
                      contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 14),
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
                    ),
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _githubToken,
                    autocorrect: false,
                    obscureText: true,
                    decoration: InputDecoration(
                      hintText: 'ghp_xxxxxxxxxxxx',
                      labelText: 'Personal Access Token',
                      filled: true,
                      fillColor: AppColors.surfaceAlt,
                      contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 14),
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
                    ),
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    height: 46,
                    child: ElevatedButton(
                      onPressed: _saveGithub,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.pinkAccent,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(18),
                          side: const BorderSide(
                              color: AppColors.borderDark, width: 4),
                        ),
                        elevation: 0,
                      ),
                      child: const Text(
                        'Simpan GitHub',
                        style: TextStyle(fontWeight: FontWeight.w800),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 20),
          NeoCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                const Text(
                  'Akun',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 4),
                Text(
                  session == null
                      ? 'Belum masuk'
                      : '${session.name} • ${session.email}',
                  style: const TextStyle(
                      fontSize: 12, color: AppColors.txtSecondary),
                ),
                const SizedBox(height: 14),
                SizedBox(
                  height: 46,
                  child: ElevatedButton(
                    onPressed: () async {
                      await ref.read(authControllerProvider.notifier).signOut();
                      if (context.mounted) context.go('/login');
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.danger,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(18),
                        side: const BorderSide(
                            color: AppColors.borderDark, width: 4),
                      ),
                      elevation: 0,
                    ),
                    child: const Text(
                      'Keluar',
                      style: TextStyle(fontWeight: FontWeight.w800),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  String _syncStatusLine(AsyncValue<SyncResult?> sync) {
    if (sync.isLoading) return 'Menyinkronkan…';
    final result = sync.valueOrNull;
    if (result == null) return 'Belum pernah sinkron.';
    if (result.error != null) return 'Terakhir gagal: ${result.error}';
    return 'Terakhir: ${result.pushed} dikirim, ${result.pulled} diterima.';
  }
}
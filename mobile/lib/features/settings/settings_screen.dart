import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/session/session_store.dart';
import '../../core/sync/sync_controller.dart';
import '../../core/theme/app_colors.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../core/widgets/sync_status_badge.dart';
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
  String? _pingMessage; // null = sukses / belum dites
  bool _testing = false;

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
    final url = _normalizeUrl(_serverUrl.text);
    if (url.isEmpty) return;

    await AppDatabase.instance.setMeta('server_url', url);
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('URL server disimpan. Login ulang agar diterapkan.')),
    );
  }

  String _normalizeUrl(String raw) {
    var url = raw.trim();
    while (url.endsWith('/')) {
      url = url.substring(0, url.length - 1);
    }
    return url;
  }

  Future<void> _testConnection() async {
    final url = _normalizeUrl(_serverUrl.text);
    if (url.isEmpty) {
      setState(() => _pingMessage = 'Isi URL server dulu.');
      return;
    }

    setState(() {
      _testing = true;
      _pingMessage = null;
    });

    final error = await ref.read(syncProvider.notifier).testConnection(url);
    if (!mounted) return;
    setState(() {
      _testing = false;
      _pingMessage = error ?? 'Terhubung! Server siap sinkronisasi.';
    });
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
                  const Row(
                    children: [
                      Expanded(
                        child: Text(
                          'Server',
                          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w800),
                        ),
                      ),
                      SyncStatusBadge(),
                    ],
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'URL ngrok web lokal untuk sinkronisasi',
                    style: TextStyle(fontSize: 12, color: AppColors.txtSecondary),
                  ),
                  const SizedBox(height: 14),
                  TextField(
                    controller: _serverUrl,
                    keyboardType: TextInputType.url,
                    autocorrect: false,
                    decoration: InputDecoration(
                      hintText: 'https://xxxx.ngrok-free.app',
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
                  const SizedBox(height: 8),
                  const Text(
                    'Jalankan "composer uroo" di web lalu salin URL Forwarding ngrok ke sini.',
                    style: TextStyle(fontSize: 11, color: AppColors.txtSecondary),
                  ),
                  const SizedBox(height: 12),
                  if (_pingMessage != null) ...[
                    Text(
                      _pingMessage!,
                      style: TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        color: _testing
                            ? AppColors.txtSecondary
                            : _pingMessage!.startsWith('Terhubung')
                                ? AppColors.success
                                : AppColors.danger,
                      ),
                    ),
                    const SizedBox(height: 12),
                  ],
                  Row(
                    children: [
                      Expanded(
                        child: SizedBox(
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
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: SizedBox(
                          height: 46,
                          child: OutlinedButton(
                            onPressed: _testing ? null : _testConnection,
                            style: OutlinedButton.styleFrom(
                              backgroundColor: AppColors.surface,
                              foregroundColor: AppColors.txtPrimary,
                              side: const BorderSide(
                                  color: AppColors.borderDark, width: 4),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(18),
                              ),
                            ),
                            child: _testing
                                ? const SizedBox(
                                    width: 22,
                                    height: 22,
                                    child: CircularProgressIndicator(strokeWidth: 3),
                                  )
                                : const Text(
                                    'Tes Koneksi',
                                    style:
                                        TextStyle(fontWeight: FontWeight.w800),
                                  ),
                          ),
                        ),
                      ),
                    ],
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
                  _syncStatusLine(ref.watch(syncProvider)),
                  style: const TextStyle(
                      fontSize: 12, color: AppColors.txtSecondary),
                ),
                const SizedBox(height: 4),
                FutureBuilder<String?>(
                  future: AppDatabase.instance.meta('last_synced_at'),
                  builder: (context, snap) {
                    final at = DateTime.tryParse(snap.data ?? '');
                    if (at == null) return const SizedBox.shrink();
                    return Text(
                      'Sinkron terakhir: ${_formatDateTime(at)}',
                      style: const TextStyle(
                          fontSize: 12, color: AppColors.txtSecondary),
                    );
                  },
                ),
                const SizedBox(height: 14),
                SizedBox(
                  height: 46,
                  child: ElevatedButton(
                    onPressed:
                        sync.syncing ? null : () => ref.read(syncProvider.notifier).syncNow(),
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
                    child: sync.syncing
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

  String _formatDateTime(DateTime dt) {
    String two(int n) => n.toString().padLeft(2, '0');
    return '${dt.day}/${dt.month}/${dt.year} ${two(dt.hour)}:${two(dt.minute)}';
  }

  String _syncStatusLine(SyncState sync) {
    if (sync.syncing) return 'Menyinkronkan…';
    final result = sync.result;
    if (sync.phase == ConnectionPhase.notConnected) {
      return 'Belum tersambung. Login dulu untuk sinkronisasi.';
    }
    if (result == null) return 'Belum pernah sinkron.';
    if (result.error != null) return 'Terakhir gagal: ${result.error}';
    return 'Terakhir: ${result.pushed} dikirim, ${result.pulled} diterima.';
  }
}
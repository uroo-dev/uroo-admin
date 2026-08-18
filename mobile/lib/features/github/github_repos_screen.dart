import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_badge.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/db/app_database.dart';
import '../../data/github/github_api.dart';

class GitHubReposScreen extends ConsumerStatefulWidget {
  const GitHubReposScreen({super.key});

  @override
  ConsumerState<GitHubReposScreen> createState() => _GitHubReposScreenState();
}

class _GitHubReposScreenState extends ConsumerState<GitHubReposScreen> {
  List<GitHubRepo>? _repos;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _repos = null;
      _error = null;
    });

    final username = await AppDatabase.instance.meta('github_username');
    final token = await AppDatabase.instance.meta('github_token');
    if (username == null || username.isEmpty || token == null || token.isEmpty) {
      setState(() => _error = 'Atur username & token GitHub di Pengaturan.');
      return;
    }

    try {
      final repos = await GitHubApi().repos(username: username, token: token);
      if (mounted) setState(() => _repos = repos);
    } catch (e) {
      if (mounted) setState(() => _error = '$e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: 'GitHub',
      body: RefreshIndicator(onRefresh: _load, child: _buildBody()),
    );
  }

  Widget _buildBody() {
    if (_error != null) {
      return ListView(
        physics: const AlwaysScrollableScrollPhysics(),
        children: [
          const SizedBox(height: 120),
          NeoEmptyState(
            title: 'Tidak bisa memuat repositori',
            message: _error!,
            icon: Icons.cloud_off_outlined,
          ),
        ],
      );
    }

    final repos = _repos;
    if (repos == null) {
      return const Center(child: CircularProgressIndicator());
    }

    if (repos.isEmpty) {
      return const NeoEmptyState(
        title: 'Tidak ada repositori',
        message: 'Belum ada repositori publik untuk akun ini.',
        icon: Icons.folder_open_outlined,
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
      itemCount: repos.length,
      itemBuilder: (context, i) {
        final repo = repos[i];
        return Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: NeoCard(
            onTap: () => context.push('/github/${repo.fullName}'),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        repo.name,
                        style: const TextStyle(
                            fontSize: 16, fontWeight: FontWeight.w800),
                      ),
                    ),
                    NeoBadge(label: repo.language ?? '—', color: AppColors.secondary),
                  ],
                ),
                if (repo.description != null) ...[
                  const SizedBox(height: 6),
                  Text(
                    repo.description!,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        fontSize: 13, color: AppColors.txtSecondary),
                  ),
                ],
                const SizedBox(height: 10),
                Row(
                  children: [
                    const Icon(Icons.star, size: 16, color: AppColors.warning),
                    const SizedBox(width: 4),
                    Text('${repo.stars}',
                        style: const TextStyle(
                            fontSize: 13, fontWeight: FontWeight.w700)),
                    const Spacer(),
                    if (repo.updatedAt != null)
                      Text(
                        'Update ${FormatUtil.date(repo.updatedAt)}',
                        style: const TextStyle(
                            fontSize: 12, color: AppColors.txtSecondary),
                      ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
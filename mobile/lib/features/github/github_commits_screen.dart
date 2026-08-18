import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../core/theme/app_colors.dart';
import '../../core/utils/format_util.dart';
import '../../core/widgets/neo_card.dart';
import '../../core/widgets/neo_empty_state.dart';
import '../../core/widgets/neo_scaffold.dart';
import '../../data/db/app_database.dart';
import '../../data/github/github_api.dart';

class GitHubCommitsScreen extends StatefulWidget {
  final String owner;
  final String repo;

  const GitHubCommitsScreen({super.key, required this.owner, required this.repo});

  @override
  State<GitHubCommitsScreen> createState() => _GitHubCommitsScreenState();
}

class _GitHubCommitsScreenState extends State<GitHubCommitsScreen> {
  List<GitHubCommit>? _commits;
  String? _error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _commits = null;
      _error = null;
    });

    final token = await AppDatabase.instance.meta('github_token');

    try {
      final commits = await GitHubApi().commits(
        owner: widget.owner,
        repo: widget.repo,
        token: token ?? '',
      );
      if (mounted) setState(() => _commits = commits);
    } catch (e) {
      if (mounted) setState(() => _error = '$e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return NeoScaffold(
      title: widget.repo,
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
            title: 'Tidak bisa memuat commit',
            message: _error!,
            icon: Icons.cloud_off_outlined,
          ),
        ],
      );
    }

    final commits = _commits;
    if (commits == null) {
      return const Center(child: CircularProgressIndicator());
    }

    if (commits.isEmpty) {
      return const NeoEmptyState(
        title: 'Belum ada commit',
        message: 'Repositori ini belum punya commit.',
        icon: Icons.commit_outlined,
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
      itemCount: commits.length,
      itemBuilder: (context, i) {
        final commit = commits[i];
        final shortSha =
            commit.sha.length > 7 ? commit.sha.substring(0, 7) : commit.sha;
        return Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: NeoCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  commit.message,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                      fontSize: 15, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    const Icon(Icons.person_outline,
                        size: 16, color: AppColors.primary),
                    const SizedBox(width: 4),
                    Expanded(
                      child: Text(
                        commit.author,
                        style: const TextStyle(
                            fontSize: 13, color: AppColors.txtSecondary),
                      ),
                    ),
                    if (commit.date != null)
                      Text(
                        FormatUtil.date(commit.date),
                        style: const TextStyle(
                            fontSize: 12, color: AppColors.txtSecondary),
                      ),
                    const SizedBox(width: 8),
                    Text(
                      shortSha,
                      style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: AppColors.primary),
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
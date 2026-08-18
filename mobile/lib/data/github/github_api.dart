import 'dart:convert';

import 'package:http/http.dart' as http;

class GitHubRepo {
  final String name;
  final String fullName;
  final String? description;
  final String? language;
  final int stars;
  final DateTime? updatedAt;

  const GitHubRepo({
    required this.name,
    required this.fullName,
    this.description,
    this.language,
    this.stars = 0,
    this.updatedAt,
  });

  factory GitHubRepo.fromJson(Map<String, dynamic> json) => GitHubRepo(
        name: (json['name'] as String?) ?? '',
        fullName: (json['full_name'] as String?) ?? '',
        description: json['description'] as String?,
        language: json['language'] as String?,
        stars: (json['stargazers_count'] as num?)?.toInt() ?? 0,
        updatedAt: DateTime.tryParse((json['updated_at'] as String?) ?? ''),
      );
}

class GitHubCommit {
  final String sha;
  final String message;
  final String author;
  final DateTime? date;

  const GitHubCommit({
    required this.sha,
    required this.message,
    required this.author,
    this.date,
  });

  factory GitHubCommit.fromJson(Map<String, dynamic> json) {
    final commit = (json['commit'] as Map?)?.cast<String, dynamic>() ?? {};
    final committer = (commit['committer'] as Map?)?.cast<String, dynamic>() ?? {};
    final author = (commit['author'] as Map?)?.cast<String, dynamic>() ?? {};

    return GitHubCommit(
      sha: (json['sha'] as String?) ?? '',
      message: (commit['message'] as String?) ?? '',
      author: ((author['name'] as String?) ??
              (committer['name'] as String?) ??
              'unknown')
          .split(' ')
          .first,
      date: DateTime.tryParse((committer['date'] as String?) ?? ''),
    );
  }
}

/// Akses GitHub API langsung dari HP (tanpa server perantara).
class GitHubApi {
  static const _base = 'https://api.github.com';

  /// Daftar repositori publik milik [username].
  Future<List<GitHubRepo>> repos({
    required String username,
    required String token,
  }) async {
    final uri = Uri.parse('$_base/users/$username/repos'
        '?per_page=100&sort=updated');

    final res = await http.get(uri, headers: _headers(token)).timeout(
          const Duration(seconds: 20),
          onTimeout: () => http.Response('', 504),
        );

    if (res.statusCode != 200) {
      throw Exception(_errorMessage(res));
    }

    final data = jsonDecode(res.body) as List;
    return data
        .cast<Map<String, dynamic>>()
        .map(GitHubRepo.fromJson)
        .toList();
  }

  /// Commit terbaru dari satu repositori.
  Future<List<GitHubCommit>> commits({
    required String owner,
    required String repo,
    required String token,
  }) async {
    final uri = Uri.parse('$_base/repos/$owner/$repo/commits?per_page=50');

    final res = await http.get(uri, headers: _headers(token)).timeout(
          const Duration(seconds: 20),
          onTimeout: () => http.Response('', 504),
        );

    if (res.statusCode != 200) {
      throw Exception(_errorMessage(res));
    }

    final data = jsonDecode(res.body) as List;
    return data
        .cast<Map<String, dynamic>>()
        .map(GitHubCommit.fromJson)
        .toList();
  }

  Map<String, String> _headers(String token) => {
        'Authorization': 'token $token',
        'Accept': 'application/vnd.github+json',
        'X-GitHub-Api-Version': '2022-11-28',
      };

  String _errorMessage(http.Response res) {
    if (res.statusCode == 404) return 'Repositori tidak ditemukan (404).';
    if (res.statusCode == 401) return 'Token GitHub tidak valid (401).';
    if (res.statusCode == 403) return 'Rate limit GitHub tercapai (403).';
    if (res.statusCode == 504) return 'Terlalu lama merespons. Cek koneksi.';
    return 'Gagal (${res.statusCode}).';
  }
}
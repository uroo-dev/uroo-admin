import 'jsonc.dart';

class Project {
  final int? id;
  final int? clientId;
  final String name;
  final String? description;
  final String? category;
  final String status;
  final int progress;
  final String? platform;
  final List<String> techStack;
  final String? clientName;
  final DateTime? deadline;
  final DateTime? createdAt;

  const Project({
    this.id,
    this.clientId,
    required this.name,
    this.description,
    this.category,
    this.status = 'development',
    this.progress = 0,
    this.platform,
    this.techStack = const [],
    this.clientName,
    this.deadline,
    this.createdAt,
  });

  factory Project.fromJson(Map<String, dynamic> json) => Project(
        id: json['id'] as int?,
        clientId: (json['client_id'] as num?)?.toInt(),
        name: (json['name'] as String?) ?? '',
        description: json['description'] as String?,
        category: json['category'] as String?,
        status: (json['status'] as String?) ?? 'development',
        progress: (json['progress'] as num?)?.toInt() ?? 0,
        platform: json['platform'] as String?,
        techStack: Jsonc.toStringList(json['tech_stack']),
        clientName: (json['client'] is Map)
            ? ((json['client'] as Map)['name'] as String?) ?? ''
            : null,
        deadline: json['deadline'] is DateTime
            ? json['deadline'] as DateTime?
            : DateTime.tryParse((json['deadline'] as String?) ?? ''),
        createdAt: json['created_at'] is DateTime
            ? json['created_at'] as DateTime?
            : DateTime.tryParse((json['created_at'] as String?) ?? ''),
      );

  Map<String, dynamic> toMap() => {
        'client_id': clientId,
        'name': name,
        'description': description,
        'category': category,
        'status': status,
        'progress': progress,
        'platform': platform,
        'tech_stack': techStack,
      };
}
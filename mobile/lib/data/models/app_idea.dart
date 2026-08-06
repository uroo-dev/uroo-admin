import 'jsonc.dart';

class AppIdea {
  final int? id;
  final String name;
  final String? tagline;
  final String? description;
  final List<String> features;
  final List<String> techStack;
  final String platform;
  final String status;
  final String priority;
  final List<String> tags;
  final String? notes;
  final DateTime? createdAt;

  const AppIdea({
    this.id,
    required this.name,
    this.tagline,
    this.description,
    this.features = const [],
    this.techStack = const [],
    this.platform = 'web',
    this.status = 'draft',
    this.priority = 'medium',
    this.tags = const [],
    this.notes,
    this.createdAt,
  });

  factory AppIdea.fromJson(Map<String, dynamic> json) => AppIdea(
        id: json['id'] as int?,
        name: (json['name'] as String?) ?? '',
        tagline: json['tagline'] as String?,
        description: json['description'] as String?,
        features: Jsonc.toStringList(json['features']),
        techStack: Jsonc.toStringList(json['tech_stack']),
        platform: (json['platform'] as String?) ?? 'web',
        status: (json['status'] as String?) ?? 'draft',
        priority: (json['priority'] as String?) ?? 'medium',
        tags: Jsonc.toStringList(json['tags']),
        notes: json['notes'] as String?,
        createdAt: json['created_at'] is DateTime
            ? json['created_at'] as DateTime?
            : DateTime.tryParse((json['created_at'] as String?) ?? ''),
      );

  Map<String, dynamic> toMap() => {
        'name': name,
        'tagline': tagline,
        'description': description,
        'features': features,
        'tech_stack': techStack,
        'platform': platform,
        'status': status,
        'priority': priority,
        'tags': tags,
        'notes': notes,
      };
}
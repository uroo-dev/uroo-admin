import '../models/jsonc.dart';

DateTime? _dt(dynamic v) =>
    v is DateTime ? v : DateTime.tryParse((v as String?) ?? '');

class Note {
  final int? id;
  final String title;
  final String content;
  final String? category;
  final List<String> tags;
  final bool isPinned;
  final bool isFavorite;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  const Note({
    this.id,
    required this.title,
    required this.content,
    this.category,
    this.tags = const [],
    this.isPinned = false,
    this.isFavorite = false,
    this.createdAt,
    this.updatedAt,
  });

  factory Note.fromJson(Map<String, dynamic> json) => Note(
        id: json['id'] as int?,
        title: (json['title'] as String?) ?? '',
        content: (json['content'] as String?) ?? '',
        category: json['category'] as String?,
        tags: Jsonc.toStringList(json['tags']),
        isPinned: (json['is_pinned'] as bool?) ?? false,
        isFavorite: (json['is_favorite'] as bool?) ?? false,
        createdAt: _dt(json['created_at']),
        updatedAt: _dt(json['updated_at']),
      );

  Map<String, dynamic> toMap() => {
        'title': title,
        'content': content,
        'category': category,
        'tags': tags,
        'is_pinned': isPinned,
        'is_favorite': isFavorite,
      };
}
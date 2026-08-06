class BrainDump {
  final int? id;
  final String content;
  final bool isPinned;
  final bool isArchived;
  final DateTime? createdAt;

  const BrainDump({
    this.id,
    required this.content,
    this.isPinned = false,
    this.isArchived = false,
    this.createdAt,
  });

  factory BrainDump.fromJson(Map<String, dynamic> json) => BrainDump(
        id: json['id'] as int?,
        content: (json['content'] as String?) ?? '',
        isPinned: (json['is_pinned'] as bool?) ?? false,
        isArchived: (json['is_archived'] as bool?) ?? false,
        createdAt: json['created_at'] is DateTime
            ? json['created_at'] as DateTime?
            : DateTime.tryParse((json['created_at'] as String?) ?? ''),
      );

  Map<String, dynamic> toMap() => {
        'content': content,
        'is_pinned': isPinned,
        'is_archived': isArchived,
      };
}
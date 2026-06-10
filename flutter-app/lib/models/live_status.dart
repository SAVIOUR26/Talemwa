class LiveStatus {
  final bool    isLive;
  final String? youtubeId;
  final String  title;
  final String? updatedAt;

  const LiveStatus({
    required this.isLive,
    this.youtubeId,
    required this.title,
    this.updatedAt,
  });

  factory LiveStatus.fromJson(Map<String, dynamic> j) => LiveStatus(
    isLive:    j['is_live'] as bool? ?? false,
    youtubeId: j['youtube_id'] as String?,
    title:     j['title'] as String? ?? 'Sunday Service',
    updatedAt: j['updated_at'] as String?,
  );

  factory LiveStatus.offline() => const LiveStatus(isLive: false, title: 'Off Air');
}

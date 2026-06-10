class Sermon {
  final int     id;
  final String  title;
  final String? series;
  final String  speaker;
  final String? description;
  final String? youtubeUrl;
  final String? mp3Url;
  final int     durationSeconds;
  final String? thumbnailUrl;
  final String? scripture;
  final String? tags;
  final bool    published;
  final int     playCount;
  final int     downloadCount;
  final String  createdAt;

  const Sermon({
    required this.id,
    required this.title,
    this.series,
    required this.speaker,
    this.description,
    this.youtubeUrl,
    this.mp3Url,
    this.durationSeconds = 0,
    this.thumbnailUrl,
    this.scripture,
    this.tags,
    this.published = true,
    this.playCount = 0,
    this.downloadCount = 0,
    required this.createdAt,
  });

  factory Sermon.fromJson(Map<String, dynamic> j) => Sermon(
    id:              j['id'] as int,
    title:           j['title'] as String,
    series:          j['series'] as String?,
    speaker:         j['speaker'] as String? ?? 'Pastor Robert Talemwa',
    description:     j['description'] as String?,
    youtubeUrl:      j['youtube_url'] as String?,
    mp3Url:          j['mp3_url'] as String?,
    durationSeconds: (j['duration_seconds'] as num?)?.toInt() ?? 0,
    thumbnailUrl:    j['thumbnail_url'] as String?,
    scripture:       j['scripture'] as String?,
    tags:            j['tags'] as String?,
    published:       (j['published'] as int? ?? 1) == 1,
    playCount:       (j['play_count'] as num?)?.toInt() ?? 0,
    downloadCount:   (j['download_count'] as num?)?.toInt() ?? 0,
    createdAt:       j['created_at'] as String? ?? '',
  );

  String get formattedDuration {
    if (durationSeconds == 0) return '';
    final h = durationSeconds ~/ 3600;
    final m = (durationSeconds % 3600) ~/ 60;
    final s = durationSeconds % 60;
    if (h > 0) return '$h:${m.toString().padLeft(2,'0')}:${s.toString().padLeft(2,'0')}';
    return '$m:${s.toString().padLeft(2,'0')}';
  }

  String? get youtubeId {
    if (youtubeUrl == null) return null;
    final m = RegExp(r'(?:v=|youtu\.be/)([A-Za-z0-9_-]{11})').firstMatch(youtubeUrl!);
    return m?.group(1);
  }

  List<String> get tagList => tags?.split(',').map((t) => t.trim()).where((t) => t.isNotEmpty).toList() ?? [];

  Map<String, dynamic> toJson() => {
    'id': id, 'title': title, 'series': series, 'speaker': speaker,
    'description': description, 'youtube_url': youtubeUrl, 'mp3_url': mp3Url,
    'duration_seconds': durationSeconds, 'thumbnail_url': thumbnailUrl,
    'scripture': scripture, 'tags': tags, 'published': published ? 1 : 0,
    'play_count': playCount, 'download_count': downloadCount, 'created_at': createdAt,
  };
}

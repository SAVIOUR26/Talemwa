class NowPlaying {
  final String  title;
  final String  artist;
  final String? art;

  const NowPlaying({required this.title, required this.artist, this.art});

  factory NowPlaying.fromJson(Map<String, dynamic> j) => NowPlaying(
    title:  j['title']  as String? ?? 'Miracles Now Radio',
    artist: j['artist'] as String? ?? '',
    art:    j['art']    as String?,
  );
}

class RadioStatus {
  final String     streamUrl;
  final bool       isOnline;
  final NowPlaying nowPlaying;
  final int        listeners;

  const RadioStatus({
    required this.streamUrl,
    required this.isOnline,
    required this.nowPlaying,
    required this.listeners,
  });

  factory RadioStatus.fromJson(Map<String, dynamic> j) => RadioStatus(
    streamUrl:  j['stream_url']  as String? ?? '',
    isOnline:   j['is_online']   as bool?   ?? false,
    nowPlaying: NowPlaying.fromJson((j['now_playing'] as Map<String, dynamic>?) ?? {}),
    listeners:  (j['listeners']  as num?)?.toInt() ?? 0,
  );

  factory RadioStatus.offline() => RadioStatus(
    streamUrl:  '',
    isOnline:   false,
    nowPlaying: const NowPlaying(title: 'Miracles Now Radio', artist: ''),
    listeners:  0,
  );
}

class RadioScheduleSlot {
  final int    id;
  final String dayOfWeek;
  final String startTime;
  final String endTime;
  final String programName;
  final String? description;
  final bool   isActive;

  const RadioScheduleSlot({
    required this.id,
    required this.dayOfWeek,
    required this.startTime,
    required this.endTime,
    required this.programName,
    this.description,
    required this.isActive,
  });

  factory RadioScheduleSlot.fromJson(Map<String, dynamic> j) => RadioScheduleSlot(
    id:          j['id'] as int,
    dayOfWeek:   j['day_of_week'] as String,
    startTime:   j['start_time'] as String,
    endTime:     j['end_time'] as String,
    programName: j['program_name'] as String,
    description: j['description'] as String?,
    isActive:    (j['is_active'] as int? ?? 1) == 1,
  );
}

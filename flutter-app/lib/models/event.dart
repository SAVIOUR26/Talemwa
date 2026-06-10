class Event {
  final int     id;
  final String  title;
  final String? description;
  final String  eventDate;
  final String? eventTime;
  final String? location;
  final bool    isOnline;
  final String? streamUrl;
  final String  createdAt;

  const Event({
    required this.id,
    required this.title,
    this.description,
    required this.eventDate,
    this.eventTime,
    this.location,
    this.isOnline = false,
    this.streamUrl,
    required this.createdAt,
  });

  factory Event.fromJson(Map<String, dynamic> j) => Event(
    id:          j['id'] as int,
    title:       j['title'] as String,
    description: j['description'] as String?,
    eventDate:   j['event_date'] as String,
    eventTime:   j['event_time'] as String?,
    location:    j['location'] as String?,
    isOnline:    (j['is_online'] as int? ?? 0) == 1,
    streamUrl:   j['stream_url'] as String?,
    createdAt:   j['created_at'] as String? ?? '',
  );

  DateTime get dateTime => DateTime.tryParse(eventDate) ?? DateTime.now();

  String get formattedDate {
    final d = dateTime;
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const days   = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
    return '${days[d.weekday - 1]}, ${d.day} ${months[d.month - 1]} ${d.year}';
  }
}

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme.dart';
import '../../providers/live_provider.dart';
import '../../providers/sermon_provider.dart';
import '../../providers/radio_provider.dart';
import '../../providers/event_provider.dart';
import '../../providers/audio_provider.dart';
import '../../models/sermon.dart';
import '../../models/event.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final live    = ref.watch(liveProvider);
    final radio   = ref.watch(radioProvider);
    final sermons = ref.watch(sermonListProvider);
    final events  = ref.watch(eventsProvider);

    return Scaffold(
      backgroundColor: AppColors.surface,
      body: RefreshIndicator(
        onRefresh: () async {
          ref.read(liveProvider.notifier).refresh();
          ref.read(sermonListProvider.notifier).refresh();
          ref.read(radioProvider.notifier).refresh();
          ref.invalidate(eventsProvider);
        },
        child: CustomScrollView(
          slivers: [
            SliverAppBar(
              expandedHeight: 120,
              pinned: true,
              backgroundColor: AppColors.navy,
              flexibleSpace: FlexibleSpaceBar(
                title: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Text('Talemwa',
                        style: TextStyle(color: AppColors.gold, fontWeight: FontWeight.bold, fontSize: 20)),
                  ],
                ),
                centerTitle: false,
                titlePadding: const EdgeInsets.only(left: 16, bottom: 16),
              ),
            ),

            // Live banner
            if (live.isLive)
              SliverToBoxAdapter(child: _LiveBanner(youtubeId: live.youtubeId, title: live.title)),

            // Radio mini strip
            if (radio.isOnline)
              SliverToBoxAdapter(child: _RadioStrip(radio: radio, ref: ref)),

            // Quick actions
            SliverToBoxAdapter(child: _QuickActions()),

            // Featured sermon
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 8),
                child: Text('Latest Sermon',
                    style: TextStyle(color: AppColors.navy, fontSize: 17, fontWeight: FontWeight.bold)),
              ),
            ),
            if (sermons.sermons.isNotEmpty)
              SliverToBoxAdapter(
                child: _SermonCard(sermon: sermons.sermons.first, ref: ref),
              ),

            // Upcoming events
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 24, 16, 8),
                child: Text('Upcoming Events',
                    style: TextStyle(color: AppColors.navy, fontSize: 17, fontWeight: FontWeight.bold)),
              ),
            ),
            events.when(
              loading: () => const SliverToBoxAdapter(child: SizedBox(height: 80, child: Center(child: CircularProgressIndicator()))),
              error:   (_, __) => const SliverToBoxAdapter(child: SizedBox()),
              data:    (list) => SliverToBoxAdapter(child: _EventsStrip(events: list.take(5).toList())),
            ),

            const SliverToBoxAdapter(child: SizedBox(height: 24)),
          ],
        ),
      ),
    );
  }
}

class _LiveBanner extends StatelessWidget {
  final String? youtubeId;
  final String  title;
  const _LiveBanner({this.youtubeId, required this.title});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => context.go('/live'),
      child: Container(
        color: AppColors.liveRed,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        child: Row(
          children: [
            const _PulsingDot(),
            const SizedBox(width: 10),
            const Text('LIVE', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 13)),
            const SizedBox(width: 8),
            Expanded(child: Text(title,
                style: const TextStyle(color: Colors.white, fontSize: 13),
                maxLines: 1, overflow: TextOverflow.ellipsis)),
            const Icon(Icons.arrow_forward_ios, color: Colors.white, size: 14),
          ],
        ),
      ),
    );
  }
}

class _PulsingDot extends StatefulWidget {
  const _PulsingDot();
  @override State<_PulsingDot> createState() => _PulsingDotState();
}

class _PulsingDotState extends State<_PulsingDot> with SingleTickerProviderStateMixin {
  late final AnimationController _ctrl;
  late final Animation<double>   _anim;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(vsync: this, duration: const Duration(milliseconds: 900))
      ..repeat(reverse: true);
    _anim = Tween<double>(begin: 0.4, end: 1.0).animate(_ctrl);
  }

  @override void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) => FadeTransition(
    opacity: _anim,
    child: const CircleAvatar(radius: 5, backgroundColor: Colors.white),
  );
}

class _RadioStrip extends StatelessWidget {
  final dynamic radio;
  final WidgetRef ref;
  const _RadioStrip({required this.radio, required this.ref});

  @override
  Widget build(BuildContext context) {
    final audio = ref.watch(audioProvider);
    final isPlaying = audio.isRadio && audio.isPlaying;

    return GestureDetector(
      onTap: () => context.go('/radio'),
      child: Container(
        margin: const EdgeInsets.all(12),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
        decoration: BoxDecoration(
          color: AppColors.navy,
          borderRadius: BorderRadius.circular(12),
        ),
        child: Row(
          children: [
            const Icon(Icons.radio, color: AppColors.gold, size: 20),
            const SizedBox(width: 10),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(radio.nowPlaying.title,
                      style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w600),
                      maxLines: 1, overflow: TextOverflow.ellipsis),
                  if (radio.nowPlaying.artist.isNotEmpty)
                    Text(radio.nowPlaying.artist,
                        style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 11),
                        maxLines: 1, overflow: TextOverflow.ellipsis),
                ],
              ),
            ),
            IconButton(
              icon: Icon(isPlaying ? Icons.pause_circle : Icons.play_circle,
                  color: AppColors.gold, size: 32),
              onPressed: () {
                if (isPlaying) {
                  ref.read(audioProvider.notifier).togglePlay();
                } else {
                  ref.read(audioProvider.notifier).playRadio(
                    radio.streamUrl,
                    title: radio.nowPlaying.title,
                    artist: radio.nowPlaying.artist,
                  );
                }
              },
            ),
          ],
        ),
      ),
    );
  }
}

class _QuickActions extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final actions = [
      (label: 'Sermons',  icon: Icons.mic,            path: '/sermons'),
      (label: 'Give',     icon: Icons.favorite,        path: '/give'),
      (label: 'Prayer',   icon: Icons.volunteer_activism, path: '/more/prayer'),
      (label: 'Events',   icon: Icons.event,           path: '/more/events'),
    ];

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      child: Row(
        children: actions.map((a) => Expanded(
          child: GestureDetector(
            onTap: () => context.go(a.path),
            child: Container(
              margin: const EdgeInsets.symmetric(horizontal: 4),
              padding: const EdgeInsets.symmetric(vertical: 14),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 4)],
              ),
              child: Column(
                children: [
                  Icon(a.icon, color: AppColors.navy, size: 24),
                  const SizedBox(height: 4),
                  Text(a.label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600)),
                ],
              ),
            ),
          ),
        )).toList(),
      ),
    );
  }
}

class _SermonCard extends StatelessWidget {
  final Sermon sermon;
  final WidgetRef ref;
  const _SermonCard({required this.sermon, required this.ref});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => context.go('/sermons/${sermon.id}'),
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 6)],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            if (sermon.thumbnailUrl != null)
              ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                child: Image.network(sermon.thumbnailUrl!,
                    height: 180, width: double.infinity, fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(height: 180, color: AppColors.navyLight,
                        child: const Icon(Icons.mic, color: AppColors.gold, size: 48))),
              ),
            Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (sermon.series != null)
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                      margin: const EdgeInsets.only(bottom: 6),
                      decoration: BoxDecoration(
                        color: AppColors.gold.withOpacity(0.15),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(sermon.series!,
                          style: const TextStyle(color: AppColors.gold, fontSize: 11, fontWeight: FontWeight.w600)),
                    ),
                  Text(sermon.title,
                      style: const TextStyle(color: AppColors.navy, fontSize: 16, fontWeight: FontWeight.bold),
                      maxLines: 2, overflow: TextOverflow.ellipsis),
                  const SizedBox(height: 4),
                  Text(sermon.speaker,
                      style: TextStyle(color: Colors.grey[600], fontSize: 13)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _EventsStrip extends StatelessWidget {
  final List<Event> events;
  const _EventsStrip({required this.events});

  @override
  Widget build(BuildContext context) {
    if (events.isEmpty) {
      return const Padding(
        padding: EdgeInsets.symmetric(horizontal: 16),
        child: Text('No upcoming events.', style: TextStyle(color: Colors.grey)),
      );
    }
    return SizedBox(
      height: 120,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: events.length,
        separatorBuilder: (_, __) => const SizedBox(width: 10),
        itemBuilder: (ctx, i) {
          final e = events[i];
          return GestureDetector(
            onTap: () => ctx.go('/events/${e.id}'),
            child: Container(
              width: 180,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: AppColors.navy,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(e.formattedDate,
                      style: const TextStyle(color: AppColors.gold, fontSize: 11, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 6),
                  Text(e.title,
                      style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w600),
                      maxLines: 2, overflow: TextOverflow.ellipsis),
                  const Spacer(),
                  Text(e.location ?? (e.isOnline ? 'Online' : ''),
                      style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 11),
                      maxLines: 1, overflow: TextOverflow.ellipsis),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

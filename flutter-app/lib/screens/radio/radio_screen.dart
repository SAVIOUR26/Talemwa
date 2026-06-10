import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/theme.dart';
import '../../providers/radio_provider.dart';
import '../../providers/audio_provider.dart';
import '../../models/radio_status.dart';

class RadioScreen extends ConsumerWidget {
  const RadioScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final radio  = ref.watch(radioProvider);
    final audio  = ref.watch(audioProvider);
    final isPlaying = audio.isRadio && audio.isPlaying;

    return DefaultTabController(
      length: 2,
      child: Scaffold(
        backgroundColor: AppColors.navy,
        appBar: AppBar(
          backgroundColor: AppColors.navy,
          title: const Text('Ministry Radio', style: TextStyle(color: Colors.white)),
          iconTheme: const IconThemeData(color: Colors.white),
          bottom: const TabBar(
            labelColor:         AppColors.gold,
            unselectedLabelColor: Colors.white54,
            indicatorColor:     AppColors.gold,
            tabs: [
              Tab(text: 'Now Playing'),
              Tab(text: 'Schedule'),
            ],
          ),
        ),
        body: TabBarView(
          children: [
            _NowPlayingTab(radio: radio, isPlaying: isPlaying, ref: ref),
            _ScheduleTab(),
          ],
        ),
      ),
    );
  }
}

class _NowPlayingTab extends StatelessWidget {
  final RadioStatus radio;
  final bool isPlaying;
  final WidgetRef ref;
  const _NowPlayingTab({required this.radio, required this.isPlaying, required this.ref});

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        // Art
        Container(
          width: 180, height: 180,
          decoration: BoxDecoration(
            color: AppColors.navyLight,
            shape: BoxShape.circle,
            boxShadow: [BoxShadow(color: AppColors.gold.withOpacity(0.3), blurRadius: 40)],
          ),
          child: radio.nowPlaying.art != null
              ? ClipOval(child: Image.network(radio.nowPlaying.art!, fit: BoxFit.cover))
              : const Icon(Icons.radio, color: AppColors.gold, size: 72),
        ),
        const SizedBox(height: 32),
        Text(radio.nowPlaying.title,
            style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
            textAlign: TextAlign.center),
        if (radio.nowPlaying.artist.isNotEmpty) ...[
          const SizedBox(height: 6),
          Text(radio.nowPlaying.artist,
              style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 15),
              textAlign: TextAlign.center),
        ],
        const SizedBox(height: 8),
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 8, height: 8,
              decoration: BoxDecoration(
                color: radio.isOnline ? Colors.greenAccent : Colors.red,
                shape: BoxShape.circle,
              ),
            ),
            const SizedBox(width: 6),
            Text(radio.isOnline ? '${radio.listeners} listening now' : 'Offline',
                style: TextStyle(color: Colors.white.withOpacity(0.5), fontSize: 13)),
          ],
        ),
        const SizedBox(height: 40),

        // Play/pause
        GestureDetector(
          onTap: () {
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
          child: Container(
            width: 72, height: 72,
            decoration: const BoxDecoration(color: AppColors.gold, shape: BoxShape.circle),
            child: Icon(isPlaying ? Icons.pause : Icons.play_arrow,
                color: Colors.white, size: 36),
          ),
        ),
        const SizedBox(height: 24),
        if (!radio.isOnline)
          Text('Stream offline', style: TextStyle(color: Colors.white.withOpacity(0.4), fontSize: 12)),
      ],
    );
  }
}

class _ScheduleTab extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final schedule = ref.watch(radioScheduleProvider);
    return schedule.when(
      loading: () => const Center(child: CircularProgressIndicator(color: AppColors.gold)),
      error:   (e, _) => Center(child: Text('Error: $e', style: const TextStyle(color: Colors.white))),
      data:    (slots) => _ScheduleList(slots: slots),
    );
  }
}

class _ScheduleList extends StatelessWidget {
  final List<RadioScheduleSlot> slots;
  const _ScheduleList({required this.slots});

  static const _days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday','daily'];

  @override
  Widget build(BuildContext context) {
    final byDay = <String, List<RadioScheduleSlot>>{};
    for (final s in slots) {
      (byDay[s.dayOfWeek] ??= []).add(s);
    }

    final days = _days.where((d) => byDay.containsKey(d)).toList();

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: days.length,
      separatorBuilder: (_, __) => const SizedBox(height: 16),
      itemBuilder: (ctx, i) {
        final day  = days[i];
        final list = byDay[day]!;
        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(day[0].toUpperCase() + day.substring(1),
                style: const TextStyle(color: AppColors.gold, fontSize: 15, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            ...list.map((s) => Container(
              margin: const EdgeInsets.only(bottom: 6),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
              decoration: BoxDecoration(
                color: AppColors.navyLight,
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  Text('${s.startTime} – ${s.endTime}',
                      style: TextStyle(color: Colors.white.withOpacity(0.6), fontSize: 12)),
                  const SizedBox(width: 12),
                  Expanded(child: Text(s.programName,
                      style: const TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.w500))),
                ],
              ),
            )),
          ],
        );
      },
    );
  }
}

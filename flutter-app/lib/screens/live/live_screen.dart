import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:youtube_player_flutter/youtube_player_flutter.dart';
import '../../core/theme.dart';
import '../../providers/live_provider.dart';
import '../../providers/radio_provider.dart';
import '../../providers/audio_provider.dart';
import '../../providers/sermon_provider.dart';
import '../../models/sermon.dart';
import 'package:go_router/go_router.dart';

class LiveScreen extends ConsumerStatefulWidget {
  const LiveScreen({super.key});
  @override ConsumerState<LiveScreen> createState() => _LiveScreenState();
}

class _LiveScreenState extends ConsumerState<LiveScreen> {
  YoutubePlayerController? _ytCtrl;
  bool _audioOnly = false;

  @override
  void dispose() {
    _ytCtrl?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final live  = ref.watch(liveProvider);
    final radio = ref.watch(radioProvider);
    final audio = ref.watch(audioProvider);

    if (live.isLive && live.youtubeId != null && _ytCtrl == null) {
      _ytCtrl = YoutubePlayerController(
        initialVideoId: live.youtubeId!,
        flags: const YoutubePlayerFlags(autoPlay: true, mute: false),
      );
    }

    if (!live.isLive) {
      return _OffAirScreen();
    }

    return Scaffold(
      backgroundColor: AppColors.navy,
      appBar: AppBar(
        backgroundColor: AppColors.navy,
        title: Text(live.title,
            style: const TextStyle(color: Colors.white), overflow: TextOverflow.ellipsis),
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            icon: const Icon(Icons.share_outlined, color: Colors.white),
            onPressed: () {/* share youtube url */},
          ),
        ],
      ),
      body: Column(
        children: [
          if (!_audioOnly && _ytCtrl != null)
            YoutubePlayer(controller: _ytCtrl!, showVideoProgressIndicator: true),

          // Audio-only toggle
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: Row(
              children: [
                Switch(
                  value: _audioOnly,
                  onChanged: (v) {
                    setState(() => _audioOnly = v);
                    if (v) {
                      _ytCtrl?.pause();
                      ref.read(audioProvider.notifier).playRadio(radio.streamUrl,
                          title: live.title, artist: 'Pastor Robert Talemwa');
                    } else {
                      ref.read(audioProvider.notifier).stop();
                      _ytCtrl?.play();
                    }
                  },
                  activeColor: AppColors.gold,
                ),
                const SizedBox(width: 8),
                const Text('Audio only (save data)',
                    style: TextStyle(color: Colors.white70, fontSize: 14)),
              ],
            ),
          ),
          const Divider(color: Colors.white12),
          Padding(
            padding: const EdgeInsets.all(16),
            child: Text(live.title,
                style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }
}

class _OffAirScreen extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final sermons = ref.watch(sermonListProvider);

    return Scaffold(
      backgroundColor: AppColors.navy,
      appBar: AppBar(
        backgroundColor: AppColors.navy,
        title: const Text('Live Stream', style: TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: CustomScrollView(
        slivers: [
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(32),
              child: Column(
                children: [
                  const Icon(Icons.tv_off, color: Colors.white38, size: 72),
                  const SizedBox(height: 16),
                  const Text('We are not live right now',
                      style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  Text('Join us every Sunday for our live service.',
                      style: TextStyle(color: Colors.white.withOpacity(0.5), fontSize: 14),
                      textAlign: TextAlign.center),
                ],
              ),
            ),
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 8),
              child: Text('Previous Services',
                  style: const TextStyle(color: AppColors.gold, fontSize: 15, fontWeight: FontWeight.bold)),
            ),
          ),
          SliverList(
            delegate: SliverChildBuilderDelegate(
              (ctx, i) {
                final s = sermons.sermons[i];
                return ListTile(
                  leading: s.thumbnailUrl != null
                      ? Image.network(s.thumbnailUrl!, width: 56, height: 40, fit: BoxFit.cover)
                      : const Icon(Icons.mic, color: AppColors.gold),
                  title: Text(s.title,
                      style: const TextStyle(color: Colors.white, fontSize: 13),
                      maxLines: 1, overflow: TextOverflow.ellipsis),
                  subtitle: Text(s.speaker,
                      style: TextStyle(color: Colors.white.withOpacity(0.5), fontSize: 11)),
                  onTap: () => context.go('/sermons/${s.id}'),
                );
              },
              childCount: sermons.sermons.take(10).length,
            ),
          ),
        ],
      ),
    );
  }
}

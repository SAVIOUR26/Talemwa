import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:audio_service/audio_service.dart';
import '../../core/theme.dart';
import '../../providers/sermon_provider.dart';
import '../../providers/audio_provider.dart';
import '../../services/audio_service.dart';
import '../../models/sermon.dart';

class SermonDetailScreen extends ConsumerWidget {
  final int sermonId;
  const SermonDetailScreen({super.key, required this.sermonId});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final detail = ref.watch(sermonDetailProvider(sermonId));

    return Scaffold(
      backgroundColor: AppColors.surface,
      body: detail.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error:   (e, _) => Center(child: Text('Error: $e')),
        data:    (s) => _SermonDetailBody(sermon: s),
      ),
    );
  }
}

class _SermonDetailBody extends ConsumerStatefulWidget {
  final Sermon sermon;
  const _SermonDetailBody({required this.sermon});
  @override ConsumerState<_SermonDetailBody> createState() => _SermonDetailBodyState();
}

class _SermonDetailBodyState extends ConsumerState<_SermonDetailBody> {
  bool _descExpanded = false;

  @override
  Widget build(BuildContext context) {
    final sermon = widget.sermon;
    final audio  = ref.watch(audioProvider);
    final isThisSermon = audio.currentSermon?.id == sermon.id;
    final isPlaying    = isThisSermon && audio.isPlaying;

    return CustomScrollView(
      slivers: [
        SliverAppBar(
          expandedHeight: 220,
          pinned: true,
          backgroundColor: AppColors.navy,
          flexibleSpace: FlexibleSpaceBar(
            background: sermon.thumbnailUrl != null
                ? Image.network(sermon.thumbnailUrl!, fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(color: AppColors.navyLight,
                        child: const Icon(Icons.mic, color: AppColors.gold, size: 60)))
                : Container(color: AppColors.navyLight,
                    child: const Icon(Icons.mic, color: AppColors.gold, size: 60)),
          ),
        ),

        SliverToBoxAdapter(
          child: Container(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                if (sermon.series != null)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    margin: const EdgeInsets.only(bottom: 8),
                    decoration: BoxDecoration(
                      color: AppColors.gold.withOpacity(0.15),
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(sermon.series!,
                        style: const TextStyle(color: AppColors.gold, fontSize: 12, fontWeight: FontWeight.w600)),
                  ),
                Text(sermon.title,
                    style: const TextStyle(color: AppColors.navy, fontSize: 20, fontWeight: FontWeight.bold)),
                const SizedBox(height: 4),
                Text(sermon.speaker,
                    style: TextStyle(color: Colors.grey[600], fontSize: 14)),
                if (sermon.scripture != null) ...[
                  const SizedBox(height: 4),
                  Text(sermon.scripture!,
                      style: const TextStyle(color: AppColors.gold, fontSize: 13, fontStyle: FontStyle.italic)),
                ],
                const SizedBox(height: 16),

                // Play button
                if (sermon.mp3Url != null)
                  _AudioPlayerBar(sermon: sermon, isPlaying: isPlaying, isActive: isThisSermon, ref: ref),

                const SizedBox(height: 16),

                // Description
                if (sermon.description != null) ...[
                  GestureDetector(
                    onTap: () => setState(() => _descExpanded = !_descExpanded),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(sermon.description!,
                            maxLines: _descExpanded ? null : 3,
                            overflow: _descExpanded ? TextOverflow.visible : TextOverflow.ellipsis,
                            style: TextStyle(color: Colors.grey[700], fontSize: 14, height: 1.5)),
                        Text(_descExpanded ? 'Show less' : 'Show more',
                            style: const TextStyle(color: AppColors.gold, fontSize: 13, fontWeight: FontWeight.w600)),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                ],
              ],
            ),
          ),
        ),
      ],
    );
  }
}

class _AudioPlayerBar extends ConsumerStatefulWidget {
  final Sermon  sermon;
  final bool    isPlaying;
  final bool    isActive;
  final WidgetRef ref;
  const _AudioPlayerBar({required this.sermon, required this.isPlaying, required this.isActive, required this.ref});
  @override ConsumerState<_AudioPlayerBar> createState() => _AudioPlayerBarState();
}

class _AudioPlayerBarState extends ConsumerState<_AudioPlayerBar> {
  double _speed = 1.0;
  final _speeds = [0.75, 1.0, 1.25, 1.5];

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.navy,
        borderRadius: BorderRadius.circular(14),
      ),
      child: Column(
        children: [
          // Seek bar (only when active)
          if (widget.isActive)
            StreamBuilder<PlaybackState>(
              stream: audioHandler.playbackState,
              builder: (ctx, snap) {
                final ps  = snap.data;
                final pos = ps?.position ?? Duration.zero;
                final dur = audioHandler.mediaItem.value?.duration ?? Duration.zero;
                final pct = dur.inMilliseconds > 0
                    ? pos.inMilliseconds / dur.inMilliseconds
                    : 0.0;
                return Column(
                  children: [
                    Slider(
                      value:    pct.clamp(0, 1),
                      onChanged: (v) => ref.read(audioProvider.notifier)
                          .seek(Duration(milliseconds: (v * dur.inMilliseconds).toInt())),
                      activeColor:   AppColors.gold,
                      inactiveColor: Colors.white24,
                    ),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(_fmt(pos), style: const TextStyle(color: Colors.white54, fontSize: 11)),
                        Text(_fmt(dur), style: const TextStyle(color: Colors.white54, fontSize: 11)),
                      ],
                    ),
                    const SizedBox(height: 8),
                  ],
                );
              },
            ),

          Row(
            children: [
              // Speed selector
              GestureDetector(
                onTap: () {
                  final idx = (_speeds.indexOf(_speed) + 1) % _speeds.length;
                  setState(() => _speed = _speeds[idx]);
                  ref.read(audioProvider.notifier).setSpeed(_speed);
                },
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.white12,
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text('${_speed}x',
                      style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.bold)),
                ),
              ),
              const Spacer(),
              IconButton(
                icon: Icon(
                  widget.isPlaying ? Icons.pause_circle : Icons.play_circle,
                  color: AppColors.gold, size: 52,
                ),
                onPressed: () {
                  if (widget.isActive) {
                    ref.read(audioProvider.notifier).togglePlay();
                  } else {
                    ref.read(audioProvider.notifier).playSermon(widget.sermon);
                  }
                },
              ),
              const Spacer(),
              const SizedBox(width: 44),
            ],
          ),
        ],
      ),
    );
  }

  String _fmt(Duration d) {
    final h = d.inHours;
    final m = d.inMinutes.remainder(60).toString().padLeft(2, '0');
    final s = d.inSeconds.remainder(60).toString().padLeft(2, '0');
    return h > 0 ? '$h:$m:$s' : '$m:$s';
  }
}

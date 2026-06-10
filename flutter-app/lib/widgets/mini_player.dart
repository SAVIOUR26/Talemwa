import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../core/theme.dart';
import '../providers/audio_provider.dart';

class MiniPlayer extends ConsumerWidget {
  const MiniPlayer({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final audio  = ref.watch(audioProvider);
    final sermon = audio.currentSermon;
    if (sermon == null) return const SizedBox.shrink();

    return GestureDetector(
      onTap: () => context.go('/sermons/${sermon.id}'),
      child: Container(
        height: 64,
        decoration: BoxDecoration(
          color: AppColors.navy,
          boxShadow: [BoxShadow(color: Colors.black26, blurRadius: 8, offset: const Offset(0, -2))],
        ),
        padding: const EdgeInsets.symmetric(horizontal: 12),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(6),
              child: sermon.thumbnailUrl != null
                  ? Image.network(sermon.thumbnailUrl!, width: 44, height: 44, fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => _placeholder())
                  : _placeholder(),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(sermon.title,
                      style: const TextStyle(color: Colors.white, fontSize: 13, fontWeight: FontWeight.w600),
                      maxLines: 1, overflow: TextOverflow.ellipsis),
                  Text(sermon.speaker,
                      style: TextStyle(color: Colors.white.withOpacity(0.7), fontSize: 11),
                      maxLines: 1, overflow: TextOverflow.ellipsis),
                ],
              ),
            ),
            IconButton(
              icon: Icon(audio.isPlaying ? Icons.pause : Icons.play_arrow, color: AppColors.gold),
              onPressed: () => ref.read(audioProvider.notifier).togglePlay(),
            ),
            IconButton(
              icon: const Icon(Icons.close, color: Colors.white54, size: 20),
              onPressed: () => ref.read(audioProvider.notifier).stop(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _placeholder() => Container(
    width: 44, height: 44,
    color: AppColors.navyLight,
    child: const Icon(Icons.mic, color: AppColors.gold, size: 20),
  );
}

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../core/theme.dart';
import '../../providers/event_provider.dart';

class EventDetailScreen extends ConsumerWidget {
  final int eventId;
  const EventDetailScreen({super.key, required this.eventId});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final detail = ref.watch(eventDetailProvider(eventId));
    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        backgroundColor: AppColors.navy,
        title: const Text('Event', style: TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: detail.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error:   (e, _) => Center(child: Text('Error: $e')),
        data: (event) => SingleChildScrollView(
          padding: const EdgeInsets.all(20),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: AppColors.navy,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.calendar_today, color: AppColors.gold, size: 36),
                    const SizedBox(width: 14),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(event.formattedDate,
                            style: const TextStyle(color: AppColors.gold, fontWeight: FontWeight.bold, fontSize: 15)),
                        if (event.eventTime != null)
                          Text(event.eventTime!,
                              style: const TextStyle(color: Colors.white70, fontSize: 13)),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              Text(event.title,
                  style: const TextStyle(color: AppColors.navy, fontSize: 22, fontWeight: FontWeight.bold)),
              const SizedBox(height: 12),
              if (event.location != null) ...[
                Row(children: [
                  const Icon(Icons.location_on_outlined, color: AppColors.gold, size: 18),
                  const SizedBox(width: 6),
                  Expanded(child: Text(event.location!,
                      style: TextStyle(color: Colors.grey[700], fontSize: 14))),
                ]),
                const SizedBox(height: 8),
              ],
              if (event.isOnline) ...[
                Row(children: [
                  const Icon(Icons.live_tv, color: AppColors.gold, size: 18),
                  const SizedBox(width: 6),
                  const Text('Online Event', style: TextStyle(color: AppColors.gold, fontSize: 14, fontWeight: FontWeight.w600)),
                ]),
                const SizedBox(height: 8),
              ],
              if (event.description != null) ...[
                const SizedBox(height: 8),
                Text(event.description!, style: TextStyle(color: Colors.grey[700], fontSize: 15, height: 1.6)),
              ],
              if (event.isOnline && event.streamUrl != null) ...[
                const SizedBox(height: 24),
                SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton.icon(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.liveRed,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                    ),
                    icon: const Icon(Icons.play_circle, color: Colors.white),
                    label: const Text('Watch Online', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                    onPressed: () async {
                      final uri = Uri.parse(event.streamUrl!);
                      if (await canLaunchUrl(uri)) launchUrl(uri, mode: LaunchMode.externalApplication);
                    },
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

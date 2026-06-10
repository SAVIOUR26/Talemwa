import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:package_info_plus/package_info_plus.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/theme.dart';

class MoreScreen extends ConsumerWidget {
  const MoreScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        backgroundColor: AppColors.navy,
        title: const Text('More', style: TextStyle(color: Colors.white)),
      ),
      body: ListView(
        children: [
          _Section(title: 'Ministry', items: [
            _Item(icon: Icons.volunteer_activism, label: 'Prayer Request',   onTap: () => context.go('/more/prayer')),
            _Item(icon: Icons.event,              label: 'Events',            onTap: () => context.go('/more/events')),
            _Item(icon: Icons.info_outline,       label: 'About Pastor Robert', onTap: () => context.go('/more/about')),
          ]),
          _Section(title: 'Follow Us', items: [
            _Item(icon: Icons.play_circle_outline, label: 'YouTube',   onTap: () => _launch('https://www.youtube.com/@pastortalemwarobert4160')),
            _Item(icon: Icons.facebook,            label: 'Facebook',  onTap: () => _launch('https://facebook.com/pastortalemwa')),
            _Item(icon: Icons.music_note,          label: 'TikTok',    onTap: () => _launch('https://tiktok.com/@pastortalemwa')),
          ]),
          _Section(title: 'App', items: [
            _Item(icon: Icons.notifications_outlined, label: 'Notification Preferences',
                onTap: () => _showNotifPrefs(context)),
            _Item(icon: Icons.brightness_6_outlined, label: 'Dark Mode',
                onTap: () {}),
          ]),
          const SizedBox(height: 12),
          FutureBuilder<PackageInfo>(
            future: PackageInfo.fromPlatform(),
            builder: (ctx, snap) => Center(
              child: Text('Talemwa v${snap.data?.version ?? '…'}',
                  style: TextStyle(color: Colors.grey[400], fontSize: 12)),
            ),
          ),
          const SizedBox(height: 24),
        ],
      ),
    );
  }

  void _launch(String url) async {
    final uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) launchUrl(uri, mode: LaunchMode.externalApplication);
  }

  void _showNotifPrefs(BuildContext context) {
    showModalBottomSheet(
      context: context,
      builder: (_) => const _NotifPrefsSheet(),
    );
  }
}

class _Section extends StatelessWidget {
  final String      title;
  final List<_Item> items;
  const _Section({required this.title, required this.items});

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 20, 16, 6),
          child: Text(title.toUpperCase(),
              style: TextStyle(color: Colors.grey[500], fontSize: 11, fontWeight: FontWeight.bold,
                  letterSpacing: 1.2)),
        ),
        Container(
          color: Colors.white,
          child: Column(
            children: items.map((item) => ListTile(
              leading:  Icon(item.icon, color: AppColors.navy, size: 22),
              title:    Text(item.label, style: const TextStyle(fontSize: 15)),
              trailing: const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
              onTap:    item.onTap,
            )).toList(),
          ),
        ),
      ],
    );
  }
}

class _Item {
  final IconData   icon;
  final String     label;
  final VoidCallback onTap;
  const _Item({required this.icon, required this.label, required this.onTap});
}

class _NotifPrefsSheet extends StatefulWidget {
  const _NotifPrefsSheet();
  @override State<_NotifPrefsSheet> createState() => _NotifPrefsSheetState();
}

class _NotifPrefsSheetState extends State<_NotifPrefsSheet> {
  bool _sermons = true;
  bool _live    = true;
  bool _events  = true;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text('Notification Preferences',
              style: TextStyle(fontSize: 17, fontWeight: FontWeight.bold, color: AppColors.navy)),
          const SizedBox(height: 16),
          SwitchListTile(title: const Text('New Sermons'),  value: _sermons, onChanged: (v) => setState(() => _sermons = v), activeColor: AppColors.gold),
          SwitchListTile(title: const Text('Live Streams'), value: _live,    onChanged: (v) => setState(() => _live    = v), activeColor: AppColors.gold),
          SwitchListTile(title: const Text('Events'),       value: _events,  onChanged: (v) => setState(() => _events  = v), activeColor: AppColors.gold),
          const SizedBox(height: 8),
        ],
      ),
    );
  }
}

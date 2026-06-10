import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../core/theme.dart';

class AboutScreen extends StatelessWidget {
  const AboutScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        backgroundColor: AppColors.navy,
        title: const Text('About Pastor Robert', style: TextStyle(color: Colors.white)),
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Hero
            Container(
              width: double.infinity,
              color: AppColors.navy,
              padding: const EdgeInsets.fromLTRB(24, 32, 24, 32),
              child: Column(
                children: [
                  CircleAvatar(
                    radius: 56,
                    backgroundColor: AppColors.navyLight,
                    child: const Icon(Icons.person, color: AppColors.gold, size: 56),
                  ),
                  const SizedBox(height: 16),
                  const Text('Pastor Robert Talemwa',
                      style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 4),
                  const Text('Missionary Preacher · Kampala, Uganda',
                      style: TextStyle(color: AppColors.gold, fontSize: 13)),
                ],
              ),
            ),

            // Mission statement
            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Mission',
                      style: TextStyle(color: AppColors.navy, fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  const Text(
                    '"Preach the gospel and take healing to nations"',
                    style: TextStyle(color: AppColors.gold, fontSize: 15, fontStyle: FontStyle.italic, height: 1.5),
                  ),
                  const SizedBox(height: 24),
                  const Text('About',
                      style: TextStyle(color: AppColors.navy, fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 8),
                  const Text(
                    'Pastor Robert Talemwa is a missionary preacher based in Kampala, Uganda. '
                    'He leads a congregation of believers and reaches thousands across Uganda '
                    'and the diaspora — including the UK, US, Canada, and Europe — through '
                    'live streaming, sermon archives, and online radio.\n\n'
                    'His ministry is built on the foundation of healing, deliverance, '
                    'and the uncompromising preaching of the gospel of Jesus Christ.',
                    style: TextStyle(fontSize: 14, height: 1.7, color: Color(0xFF444444)),
                  ),
                  const SizedBox(height: 24),

                  // Pillars
                  const Text('Ministry Pillars',
                      style: TextStyle(color: AppColors.navy, fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 12),
                  ...[
                    (Icons.menu_book,   'The Gospel',    'Uncompromising preaching of God\'s Word'),
                    (Icons.healing,     'Healing',       'Physical and spiritual healing to the nations'),
                    (Icons.groups,      'Community',     'Building a family of believers worldwide'),
                    (Icons.public,      'Nations',       'Taking the message beyond Uganda to the world'),
                  ].map((p) => Padding(
                    padding: const EdgeInsets.only(bottom: 12),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: AppColors.gold.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Icon(p.$1, color: AppColors.gold, size: 22),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(p.$2, style: const TextStyle(fontWeight: FontWeight.bold, color: AppColors.navy)),
                              Text(p.$3, style: TextStyle(color: Colors.grey[600], fontSize: 13)),
                            ],
                          ),
                        ),
                      ],
                    ),
                  )),

                  const SizedBox(height: 24),
                  const Text('Connect',
                      style: TextStyle(color: AppColors.navy, fontSize: 16, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 12),
                  _SocialBtn(icon: Icons.play_circle_fill, label: 'YouTube',  color: const Color(0xFFFF0000),
                      url: 'https://www.youtube.com/@pastortalemwarobert4160'),
                  const SizedBox(height: 8),
                  _SocialBtn(icon: Icons.facebook, label: 'Facebook', color: const Color(0xFF1877F2),
                      url: 'https://facebook.com/pastortalemwa'),
                  const SizedBox(height: 8),
                  _SocialBtn(icon: Icons.music_note, label: 'TikTok', color: Colors.black,
                      url: 'https://tiktok.com/@pastortalemwa'),
                  const SizedBox(height: 32),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _SocialBtn extends StatelessWidget {
  final IconData icon;
  final String   label;
  final Color    color;
  final String   url;
  const _SocialBtn({required this.icon, required this.label, required this.color, required this.url});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      height: 48,
      child: OutlinedButton.icon(
        style: OutlinedButton.styleFrom(
          side: BorderSide(color: color.withOpacity(0.4)),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
        icon: Icon(icon, color: color, size: 20),
        label: Text(label, style: TextStyle(color: color, fontWeight: FontWeight.w600)),
        onPressed: () async {
          final uri = Uri.parse(url);
          if (await canLaunchUrl(uri)) launchUrl(uri, mode: LaunchMode.externalApplication);
        },
      ),
    );
  }
}

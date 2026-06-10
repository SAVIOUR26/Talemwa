import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../core/theme.dart';
import '../providers/audio_provider.dart';
import 'mini_player.dart';

class ScaffoldWithNav extends ConsumerWidget {
  final Widget child;
  const ScaffoldWithNav({super.key, required this.child});

  static const _tabs = [
    (label: 'Home',    icon: Icons.home_outlined,       active: Icons.home,             path: '/'),
    (label: 'Sermons', icon: Icons.mic_none_outlined,   active: Icons.mic,              path: '/sermons'),
    (label: 'Radio',   icon: Icons.radio_outlined,      active: Icons.radio,            path: '/radio'),
    (label: 'Give',    icon: Icons.favorite_outline,    active: Icons.favorite,         path: '/give'),
    (label: 'More',    icon: Icons.menu_outlined,       active: Icons.menu,             path: '/more'),
  ];

  int _tabIndex(String location) {
    if (location.startsWith('/sermons')) return 1;
    if (location.startsWith('/radio'))   return 2;
    if (location.startsWith('/give'))    return 3;
    if (location.startsWith('/more') || location.startsWith('/events') || location.startsWith('/live')) return 4;
    return 0;
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final location  = GoRouterState.of(context).matchedLocation;
    final current   = _tabIndex(location);
    final hasPlayer = ref.watch(audioProvider).currentSermon != null;

    return Scaffold(
      body: Column(
        children: [
          Expanded(child: child),
          if (hasPlayer) const MiniPlayer(),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: current,
        onDestinationSelected: (i) => context.go(_tabs[i].path),
        destinations: _tabs.map((t) => NavigationDestination(
          icon:          Icon(t.icon),
          selectedIcon:  Icon(t.active, color: AppColors.gold),
          label:         t.label,
        )).toList(),
      ),
    );
  }
}

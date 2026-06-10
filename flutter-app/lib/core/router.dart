import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../screens/home/home_screen.dart';
import '../screens/sermons/sermons_screen.dart';
import '../screens/sermons/sermon_detail_screen.dart';
import '../screens/radio/radio_screen.dart';
import '../screens/live/live_screen.dart';
import '../screens/give/give_screen.dart';
import '../screens/more/more_screen.dart';
import '../screens/more/prayer_screen.dart';
import '../screens/more/events_screen.dart';
import '../screens/more/event_detail_screen.dart';
import '../screens/more/about_screen.dart';
import '../widgets/scaffold_with_nav.dart';

final appRouter = GoRouter(
  initialLocation: '/',
  routes: [
    ShellRoute(
      builder: (context, state, child) => ScaffoldWithNav(child: child),
      routes: [
        GoRoute(path: '/',         builder: (c, s) => const HomeScreen()),
        GoRoute(path: '/sermons',  builder: (c, s) => const SermonsScreen()),
        GoRoute(path: '/radio',    builder: (c, s) => const RadioScreen()),
        GoRoute(path: '/give',     builder: (c, s) => const GiveScreen()),
        GoRoute(path: '/more',     builder: (c, s) => const MoreScreen()),
        GoRoute(path: '/more/prayer', builder: (c, s) => const PrayerScreen()),
        GoRoute(path: '/more/events', builder: (c, s) => const EventsScreen()),
        GoRoute(path: '/more/about',  builder: (c, s) => const AboutScreen()),
        GoRoute(
          path: '/sermons/:id',
          builder: (c, s) => SermonDetailScreen(
            sermonId: int.parse(s.pathParameters['id']!),
          ),
        ),
        GoRoute(
          path: '/events/:id',
          builder: (c, s) => EventDetailScreen(
            eventId: int.parse(s.pathParameters['id']!),
          ),
        ),
        GoRoute(path: '/live', builder: (c, s) => const LiveScreen()),
      ],
    ),
  ],
  redirect: (context, state) => null,
);

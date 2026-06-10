import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:hive_flutter/hive_flutter.dart';
import 'core/router.dart';
import 'core/theme.dart';
import 'core/constants.dart';
import 'services/audio_service.dart';
import 'services/notification_service.dart';

final _navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await Firebase.initializeApp();
  await Hive.initFlutter();
  await Hive.openBox(AppConstants.sermonBox);
  await Hive.openBox(AppConstants.eventBox);
  await Hive.openBox(AppConstants.liveBox);
  await Hive.openBox(AppConstants.settingsBox);
  await initAudioService();
  await NotificationService.init(_navigatorKey);
  runApp(const ProviderScope(child: TalemwaApp()));
}

class TalemwaApp extends StatelessWidget {
  const TalemwaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title:                    'Talemwa',
      theme:                    AppTheme.light,
      darkTheme:                AppTheme.dark,
      themeMode:                ThemeMode.system,
      routerConfig:             appRouter,
      debugShowCheckedModeBanner: false,
    );
  }
}

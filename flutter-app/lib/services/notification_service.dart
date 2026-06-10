import 'dart:io';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:go_router/go_router.dart';
import 'api_service.dart';

final _localNotifications = FlutterLocalNotificationsPlugin();
GlobalKey<NavigatorState>? _navigatorKey;

Future<void> _firebaseBackgroundHandler(RemoteMessage message) async {
  // Background handler — no UI work
}

class NotificationService {
  static Future<void> init(GlobalKey<NavigatorState> navKey) async {
    _navigatorKey = navKey;

    FirebaseMessaging.onBackgroundMessage(_firebaseBackgroundHandler);

    const android = AndroidInitializationSettings('@mipmap/ic_launcher');
    const ios     = DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );
    await _localNotifications.initialize(
      const InitializationSettings(android: android, iOS: ios),
      onDidReceiveNotificationResponse: (details) {
        if (details.payload != null) _handlePayload(details.payload!);
      },
    );

    await FirebaseMessaging.instance.requestPermission();

    final token = await FirebaseMessaging.instance.getToken();
    if (token != null) await _registerToken(token);

    FirebaseMessaging.instance.onTokenRefresh.listen(_registerToken);

    FirebaseMessaging.onMessage.listen((msg) {
      final n = msg.notification;
      if (n == null) return;
      _localNotifications.show(
        msg.hashCode,
        n.title,
        n.body,
        NotificationDetails(
          android: AndroidNotificationDetails(
            'talemwa_general', 'Talemwa',
            importance: Importance.high,
            priority:   Priority.high,
          ),
          iOS: const DarwinNotificationDetails(),
        ),
        payload: _payloadFromData(msg.data),
      );
    });

    FirebaseMessaging.onMessageOpenedApp.listen((msg) {
      _routeFromData(msg.data);
    });

    final initial = await FirebaseMessaging.instance.getInitialMessage();
    if (initial != null) _routeFromData(initial.data);
  }

  static Future<void> _registerToken(String token) async {
    try {
      final platform = Platform.isAndroid ? 'android' : 'ios';
      await ApiService.registerDeviceToken(token, platform);
    } catch (_) {}
  }

  static String _payloadFromData(Map<String, dynamic> data) {
    final type = data['type'] as String? ?? 'general';
    final id   = data['id']   as String? ?? '';
    return '$type:$id';
  }

  static void _handlePayload(String payload) {
    final parts = payload.split(':');
    final type  = parts[0];
    final id    = parts.length > 1 ? parts[1] : '';
    _navigate(type, id);
  }

  static void _routeFromData(Map<String, dynamic> data) {
    final type = data['type'] as String? ?? 'general';
    final id   = data['id']   as String? ?? '';
    _navigate(type, id);
  }

  static void _navigate(String type, String id) {
    final ctx = _navigatorKey?.currentContext;
    if (ctx == null) return;
    switch (type) {
      case 'live':
        GoRouter.of(ctx).go('/live');
      case 'sermon':
        if (id.isNotEmpty) GoRouter.of(ctx).go('/sermons/$id');
      case 'event':
        if (id.isNotEmpty) GoRouter.of(ctx).go('/events/$id');
    }
  }
}

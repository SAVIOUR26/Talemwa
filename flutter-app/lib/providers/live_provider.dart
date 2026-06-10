import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../models/live_status.dart';
import '../services/api_service.dart';
import '../core/constants.dart';

class LiveNotifier extends StateNotifier<LiveStatus> {
  Timer? _timer;

  LiveNotifier() : super(LiveStatus.offline()) {
    _loadFromCache();
    _fetch();
    _timer = Timer.periodic(const Duration(seconds: 60), (_) => _fetch());
  }

  void _loadFromCache() {
    final box    = Hive.box(AppConstants.liveBox);
    final cached = box.get('live') as Map?;
    if (cached != null) {
      try {
        state = LiveStatus.fromJson(Map<String, dynamic>.from(cached));
      } catch (_) {}
    }
  }

  Future<void> _fetch() async {
    try {
      final data = await ApiService.getLiveStatus();
      state = LiveStatus.fromJson(data);
      final box = Hive.box(AppConstants.liveBox);
      await box.put('live', data);
    } catch (_) {}
  }

  Future<void> refresh() => _fetch();

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }
}

final liveProvider =
    StateNotifierProvider<LiveNotifier, LiveStatus>((_) => LiveNotifier());

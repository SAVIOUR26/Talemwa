import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../models/radio_status.dart';
import '../services/api_service.dart';
import '../core/constants.dart';

class RadioNotifier extends StateNotifier<RadioStatus> {
  Timer? _timer;

  RadioNotifier() : super(RadioStatus.offline()) {
    _loadFromCache();
    _fetch();
    _timer = Timer.periodic(const Duration(seconds: 30), (_) => _fetch());
  }

  void _loadFromCache() {
    final box    = Hive.box(AppConstants.settingsBox);
    final cached = box.get('radio_status') as Map?;
    if (cached != null) {
      try {
        state = RadioStatus.fromJson(Map<String, dynamic>.from(cached));
      } catch (_) {}
    }
  }

  Future<void> _fetch() async {
    try {
      final data = await ApiService.getRadioStatus();
      state = RadioStatus.fromJson(data);
      final box = Hive.box(AppConstants.settingsBox);
      await box.put('radio_status', data);
    } catch (_) {}
  }

  Future<void> refresh() => _fetch();

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }
}

final radioProvider =
    StateNotifierProvider<RadioNotifier, RadioStatus>((_) => RadioNotifier());

final radioScheduleProvider = FutureProvider<List<RadioScheduleSlot>>((ref) async {
  final list = await ApiService.getRadioSchedule();
  return list.map((e) => RadioScheduleSlot.fromJson(e as Map<String, dynamic>)).toList();
});

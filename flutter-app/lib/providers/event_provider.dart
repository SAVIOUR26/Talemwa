import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../models/event.dart';
import '../services/api_service.dart';
import '../core/constants.dart';

final eventsProvider = FutureProvider<List<Event>>((ref) async {
  try {
    final list = await ApiService.getEvents();
    final events = list.map((e) => Event.fromJson(e as Map<String, dynamic>)).toList();
    final box = Hive.box(AppConstants.eventBox);
    await box.put('list', list);
    return events;
  } catch (_) {
    final box    = Hive.box(AppConstants.eventBox);
    final cached = box.get('list') as List?;
    if (cached != null) {
      return cached
          .cast<Map>()
          .map((e) => Event.fromJson(Map<String, dynamic>.from(e)))
          .toList();
    }
    rethrow;
  }
});

final eventDetailProvider = FutureProvider.family<Event, int>((ref, id) async {
  final data = await ApiService.getEvent(id);
  return Event.fromJson(data);
});

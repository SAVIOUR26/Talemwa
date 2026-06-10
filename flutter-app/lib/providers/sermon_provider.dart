import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../models/sermon.dart';
import '../services/api_service.dart';
import '../core/constants.dart';

class SermonListState {
  final List<Sermon> sermons;
  final bool         isLoading;
  final bool         hasMore;
  final int          page;
  final String?      search;
  final String?      series;
  final String?      error;

  const SermonListState({
    this.sermons  = const [],
    this.isLoading = false,
    this.hasMore  = true,
    this.page     = 1,
    this.search,
    this.series,
    this.error,
  });

  SermonListState copyWith({
    List<Sermon>? sermons,
    bool?         isLoading,
    bool?         hasMore,
    int?          page,
    String?       search,
    String?       series,
    String?       error,
  }) => SermonListState(
    sermons:   sermons   ?? this.sermons,
    isLoading: isLoading ?? this.isLoading,
    hasMore:   hasMore   ?? this.hasMore,
    page:      page      ?? this.page,
    search:    search    ?? this.search,
    series:    series    ?? this.series,
    error:     error,
  );
}

class SermonListNotifier extends StateNotifier<SermonListState> {
  SermonListNotifier() : super(const SermonListState()) {
    _loadFromCache();
    load();
  }

  void _loadFromCache() {
    final box = Hive.box(AppConstants.sermonBox);
    final cached = box.get('list') as List?;
    if (cached != null) {
      state = state.copyWith(
        sermons: cached.cast<Map>().map((e) => Sermon.fromJson(Map<String, dynamic>.from(e))).toList(),
      );
    }
  }

  Future<void> load({bool reset = false}) async {
    if (state.isLoading) return;
    if (!state.hasMore && !reset) return;

    final page = reset ? 1 : state.page;
    state = state.copyWith(isLoading: true, error: null);

    try {
      final data = await ApiService.getSermons(
        page:   page,
        search: state.search,
        series: state.series,
      );
      final items = (data['data'] as List? ?? [])
          .map((e) => Sermon.fromJson(e as Map<String, dynamic>))
          .toList();
      final meta    = data['meta'] as Map<String, dynamic>?;
      final hasMore = meta != null
          ? (meta['current_page'] as int) < (meta['last_page'] as int)
          : items.length >= 15;

      final updated = reset ? items : [...state.sermons, ...items];

      if (reset && state.search == null && state.series == null) {
        final box = Hive.box(AppConstants.sermonBox);
        await box.put('list', items.map((s) => s.toJson()).toList());
      }

      state = state.copyWith(
        sermons:   updated,
        isLoading: false,
        hasMore:   hasMore,
        page:      page + 1,
      );
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<void> refresh() => load(reset: true);

  void setSearch(String? q) {
    state = SermonListState(search: q, series: state.series);
    load(reset: true);
  }

  void setSeries(String? s) {
    state = SermonListState(search: state.search, series: s);
    load(reset: true);
  }
}

final sermonListProvider =
    StateNotifierProvider<SermonListNotifier, SermonListState>(
      (_) => SermonListNotifier(),
    );

final sermonSeriesProvider = FutureProvider<List<String>>((ref) async {
  final list = await ApiService.getSermonSeries();
  return list.map((e) => e['series'] as String? ?? '').where((s) => s.isNotEmpty).toList();
});

final sermonDetailProvider = FutureProvider.family<Sermon, int>((ref, id) async {
  final data = await ApiService.getSermon(id);
  return Sermon.fromJson(data);
});

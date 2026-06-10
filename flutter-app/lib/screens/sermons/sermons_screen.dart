import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../core/theme.dart';
import '../../providers/sermon_provider.dart';
import '../../models/sermon.dart';

class SermonsScreen extends ConsumerStatefulWidget {
  const SermonsScreen({super.key});
  @override ConsumerState<SermonsScreen> createState() => _SermonsScreenState();
}

class _SermonsScreenState extends ConsumerState<SermonsScreen> {
  final _searchCtrl   = TextEditingController();
  final _scrollCtrl   = ScrollController();
  String? _activeSeries;

  @override
  void initState() {
    super.initState();
    _scrollCtrl.addListener(() {
      if (_scrollCtrl.position.pixels >= _scrollCtrl.position.maxScrollExtent - 200) {
        ref.read(sermonListProvider.notifier).load();
      }
    });
  }

  @override
  void dispose() {
    _searchCtrl.dispose();
    _scrollCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final state   = ref.watch(sermonListProvider);
    final seriesA = ref.watch(sermonSeriesProvider);

    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: AppBar(
        backgroundColor: AppColors.navy,
        title: const Text('Sermons', style: TextStyle(color: Colors.white)),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(56),
          child: Padding(
            padding: const EdgeInsets.fromLTRB(12, 0, 12, 8),
            child: TextField(
              controller: _searchCtrl,
              style: const TextStyle(color: Colors.white),
              decoration: InputDecoration(
                hintText: 'Search sermons…',
                hintStyle: TextStyle(color: Colors.white.withOpacity(0.5)),
                prefixIcon: const Icon(Icons.search, color: Colors.white54),
                suffixIcon: _searchCtrl.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.close, color: Colors.white54),
                        onPressed: () {
                          _searchCtrl.clear();
                          ref.read(sermonListProvider.notifier).setSearch(null);
                        })
                    : null,
                filled: true,
                fillColor: Colors.white.withOpacity(0.1),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(8), borderSide: BorderSide.none),
                contentPadding: EdgeInsets.zero,
              ),
              onSubmitted: (q) => ref.read(sermonListProvider.notifier).setSearch(q),
            ),
          ),
        ),
      ),
      body: Column(
        children: [
          // Series filter chips
          seriesA.when(
            loading: () => const SizedBox(height: 48),
            error:   (_, __) => const SizedBox(),
            data:    (series) => SizedBox(
              height: 48,
              child: ListView(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                children: [
                  _Chip(label: 'All', active: _activeSeries == null,
                      onTap: () { setState(() => _activeSeries = null); ref.read(sermonListProvider.notifier).setSeries(null); }),
                  ...series.map((s) => _Chip(
                    label: s, active: _activeSeries == s,
                    onTap: () {
                      final next = _activeSeries == s ? null : s;
                      setState(() => _activeSeries = next);
                      ref.read(sermonListProvider.notifier).setSeries(next);
                    },
                  )),
                ],
              ),
            ),
          ),
          Expanded(
            child: state.error != null && state.sermons.isEmpty
                ? Center(child: Text(state.error!, style: const TextStyle(color: Colors.red)))
                : RefreshIndicator(
                    onRefresh: () => ref.read(sermonListProvider.notifier).refresh(),
                    child: ListView.builder(
                      controller: _scrollCtrl,
                      padding: const EdgeInsets.all(12),
                      itemCount: state.sermons.length + (state.isLoading ? 1 : 0),
                      itemBuilder: (ctx, i) {
                        if (i == state.sermons.length) {
                          return const Padding(
                            padding: EdgeInsets.all(16),
                            child: Center(child: CircularProgressIndicator()),
                          );
                        }
                        return _SermonTile(sermon: state.sermons[i]);
                      },
                    ),
                  ),
          ),
        ],
      ),
    );
  }
}

class _Chip extends StatelessWidget {
  final String  label;
  final bool    active;
  final VoidCallback onTap;
  const _Chip({required this.label, required this.active, required this.onTap});

  @override
  Widget build(BuildContext context) => GestureDetector(
    onTap: onTap,
    child: Container(
      margin: const EdgeInsets.only(right: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
      decoration: BoxDecoration(
        color:        active ? AppColors.gold : Colors.white,
        borderRadius: BorderRadius.circular(20),
        border:       Border.all(color: active ? AppColors.gold : Colors.grey.shade300),
      ),
      child: Text(label,
          style: TextStyle(
            color:       active ? Colors.white : AppColors.navy,
            fontSize:    13,
            fontWeight:  active ? FontWeight.bold : FontWeight.normal,
          )),
    ),
  );
}

class _SermonTile extends StatelessWidget {
  final Sermon sermon;
  const _SermonTile({required this.sermon});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => context.go('/sermons/${sermon.id}'),
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [BoxShadow(color: Colors.black08, blurRadius: 4)],
        ),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.horizontal(left: Radius.circular(12)),
              child: sermon.thumbnailUrl != null
                  ? Image.network(sermon.thumbnailUrl!, width: 88, height: 88, fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => _thumb())
                  : _thumb(),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (sermon.series != null)
                      Text(sermon.series!,
                          style: const TextStyle(color: AppColors.gold, fontSize: 11, fontWeight: FontWeight.w600)),
                    Text(sermon.title,
                        style: const TextStyle(color: AppColors.navy, fontSize: 14, fontWeight: FontWeight.bold),
                        maxLines: 2, overflow: TextOverflow.ellipsis),
                    const SizedBox(height: 4),
                    Row(children: [
                      Icon(Icons.play_circle_outline, size: 13, color: Colors.grey[500]),
                      const SizedBox(width: 3),
                      Text('${sermon.playCount}', style: TextStyle(color: Colors.grey[500], fontSize: 11)),
                      if (sermon.formattedDuration.isNotEmpty) ...[
                        const SizedBox(width: 8),
                        Icon(Icons.timer_outlined, size: 13, color: Colors.grey[500]),
                        const SizedBox(width: 3),
                        Text(sermon.formattedDuration, style: TextStyle(color: Colors.grey[500], fontSize: 11)),
                      ],
                    ]),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _thumb() => Container(
    width: 88, height: 88, color: AppColors.navyLight,
    child: const Icon(Icons.mic, color: AppColors.gold, size: 32),
  );
}

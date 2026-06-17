<?php
$pageTitle   = 'Sermon Archive';
$metaDesc    = 'Browse and listen to sermons by Pastor Robert Talemwa. Search by title, series, or scripture.';
$currentPage = 'sermons';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/nav.php';
?>

<section class="bg-navy py-12">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 text-center">
    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Sermon Archive</h1>
    <p class="text-gray-400">Browse hundreds of messages — search by title, scripture, or series</p>
  </div>
</section>

<main class="max-w-6xl mx-auto px-4 sm:px-6 py-10" x-data="sermonArchive()" x-init="init()">

  <!-- Search + filters -->
  <div class="flex flex-col sm:flex-row gap-3 mb-6">
    <div class="relative flex-1">
      <i class="ti ti-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
      <input x-model="search" @input.debounce.400ms="load()" type="text"
             placeholder="Search sermons, scriptures, speakers…"
             class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
    </div>
    <select x-model="filterSeries" @change="load()"
            class="border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold">
      <option value="">All Series</option>
      <template x-for="s in seriesList" :key="s.series">
        <option :value="s.series" x-text="`${s.series} (${s.count})`"></option>
      </template>
    </select>
  </div>

  <!-- Series chips -->
  <div x-show="seriesList.length" class="flex gap-2 flex-wrap mb-8">
    <button @click="filterSeries=''; load()"
            :class="!filterSeries ? 'bg-navy text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
            class="px-3 py-1.5 rounded-full text-xs font-medium transition">All</button>
    <template x-for="s in seriesList" :key="s.series">
      <button @click="filterSeries = s.series; load()"
              :class="filterSeries === s.series ? 'bg-navy text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
              class="px-3 py-1.5 rounded-full text-xs font-medium transition"
              x-text="s.series"></button>
    </template>
  </div>

  <!-- Loading -->
  <div x-show="loading" class="flex justify-center py-16">
    <i class="ti ti-loader-2 animate-spin text-3xl text-gold"></i>
  </div>

  <!-- Grid -->
  <div x-show="!loading" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <template x-if="sermons.length === 0">
      <div class="col-span-3 text-center py-16 text-gray-400">
        <i class="ti ti-search text-4xl block mb-3"></i>
        No sermons found. Try a different search.
      </div>
    </template>
    <template x-for="s in sermons" :key="s.id">
      <a :href="`/sermons/${s.id}`"
         class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group">
        <div class="relative">
          <img :src="s.thumbnail_url || 'https://placehold.co/400x220/0A1628/C9A84C?text=📖'"
               class="w-full h-44 object-cover group-hover:scale-105 transition duration-300" alt="">
          <div x-show="s.duration_seconds"
               class="absolute bottom-2 right-2 bg-black/70 text-white text-xs px-2 py-0.5 rounded font-mono"
               x-text="formatDuration(s.duration_seconds)"></div>
        </div>
        <div class="p-5">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gold" x-text="s.series || 'Sermon'"></span>
            <span class="text-xs text-gray-400 flex items-center gap-1">
              <i class="ti ti-player-play"></i>
              <span x-text="(s.play_count || 0).toLocaleString()"></span>
            </span>
          </div>
          <h3 class="font-bold text-gray-900 line-clamp-2 mb-2" x-text="s.title"></h3>
          <p x-show="s.scripture" class="text-xs text-gray-500 flex items-center gap-1 mb-2">
            <i class="ti ti-book"></i> <span x-text="s.scripture"></span>
          </p>
          <p class="text-xs text-gray-400" x-text="s.speaker"></p>
        </div>
      </a>
    </template>
  </div>

  <!-- Pagination -->
  <div class="flex items-center justify-center gap-3" x-show="totalPages > 1">
    <button @click="page--; load()" :disabled="page <= 1"
            class="px-4 py-2 border border-gray-200 rounded-xl text-sm hover:bg-gray-50 disabled:opacity-40 transition">
      ← Previous
    </button>
    <span class="text-sm text-gray-500" x-text="`Page ${page} of ${totalPages}`"></span>
    <button @click="page++; load()" :disabled="page >= totalPages"
            class="px-4 py-2 border border-gray-200 rounded-xl text-sm hover:bg-gray-50 disabled:opacity-40 transition">
      Next →
    </button>
  </div>

</main>

<?php include __DIR__ . '/../partials/radio-bar.php'; ?>
<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
function sermonArchive() {
  return {
    sermons:      [],
    seriesList:   [],
    search:       '',
    filterSeries: '',
    loading:      true,
    page:         1,
    totalPages:   1,

    async init() {
      await Promise.all([this.load(), this.loadSeries()]);
    },

    async load() {
      this.loading = true;
      try {
        const p = new URLSearchParams({ page: this.page });
        if (this.search)       p.set('search', this.search);
        if (this.filterSeries) p.set('series', this.filterSeries);
        const data      = await apiFetch(`/api/sermons?${p}`);
        this.sermons    = data.items;
        this.totalPages = data.total_pages;
      } catch {} finally { this.loading = false; }
    },

    async loadSeries() {
      try { this.seriesList = await apiFetch('/api/sermons/series'); } catch {}
    }
  }
}
</script>

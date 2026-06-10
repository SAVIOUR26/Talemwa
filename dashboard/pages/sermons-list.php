<?php $pageTitle = 'Sermons'; $activePage = 'sermons-list'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">

  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">All Sermons</h1>
      <p class="text-sm text-gray-500 mt-0.5" id="sermon-count">Loading…</p>
    </div>
    <a href="/pages/sermons-upload.php"
       class="inline-flex items-center gap-2 bg-navy text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-navy-700 transition">
      <i class="ti ti-upload"></i> Upload Sermon
    </a>
  </header>

  <main class="flex-1 p-8" x-data="sermonsList()" x-init="init()">

    <!-- Filters -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 flex flex-wrap gap-3 items-end">
      <div class="flex-1 min-w-48">
        <label class="block text-xs text-gray-500 mb-1">Search</label>
        <input x-model="search" @input.debounce.400ms="load()" type="text" placeholder="Title, speaker, scripture…"
               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold">
      </div>
      <div class="min-w-36">
        <label class="block text-xs text-gray-500 mb-1">Series</label>
        <select x-model="filterSeries" @change="load()"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold">
          <option value="">All Series</option>
          <template x-for="s in seriesList" :key="s.series">
            <option :value="s.series" x-text="s.series"></option>
          </template>
        </select>
      </div>
      <button @click="search=''; filterSeries=''; load()"
              class="text-sm text-gray-400 hover:text-gray-700 transition px-3 py-2">
        Clear filters
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left px-6 py-3.5 font-medium text-gray-500 w-[40%]">Sermon</th>
              <th class="text-left px-4 py-3.5 font-medium text-gray-500">Series</th>
              <th class="text-left px-4 py-3.5 font-medium text-gray-500">Date</th>
              <th class="text-right px-4 py-3.5 font-medium text-gray-500">Plays</th>
              <th class="text-center px-4 py-3.5 font-medium text-gray-500">Status</th>
              <th class="text-right px-6 py-3.5 font-medium text-gray-500">Actions</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="loading">
              <tr><td colspan="6" class="text-center py-12 text-gray-400">
                <i class="ti ti-loader-2 animate-spin text-2xl"></i>
              </td></tr>
            </template>
            <template x-if="!loading && sermons.length === 0">
              <tr><td colspan="6" class="text-center py-12 text-gray-400">
                No sermons found.
                <a href="/pages/sermons-upload.php" class="text-gold hover:underline ml-1">Upload one</a>
              </td></tr>
            </template>
            <template x-for="s in sermons" :key="s.id">
              <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <img :src="s.thumbnail_url || 'https://placehold.co/48x48/0A1628/C9A84C?text=📖'"
                         class="w-12 h-9 rounded object-cover flex-shrink-0">
                    <div class="min-w-0">
                      <p class="font-medium text-gray-900 truncate" x-text="s.title"></p>
                      <p class="text-xs text-gray-400 truncate" x-text="s.speaker"></p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 text-gray-600 text-xs" x-text="s.series || '—'"></td>
                <td class="px-4 py-4 text-gray-500 text-xs" x-text="formatDate(s.created_at)"></td>
                <td class="px-4 py-4 text-right font-medium text-gray-900" x-text="(s.play_count || 0).toLocaleString()"></td>
                <td class="px-4 py-4 text-center">
                  <button @click="togglePublished(s)"
                          :class="s.published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                          class="text-xs px-2.5 py-1 rounded-full font-medium transition hover:opacity-80">
                    <span x-text="s.published ? 'Published' : 'Draft'"></span>
                  </button>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <a :href="`/pages/sermons-upload.php?id=${s.id}`"
                       class="p-1.5 text-gray-400 hover:text-navy hover:bg-gray-100 rounded-lg transition" title="Edit">
                      <i class="ti ti-edit"></i>
                    </a>
                    <button @click="deleteSermon(s)"
                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete">
                      <i class="ti ti-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100" x-show="totalPages > 1">
        <p class="text-sm text-gray-500" x-text="`Page ${page} of ${totalPages} · ${total} sermons`"></p>
        <div class="flex gap-2">
          <button @click="page--; load()" :disabled="page <= 1"
                  class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 transition">
            Previous
          </button>
          <button @click="page++; load()" :disabled="page >= totalPages"
                  class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg hover:bg-gray-50 disabled:opacity-40 transition">
            Next
          </button>
        </div>
      </div>
    </div>

  </main>
</div>

<script>
function sermonsList() {
  return {
    sermons:      [],
    seriesList:   [],
    search:       '',
    filterSeries: '',
    loading:      true,
    page:         1,
    total:        0,
    totalPages:   1,

    async init() {
      authGuard();
      await Promise.all([this.load(), this.loadSeries()]);
    },

    async load() {
      this.loading = true;
      try {
        const params = new URLSearchParams({ page: this.page });
        if (this.search)       params.set('search', this.search);
        if (this.filterSeries) params.set('series', this.filterSeries);

        const data       = await apiGet(`/api/sermons?${params}`);
        this.sermons     = data.items;
        this.total       = data.total;
        this.totalPages  = data.total_pages;
        document.getElementById('sermon-count').textContent = `${data.total} sermon${data.total !== 1 ? 's' : ''}`;
      } catch {} finally {
        this.loading = false;
      }
    },

    async loadSeries() {
      try {
        this.seriesList = await apiGet('/api/sermons/series');
      } catch {}
    },

    async togglePublished(s) {
      try {
        await apiPut(`/api/admin/sermons/${s.id}`, { ...s, published: s.published ? 0 : 1 });
        s.published = s.published ? 0 : 1;
        showToast(s.published ? 'Sermon published' : 'Sermon set to draft');
      } catch (e) {
        showToast(e.message, 'error');
      }
    },

    async deleteSermon(s) {
      if (!confirmAction(`Delete "${s.title}"? This will hide it from the app.`)) return;
      try {
        await apiDelete(`/api/admin/sermons/${s.id}`);
        this.sermons = this.sermons.filter(x => x.id !== s.id);
        showToast('Sermon removed');
      } catch (e) {
        showToast(e.message, 'error');
      }
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>

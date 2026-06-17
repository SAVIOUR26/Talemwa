<?php $pageTitle = 'Series Manager'; $activePage = 'sermons-series'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
    <h1 class="text-xl font-semibold text-gray-900">Series Manager</h1>
    <p class="text-sm text-gray-500 mt-0.5">Organise sermons into teaching series</p>
  </header>

  <main class="flex-1 p-8" x-data="seriesManager()" x-init="init()">

    <div class="max-w-2xl mx-auto space-y-4">

      <!-- Rename form -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-4">All Series</h2>

        <template x-if="loading">
          <p class="text-gray-400 text-sm text-center py-4"><i class="ti ti-loader-2 animate-spin"></i></p>
        </template>

        <div class="space-y-2">
          <template x-for="s in series" :key="s.series">
            <div class="flex items-center gap-3 py-2.5 border-b border-gray-50">
              <div class="flex-1 min-w-0">
                <template x-if="editing !== s.series">
                  <div class="flex items-center gap-2">
                    <p class="font-medium text-gray-900" x-text="s.series"></p>
                    <span class="text-xs text-gray-400" x-text="`${s.count} sermon${s.count !== 1 ? 's' : ''}`"></span>
                  </div>
                </template>
                <template x-if="editing === s.series">
                  <input :id="`rename-${s.series}`" type="text" :value="s.series" @keydown.enter="rename(s, $event.target.value)"
                         class="border border-gold rounded-lg px-3 py-1.5 text-sm focus:outline-none w-full">
                </template>
              </div>
              <div class="flex gap-1">
                <button x-show="editing !== s.series" @click="editing = s.series"
                        class="p-1.5 text-gray-400 hover:text-navy hover:bg-gray-100 rounded-lg transition text-xs" title="Rename">
                  <i class="ti ti-edit"></i>
                </button>
                <button x-show="editing === s.series" @click="rename(s, document.getElementById(`rename-${s.series}`).value)"
                        class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition text-xs">
                  <i class="ti ti-check"></i>
                </button>
                <button x-show="editing === s.series" @click="editing = null"
                        class="p-1.5 text-gray-400 hover:bg-gray-100 rounded-lg transition text-xs">
                  <i class="ti ti-x"></i>
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>

      <div class="bg-navy/5 border border-navy/10 rounded-2xl p-5 text-sm text-gray-600">
        <i class="ti ti-info-circle text-navy mr-1"></i>
        Series are automatically created when you assign a series name to a sermon during upload.
        Renaming a series here updates all sermons in that series.
      </div>

    </div>
  </main>
</div>

<script>
function seriesManager() {
  return {
    series:  [],
    loading: true,
    editing: null,

    async init() {
      authGuard();
      try { this.series = await apiGet('/api/sermons/series'); }
      catch {} finally { this.loading = false; }
    },

    async rename(s, newName) {
      newName = newName.trim();
      if (!newName || newName === s.series) { this.editing = null; return; }

      // Fetch all sermons in this series and update each
      try {
        const data = await apiGet(`/api/sermons?series=${encodeURIComponent(s.series)}&page=1`);
        const all  = data.items || [];
        await Promise.all(all.map(sermon =>
          apiPut(`/api/admin/sermons/${sermon.id}`, { ...sermon, series: newName })
        ));
        s.series     = newName;
        this.editing = null;
        showToast('Series renamed');
      } catch(e) {
        showToast(e.message, 'error');
      }
    }
  }
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

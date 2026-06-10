<?php $pageTitle = 'Listener Stats'; $activePage = 'radio-stats'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
    <h1 class="text-xl font-semibold text-gray-900">Radio · Listener Stats</h1>
    <p class="text-sm text-gray-500 mt-0.5">Live data from AzuraCast</p>
  </header>

  <main class="flex-1 p-8" x-data="radioStats()" x-init="init()">

    <!-- Live stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Current Listeners</p>
        <p class="text-3xl font-bold text-gray-900 mt-1" x-text="live.listeners ?? '—'"></p>
        <p class="text-xs text-gray-400 mt-1">Updates every 30 seconds</p>
      </div>
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Stream Status</p>
        <div class="flex items-center gap-2 mt-1">
          <span :class="live.is_online ? 'bg-green-500' : 'bg-gray-400'" class="w-2.5 h-2.5 rounded-full"></span>
          <span class="text-lg font-bold" :class="live.is_online ? 'text-green-600' : 'text-gray-400'"
                x-text="live.is_online ? 'Online' : 'Offline'"></span>
        </div>
      </div>
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Now Playing</p>
        <p class="font-semibold text-gray-900 mt-1 truncate" x-text="live.now_playing?.title || '—'"></p>
        <p class="text-xs text-gray-400 truncate" x-text="live.now_playing?.artist || ''"></p>
      </div>
    </div>

    <!-- AzuraCast iframe note -->
    <div class="bg-navy/5 border border-navy/10 rounded-2xl p-6">
      <div class="flex items-start gap-4">
        <i class="ti ti-info-circle text-navy text-xl flex-shrink-0 mt-0.5"></i>
        <div>
          <h3 class="font-semibold text-navy mb-1">Full Analytics Available in AzuraCast</h3>
          <p class="text-sm text-gray-600 mb-3">
            Detailed listener history, geographic data, peak concurrent listeners, and listener-hours
            are available directly in your AzuraCast dashboard.
          </p>
          <a href="https://radio.roberttalemwa.online" target="_blank"
             class="inline-flex items-center gap-2 bg-navy text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-navy-700 transition">
            <i class="ti ti-external-link"></i> Open AzuraCast Dashboard
          </a>
        </div>
      </div>
    </div>

  </main>
</div>

<script>
function radioStats() {
  return {
    live: {},
    async init() {
      authGuard();
      await this.refresh();
      setInterval(() => this.refresh(), 30000);
    },
    async refresh() {
      try { this.live = await apiGet('/api/radio'); } catch {}
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>

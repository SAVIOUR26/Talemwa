<?php $pageTitle = 'Dashboard Overview'; $activePage = 'overview'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">

  <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Dashboard</h1>
      <p class="text-sm text-gray-500 mt-0.5">Welcome back, <span id="welcome-name" class="font-medium text-navy">Admin</span></p>
    </div>
  </header>

  <main class="flex-1 p-8" x-data="overview()" x-init="init()">

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

      <!-- Sermons -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm text-gray-500">Total Sermons</p>
            <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.sermons ?? '—'"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-navy/5 flex items-center justify-center">
            <i class="ti ti-microphone text-navy text-xl"></i>
          </div>
        </div>
        <a href="/pages/sermons-list.php" class="text-xs text-gold mt-3 inline-flex items-center gap-1 hover:underline">
          View all <i class="ti ti-arrow-right text-xs"></i>
        </a>
      </div>

      <!-- Radio -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm text-gray-500">Radio Status</p>
            <div class="flex items-center gap-2 mt-1">
              <span :class="stats.live?.is_live ? 'bg-red-500 animate-pulse' : 'bg-gray-300'" class="w-2.5 h-2.5 rounded-full flex-shrink-0"></span>
              <span class="text-lg font-bold text-gray-900" x-text="stats.live?.is_live ? 'LIVE' : 'Off Air'"></span>
            </div>
            <p class="text-xs text-gray-400 mt-0.5 truncate" x-text="stats.live?.is_live ? stats.live.title : 'Not broadcasting'"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center">
            <i class="ti ti-broadcast text-red-500 text-xl"></i>
          </div>
        </div>
        <a href="/pages/radio-live.php" class="text-xs text-gold mt-3 inline-flex items-center gap-1 hover:underline">
          Live Control <i class="ti ti-arrow-right text-xs"></i>
        </a>
      </div>

      <!-- App Installs -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm text-gray-500">App Installs</p>
            <p class="text-3xl font-bold text-gray-900 mt-1" x-text="stats.installs?.total ?? '—'"></p>
            <p class="text-xs text-green-600 mt-0.5" x-text="stats.installs?.this_week ? `+${stats.installs.this_week} this week` : ''"></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center">
            <i class="ti ti-device-mobile text-blue-500 text-xl"></i>
          </div>
        </div>
        <a href="/pages/app-installs.php" class="text-xs text-gold mt-3 inline-flex items-center gap-1 hover:underline">
          View stats <i class="ti ti-arrow-right text-xs"></i>
        </a>
      </div>

      <!-- Giving this month -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-sm text-gray-500">Giving This Month</p>
            <template x-if="stats.giving_this_month?.length">
              <div class="mt-1 space-y-0.5">
                <template x-for="g in stats.giving_this_month" :key="g.currency">
                  <p class="text-lg font-bold text-gray-900"
                     x-text="formatCurrency(g.total, g.currency)"></p>
                </template>
              </div>
            </template>
            <template x-if="!stats.giving_this_month?.length">
              <p class="text-lg font-bold text-gray-400 mt-1">No gifts yet</p>
            </template>
          </div>
          <div class="w-12 h-12 rounded-xl bg-gold/10 flex items-center justify-center">
            <i class="ti ti-coin text-gold text-xl"></i>
          </div>
        </div>
        <a href="/pages/giving-records.php" class="text-xs text-gold mt-3 inline-flex items-center gap-1 hover:underline">
          View records <i class="ti ti-arrow-right text-xs"></i>
        </a>
      </div>

    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

      <!-- App installs 30-day chart -->
      <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="font-semibold text-gray-900">App Installs — Last 30 Days</h2>
          <span class="text-xs text-gray-400">Android vs iOS</span>
        </div>
        <canvas id="installsChart" height="110"></canvas>
      </div>

      <!-- Prayer requests summary -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-gray-900">Prayer Requests</h2>
          <a href="/pages/prayers.php" class="text-xs text-gold hover:underline">View all</a>
        </div>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
            <div class="flex items-center gap-2">
              <i class="ti ti-alert-circle text-red-500"></i>
              <span class="text-sm font-medium text-gray-700">Urgent</span>
            </div>
            <span class="font-bold text-red-500 text-lg" x-text="stats.prayers?.urgent ?? 0"></span>
          </div>
          <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl">
            <div class="flex items-center gap-2">
              <i class="ti ti-mail text-amber-500"></i>
              <span class="text-sm font-medium text-gray-700">Unread</span>
            </div>
            <span class="font-bold text-amber-500 text-lg" x-text="stats.prayers?.unread ?? 0"></span>
          </div>
        </div>
        <a href="/pages/prayers.php" class="mt-4 w-full flex items-center justify-center gap-2 py-2.5 bg-navy/5 hover:bg-navy/10 text-navy text-sm font-medium rounded-xl transition">
          <i class="ti ti-pray"></i> Open Prayer Requests
        </a>
      </div>

    </div>

    <!-- Bottom charts row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

      <!-- Giving 6-month bar -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="font-semibold text-gray-900">Giving Trend — 6 Months</h2>
          <a href="/pages/giving-reports.php" class="text-xs text-gold hover:underline">Full report</a>
        </div>
        <canvas id="givingChart" height="140"></canvas>
      </div>

      <!-- Top sermons -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-gray-900">Top Sermons by Plays</h2>
          <a href="/pages/sermons-list.php" class="text-xs text-gold hover:underline">All sermons</a>
        </div>
        <canvas id="sermonsChart" height="140"></canvas>
      </div>

    </div>

  </main>
</div>

<script>
function overview() {
  return {
    stats: {},

    async init() {
      authGuard();
      document.getElementById('welcome-name').textContent = localStorage.getItem('talemwa_name') || 'Admin';
      await this.loadStats();
      await this.loadCharts();
    },

    async loadStats() {
      try {
        this.stats = await apiGet('/api/admin/stats');
      } catch {}
    },

    async loadCharts() {
      try {
        const [installData, givingData, sermonData] = await Promise.all([
          apiGet('/api/admin/stats/installs'),
          apiGet('/api/admin/stats/giving'),
          apiGet('/api/admin/stats/sermons'),
        ]);

        this.buildInstallsChart(installData);
        this.buildGivingChart(givingData);
        this.buildSermonsChart(sermonData);
      } catch {}
    },

    buildInstallsChart(data) {
      const days = [...new Set(data.daily.map(d => d.date))].sort();
      const android = days.map(d => {
        const row = data.daily.find(r => r.date === d && r.platform === 'android');
        return row ? row.count : 0;
      });
      const ios = days.map(d => {
        const row = data.daily.find(r => r.date === d && r.platform === 'ios');
        return row ? row.count : 0;
      });

      new Chart(document.getElementById('installsChart'), {
        type: 'line',
        data: {
          labels: days.map(d => new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })),
          datasets: [
            { label: 'Android', data: android, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', tension: 0.4, fill: true },
            { label: 'iOS',     data: ios,     borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', tension: 0.4, fill: true }
          ]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
      });
    },

    buildGivingChart(data) {
      const months   = [...new Set(data.monthly.map(r => r.month))].sort().slice(-6);
      const usd      = months.map(m => { const r = data.monthly.find(x => x.month === m && x.currency === 'USD'); return r ? r.total : 0; });
      const ugx      = months.map(m => { const r = data.monthly.find(x => x.month === m && x.currency === 'UGX'); return r ? r.total / 3700 : 0; });

      new Chart(document.getElementById('givingChart'), {
        type: 'bar',
        data: {
          labels: months.map(m => { const [y, mo] = m.split('-'); return new Date(y, mo-1).toLocaleDateString('en-GB', { month: 'short', year: '2-digit' }); }),
          datasets: [
            { label: 'USD', data: usd, backgroundColor: '#C9A84C' },
            { label: 'UGX (÷3700)', data: ugx, backgroundColor: '#0A1628' }
          ]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
      });
    },

    buildSermonsChart(data) {
      const top = data.top_by_plays.slice(0, 8);
      new Chart(document.getElementById('sermonsChart'), {
        type: 'bar',
        data: {
          labels: top.map(s => s.title.length > 25 ? s.title.slice(0,25) + '…' : s.title),
          datasets: [{ label: 'Plays', data: top.map(s => s.play_count), backgroundColor: '#0A1628' }]
        },
        options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
      });
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>

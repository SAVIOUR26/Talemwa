<?php $pageTitle = 'App Installs'; $activePage = 'app-installs'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
    <h1 class="text-xl font-semibold text-gray-900">App Installs & Users</h1>
    <p class="text-sm text-gray-500 mt-0.5">Track app adoption across platforms and countries</p>
  </header>

  <main class="flex-1 p-8" x-data="installStats()" x-init="init()">

    <!-- Platform totals -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Total Installs</p>
        <p class="text-3xl font-bold text-gray-900 mt-1" x-text="totals.total?.toLocaleString() || '—'"></p>
      </div>
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">Android</p>
        <p class="text-3xl font-bold text-green-600 mt-1" x-text="totals.android?.toLocaleString() || '0'"></p>
      </div>
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <p class="text-sm text-gray-500">iOS</p>
        <p class="text-3xl font-bold text-blue-600 mt-1" x-text="totals.ios?.toLocaleString() || '0'"></p>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
      <div class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-6">Daily Installs — Last 30 Days</h2>
        <canvas id="dailyChart" height="120"></canvas>
      </div>
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-4">Platform Split</h2>
        <canvas id="platformChart" height="200"></canvas>
      </div>
    </div>

    <!-- Country table -->
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Top Countries <span class="text-sm font-normal text-gray-400 ml-1">(diaspora reach)</span></h2>
      </div>
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 bg-gray-50">
            <th class="text-left px-6 py-3 font-medium text-gray-500">#</th>
            <th class="text-left px-4 py-3 font-medium text-gray-500">Country</th>
            <th class="text-right px-6 py-3 font-medium text-gray-500">Installs</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="(row, idx) in byCountry" :key="row.country">
            <tr class="border-b border-gray-50 hover:bg-gray-50">
              <td class="px-6 py-3 text-gray-400 text-xs" x-text="idx+1"></td>
              <td class="px-4 py-3 font-medium text-gray-700" x-text="row.country || 'Unknown'"></td>
              <td class="px-6 py-3 text-right font-medium text-gray-900" x-text="parseInt(row.count).toLocaleString()"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

  </main>
</div>

<script>
function installStats() {
  return {
    totals:    { total:0, android:0, ios:0 },
    byCountry: [],

    async init() {
      authGuard();
      try {
        const data = await apiGet('/api/admin/stats/installs');
        // Platform totals
        data.by_platform.forEach(p => {
          this.totals[p.platform] = parseInt(p.count);
          this.totals.total += parseInt(p.count);
        });
        this.byCountry = data.by_country;
        this.buildDailyChart(data.daily);
        this.buildPlatformChart(data.by_platform);
      } catch {}
    },

    buildDailyChart(daily) {
      const days    = [...new Set(daily.map(d=>d.date))].sort();
      const android = days.map(d => { const r=daily.find(x=>x.date===d&&x.platform==='android'); return r?r.count:0; });
      const ios     = days.map(d => { const r=daily.find(x=>x.date===d&&x.platform==='ios'); return r?r.count:0; });
      new Chart(document.getElementById('dailyChart'), {
        type:'line',
        data:{ labels: days.map(d=>new Date(d).toLocaleDateString('en-GB',{day:'numeric',month:'short'})),
               datasets:[
                 {label:'Android',data:android,borderColor:'#22c55e',backgroundColor:'rgba(34,197,94,0.1)',tension:0.4,fill:true},
                 {label:'iOS',data:ios,borderColor:'#3b82f6',backgroundColor:'rgba(59,130,246,0.1)',tension:0.4,fill:true}
               ]},
        options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true,ticks:{stepSize:1}}}}
      });
    },

    buildPlatformChart(byPlatform) {
      new Chart(document.getElementById('platformChart'), {
        type:'doughnut',
        data:{ labels: byPlatform.map(p=>p.platform.charAt(0).toUpperCase()+p.platform.slice(1)),
               datasets:[{data:byPlatform.map(p=>p.count),backgroundColor:['#22c55e','#3b82f6','#f59e0b']}] },
        options:{responsive:true,plugins:{legend:{position:'bottom'}}}
      });
    }
  }
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

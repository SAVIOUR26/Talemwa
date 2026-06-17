<?php $pageTitle = 'Giving Reports'; $activePage = 'giving-reports'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Giving Reports</h1>
      <p class="text-sm text-gray-500 mt-0.5">Financial summary and trends</p>
    </div>
    <button onclick="exportSummaryCSV()"
            class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
      <i class="ti ti-download"></i> Export CSV
    </button>
  </header>

  <main class="flex-1 p-8" x-data="givingReports()" x-init="init()">

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-6">Giving by Month</h2>
        <canvas id="monthlyChart" height="180"></canvas>
      </div>
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-6">By Giving Type</h2>
        <canvas id="typeChart" height="180"></canvas>
      </div>
    </div>

    <!-- Monthly summary table -->
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-900">Monthly Summary</h2>
      </div>
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 bg-gray-50">
            <th class="text-left px-6 py-3 font-medium text-gray-500">Month</th>
            <th class="text-right px-4 py-3 font-medium text-gray-500">USD</th>
            <th class="text-right px-4 py-3 font-medium text-gray-500">UGX</th>
            <th class="text-right px-4 py-3 font-medium text-gray-500">GBP</th>
            <th class="text-right px-6 py-3 font-medium text-gray-500">Transactions</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="row in monthlyRows" :key="row.month">
            <tr class="border-b border-gray-50 hover:bg-gray-50">
              <td class="px-6 py-3 font-medium text-gray-700" x-text="row.label"></td>
              <td class="px-4 py-3 text-right text-gray-700" x-text="row.USD ? formatCurrency(row.USD, 'USD') : '—'"></td>
              <td class="px-4 py-3 text-right text-gray-700" x-text="row.UGX ? formatCurrency(row.UGX, 'UGX') : '—'"></td>
              <td class="px-4 py-3 text-right text-gray-700" x-text="row.GBP ? formatCurrency(row.GBP, 'GBP') : '—'"></td>
              <td class="px-6 py-3 text-right text-gray-500" x-text="row.count"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

  </main>
</div>

<script>
function givingReports() {
  return {
    monthlyRows: [],

    async init() {
      authGuard();
      try {
        const data = await apiGet('/api/admin/stats/giving');
        this.buildMonthlyTable(data.monthly);
        this.buildMonthlyChart(data.monthly);
        this.buildTypeChart(data.by_type);
        window._reportData = data;
      } catch {}
    },

    buildMonthlyTable(monthly) {
      const months = [...new Set(monthly.map(r => r.month))].sort().reverse();
      this.monthlyRows = months.map(m => {
        const rows  = monthly.filter(r => r.month === m);
        const count = rows.reduce((s,r) => s + parseInt(r.count), 0);
        const row   = { month: m, label: '', USD:0, UGX:0, GBP:0, EUR:0, count };
        const [y, mo] = m.split('-');
        row.label = new Date(y, mo-1).toLocaleDateString('en-GB', { month: 'long', year: 'numeric' });
        rows.forEach(r => { row[r.currency] = parseFloat(r.total); });
        return row;
      });
    },

    buildMonthlyChart(monthly) {
      const months = [...new Set(monthly.map(r => r.month))].sort().slice(-6);
      const usd    = months.map(m => { const r = monthly.find(x => x.month===m && x.currency==='USD'); return r ? r.total : 0; });
      const ugx    = months.map(m => { const r = monthly.find(x => x.month===m && x.currency==='UGX'); return r ? r.total/3700 : 0; });
      const labels = months.map(m => { const [y,mo]=m.split('-'); return new Date(y,mo-1).toLocaleDateString('en-GB',{month:'short',year:'2-digit'}); });
      new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: { labels, datasets: [
          { label:'USD', data:usd, backgroundColor:'#C9A84C' },
          { label:'UGX ÷3700', data:ugx, backgroundColor:'#0A1628' }
        ]},
        options: { responsive:true, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true}} }
      });
    },

    buildTypeChart(byType) {
      const types   = [...new Set(byType.map(r => r.giving_type))];
      const totals  = types.map(t => byType.filter(r=>r.giving_type===t).reduce((s,r)=>s+parseFloat(r.total),0));
      const colors  = ['#C9A84C','#0A1628','#22c55e','#3b82f6'];
      new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: { labels: types.map(t => t.charAt(0).toUpperCase()+t.slice(1)), datasets: [{ data:totals, backgroundColor:colors }] },
        options: { responsive:true, plugins:{legend:{position:'right'}} }
      });
    }
  }
}

function exportSummaryCSV() {
  const rows = window._reportData?.monthly || [];
  if (!rows.length) return;
  const csv = ['Month,Currency,Total,Count',
    ...rows.map(r => `${r.month},${r.currency},${r.total},${r.count}`)
  ].join('\n');
  const a = document.createElement('a');
  a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
  a.download = `giving-report-${new Date().toISOString().split('T')[0]}.csv`;
  a.click();
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

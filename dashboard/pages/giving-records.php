<?php $pageTitle = 'Giving Records'; $activePage = 'giving-records'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">

  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Giving Records</h1>
      <p class="text-sm text-gray-500 mt-0.5" id="giving-count">Loading…</p>
    </div>
    <button onclick="exportCSV()" class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
      <i class="ti ti-download"></i> Export CSV
    </button>
  </header>

  <main class="flex-1 p-8" x-data="givingRecords()" x-init="init()">

    <!-- Filters -->
    <div class="bg-white border border-gray-200 rounded-2xl p-4 mb-6 flex flex-wrap gap-3 items-end">
      <div>
        <label class="block text-xs text-gray-500 mb-1">Currency</label>
        <select x-model="filters.currency" @change="load()"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold">
          <option value="">All Currencies</option>
          <option>USD</option><option>UGX</option><option>GBP</option><option>EUR</option>
        </select>
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">Type</label>
        <select x-model="filters.giving_type" @change="load()"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold">
          <option value="">All Types</option>
          <option>tithe</option><option>offering</option><option>project</option><option>campaign</option>
        </select>
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select x-model="filters.status" @change="load()"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold">
          <option value="">All Statuses</option>
          <option>completed</option><option>pending</option><option>failed</option>
        </select>
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">From</label>
        <input type="date" x-model="filters.from" @change="load()"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold">
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">To</label>
        <input type="date" x-model="filters.to" @change="load()"
               class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold">
      </div>
      <button @click="filters={currency:'',giving_type:'',status:'',from:'',to:''}; load()"
              class="text-sm text-gray-400 hover:text-gray-700 px-3 py-2">Clear</button>
    </div>

    <!-- Table -->
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="text-left px-6 py-3.5 font-medium text-gray-500">Date</th>
              <th class="text-left px-4 py-3.5 font-medium text-gray-500">Reference</th>
              <th class="text-left px-4 py-3.5 font-medium text-gray-500">Donor</th>
              <th class="text-right px-4 py-3.5 font-medium text-gray-500">Amount</th>
              <th class="text-left px-4 py-3.5 font-medium text-gray-500">Type</th>
              <th class="text-center px-4 py-3.5 font-medium text-gray-500">Status</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="loading">
              <tr><td colspan="6" class="text-center py-12 text-gray-400"><i class="ti ti-loader-2 animate-spin text-2xl"></i></td></tr>
            </template>
            <template x-if="!loading && records.length === 0">
              <tr><td colspan="6" class="text-center py-12 text-gray-400">No giving records found</td></tr>
            </template>
            <template x-for="r in records" :key="r.id">
              <tr class="border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer" @click="selected = r">
                <td class="px-6 py-4 text-gray-600 text-xs" x-text="formatDate(r.created_at)"></td>
                <td class="px-4 py-4 font-mono text-xs text-gray-500" x-text="r.reference"></td>
                <td class="px-4 py-4 text-gray-700" x-text="r.donor_name || 'Anonymous'"></td>
                <td class="px-4 py-4 text-right font-semibold text-gray-900" x-text="formatCurrency(r.amount, r.currency)"></td>
                <td class="px-4 py-4">
                  <span class="capitalize text-xs bg-navy/5 text-navy px-2 py-0.5 rounded-full" x-text="r.giving_type"></span>
                </td>
                <td class="px-4 py-4 text-center">
                  <span :class="{
                    'bg-green-100 text-green-700': r.status === 'completed',
                    'bg-amber-100 text-amber-700': r.status === 'pending',
                    'bg-red-100 text-red-600':    r.status === 'failed'
                  }" class="text-xs px-2.5 py-1 rounded-full font-medium capitalize" x-text="r.status"></span>
                </td>
              </tr>
            </template>
          </tbody>
          <tfoot x-show="records.length > 0">
            <tr class="border-t-2 border-gray-200 bg-gray-50">
              <td colspan="3" class="px-6 py-3 text-sm font-semibold text-gray-700">Total (filtered, completed)</td>
              <td class="px-4 py-3 text-right font-bold text-gray-900" x-text="totalsDisplay"></td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

  </main>
</div>

<!-- Detail modal -->
<div x-data="{ get s() { return document.querySelector('[x-data]').__x?.$data.selected } }"
     class="hidden" id="giving-modal">
</div>

<script>
function givingRecords() {
  return {
    records:      [],
    selected:     null,
    filters:      { currency:'', giving_type:'', status:'', from:'', to:'' },
    loading:      true,
    totalsDisplay:'',

    async init() {
      authGuard();
      await this.load();
    },

    async load() {
      this.loading = true;
      try {
        const p = new URLSearchParams();
        Object.entries(this.filters).forEach(([k,v]) => { if(v) p.set(k,v); });
        this.records = await apiGet(`/api/admin/givings?${p}`);
        document.getElementById('giving-count').textContent = `${this.records.length} records`;

        // Calculate totals per currency for completed only
        const completed = this.records.filter(r => r.status === 'completed');
        const totals    = {};
        completed.forEach(r => {
          totals[r.currency] = (totals[r.currency] || 0) + parseFloat(r.amount);
        });
        this.totalsDisplay = Object.entries(totals)
          .map(([c, t]) => formatCurrency(t, c))
          .join(' · ') || '—';

        window._csvData = this.records;
      } catch {} finally {
        this.loading = false;
      }
    }
  }
}

function exportCSV() {
  const data = window._csvData || [];
  if (!data.length) return;
  const headers = ['Date','Reference','Donor Name','Donor Email','Amount','Currency','Type','Status'];
  const rows    = data.map(r => [
    r.created_at, r.reference, r.donor_name||'', r.donor_email||'',
    r.amount, r.currency, r.giving_type, r.status
  ]);
  const csv = [headers, ...rows].map(r => r.map(v => `"${v}"`).join(',')).join('\n');
  const a   = document.createElement('a');
  a.href    = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
  a.download = `giving-records-${new Date().toISOString().split('T')[0]}.csv`;
  a.click();
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

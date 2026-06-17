<?php $pageTitle = 'Notification History'; $activePage = 'app-notify-history'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
    <h1 class="text-xl font-semibold text-gray-900">Notification History</h1>
    <p class="text-sm text-gray-500 mt-0.5">All push notifications sent from the dashboard</p>
  </header>

  <main class="flex-1 p-8" x-data="notifyHistory()" x-init="init()">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 bg-gray-50">
            <th class="text-left px-6 py-3.5 font-medium text-gray-500">Notification</th>
            <th class="text-left px-4 py-3.5 font-medium text-gray-500">Target</th>
            <th class="text-right px-4 py-3.5 font-medium text-gray-500">Sent</th>
            <th class="text-left px-4 py-3.5 font-medium text-gray-500">Date</th>
          </tr>
        </thead>
        <tbody>
          <template x-if="loading">
            <tr><td colspan="4" class="text-center py-12 text-gray-400"><i class="ti ti-loader-2 animate-spin text-2xl"></i></td></tr>
          </template>
          <template x-if="!loading && rows.length === 0">
            <tr><td colspan="4" class="text-center py-12 text-gray-400">No notifications sent yet.</td></tr>
          </template>
          <template x-for="n in rows" :key="n.id">
            <tr class="border-b border-gray-50 hover:bg-gray-50">
              <td class="px-6 py-4">
                <p class="font-medium text-gray-900" x-text="n.title"></p>
                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1" x-text="n.message"></p>
              </td>
              <td class="px-4 py-4">
                <span class="text-xs bg-navy/5 text-navy px-2.5 py-1 rounded-full capitalize" x-text="n.target"></span>
              </td>
              <td class="px-4 py-4 text-right font-medium text-gray-900" x-text="n.sent_count?.toLocaleString()"></td>
              <td class="px-4 py-4 text-gray-500 text-xs" x-text="formatDateTime(n.created_at)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </main>
</div>

<script>
function notifyHistory() {
  return {
    rows:    [],
    loading: true,
    async init() {
      authGuard();
      try { this.rows = await apiGet('/api/admin/notify/history'); }
      catch {} finally { this.loading = false; }
    }
  }
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

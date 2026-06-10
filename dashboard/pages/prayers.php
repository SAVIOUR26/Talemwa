<?php $pageTitle = 'Prayer Requests'; $activePage = 'prayers'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">

  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Prayer Requests</h1>
      <p class="text-sm text-gray-500 mt-0.5" id="prayer-count">Loading…</p>
    </div>
  </header>

  <main class="flex-1 p-8" x-data="prayerList()" x-init="init()">

    <!-- Filter tabs -->
    <div class="flex gap-2 mb-6 flex-wrap">
      <template x-for="tab in tabs" :key="tab.value">
        <button @click="filter = tab.value; load()"
                :class="filter === tab.value ? 'bg-navy text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                class="px-4 py-2 rounded-xl text-sm font-medium transition"
                x-text="tab.label"></button>
      </template>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Prayer list -->
      <div class="space-y-3">
        <template x-if="loading">
          <div class="text-center py-12 text-gray-400"><i class="ti ti-loader-2 animate-spin text-2xl"></i></div>
        </template>
        <template x-if="!loading && prayers.length === 0">
          <div class="text-center py-12 text-gray-400 bg-white rounded-2xl border border-gray-200">
            <i class="ti ti-pray text-4xl mb-2 block"></i>
            No prayer requests in this category
          </div>
        </template>
        <template x-for="p in prayers" :key="p.id">
          <div @click="selected = p"
               :class="selected?.id === p.id ? 'border-gold ring-1 ring-gold' : 'border-gray-200 hover:border-gray-300'"
               class="bg-white border rounded-2xl p-4 cursor-pointer transition">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <span x-show="p.is_urgent" class="bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">URGENT</span>
                  <span x-show="!p.is_read" class="bg-amber-100 text-amber-700 text-xs font-medium px-2 py-0.5 rounded-full">Unread</span>
                  <span x-show="p.is_prayed" class="bg-green-100 text-green-700 text-xs font-medium px-2 py-0.5 rounded-full">Prayed</span>
                </div>
                <p class="text-sm text-gray-700 line-clamp-2" x-text="p.message"></p>
                <p class="text-xs text-gray-400 mt-1.5" x-text="timeAgo(p.created_at)"></p>
              </div>
              <i class="ti ti-chevron-right text-gray-300 flex-shrink-0 mt-1"></i>
            </div>
          </div>
        </template>
      </div>

      <!-- Detail panel -->
      <div x-show="selected" x-cloak class="bg-white border border-gray-200 rounded-2xl p-6 h-fit sticky top-24">
        <div class="flex items-start justify-between mb-4">
          <h2 class="font-semibold text-gray-900">Prayer Request</h2>
          <span class="text-xs text-gray-400" x-text="formatDateTime(selected?.created_at)"></span>
        </div>

        <p class="text-gray-700 leading-relaxed" x-text="selected?.message"></p>

        <template x-if="selected?.contact">
          <div class="mt-4 p-3 bg-gray-50 rounded-xl">
            <p class="text-xs text-gray-500 mb-1">Contact</p>
            <p class="text-sm font-medium text-gray-900" x-text="selected?.contact"></p>
            <a :href="`mailto:${selected?.contact}?subject=Re: Your Prayer Request&body=Dear friend,%0A%0AThank you for sharing your prayer request with us.`"
               class="text-xs text-gold hover:underline mt-1 inline-flex items-center gap-1">
              <i class="ti ti-mail"></i> Reply by email
            </a>
          </div>
        </template>

        <div class="flex flex-wrap gap-2 mt-5">
          <button @click="markRead()" x-show="!selected?.is_read"
                  class="flex-1 py-2.5 text-sm font-medium bg-navy/5 hover:bg-navy/10 text-navy rounded-xl transition flex items-center justify-center gap-1.5">
            <i class="ti ti-check"></i> Mark as Read
          </button>
          <button @click="markPrayed()" x-show="!selected?.is_prayed"
                  class="flex-1 py-2.5 text-sm font-medium bg-green-50 hover:bg-green-100 text-green-700 rounded-xl transition flex items-center justify-center gap-1.5">
            <i class="ti ti-pray"></i> Mark as Prayed
          </button>
          <button @click="toggleUrgent()"
                  :class="selected?.is_urgent ? 'bg-red-500 text-white' : 'bg-red-50 text-red-600 hover:bg-red-100'"
                  class="flex-1 py-2.5 text-sm font-medium rounded-xl transition flex items-center justify-center gap-1.5">
            <i class="ti ti-alert-circle"></i>
            <span x-text="selected?.is_urgent ? 'Remove Urgent' : 'Mark Urgent'"></span>
          </button>
        </div>
      </div>

      <div x-show="!selected" x-cloak class="hidden lg:flex items-center justify-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center">
        <div>
          <i class="ti ti-hand-click text-4xl text-gray-300 block mb-2"></i>
          <p class="text-gray-400 text-sm">Select a prayer request to view details</p>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
function prayerList() {
  return {
    prayers:  [],
    selected: null,
    filter:   'all',
    loading:  true,
    tabs: [
      { label: 'All',     value: 'all' },
      { label: 'Unread',  value: 'unread' },
      { label: 'Urgent',  value: 'urgent' },
      { label: 'Prayed',  value: 'prayed' },
    ],

    async init() {
      authGuard();
      await this.load();
    },

    async load() {
      this.loading  = true;
      this.selected = null;
      try {
        const params = new URLSearchParams();
        if (this.filter === 'unread') params.set('is_read',   0);
        if (this.filter === 'urgent') params.set('is_urgent', 1);
        if (this.filter === 'prayed') params.set('is_prayed', 1);

        this.prayers = await apiGet(`/api/admin/prayers?${params}`);
        document.getElementById('prayer-count').textContent =
          `${this.prayers.length} request${this.prayers.length !== 1 ? 's' : ''}`;
      } catch {} finally {
        this.loading = false;
      }
    },

    async update(fields) {
      try {
        await apiPut(`/api/admin/prayers/${this.selected.id}`, fields);
        Object.assign(this.selected, fields);
        // Update in list
        const idx = this.prayers.findIndex(p => p.id === this.selected.id);
        if (idx !== -1) Object.assign(this.prayers[idx], fields);
      } catch (e) {
        showToast(e.message, 'error');
      }
    },

    markRead()      { this.update({ is_read:   1 }); showToast('Marked as read'); },
    markPrayed()    { this.update({ is_prayed: 1, is_read: 1 }); showToast('Marked as prayed 🙏'); },
    toggleUrgent()  {
      const val = this.selected.is_urgent ? 0 : 1;
      this.update({ is_urgent: val });
      showToast(val ? 'Marked as urgent' : 'Urgent flag removed');
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>

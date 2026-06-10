<?php $pageTitle = 'Giving Campaigns'; $activePage = 'giving-campaigns'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Giving Campaigns</h1>
      <p class="text-sm text-gray-500 mt-0.5">Track fundraising campaigns and project goals</p>
    </div>
    <button onclick="document.getElementById('campaignModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-navy text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-navy-700 transition">
      <i class="ti ti-plus"></i> New Campaign
    </button>
  </header>

  <main class="flex-1 p-8" x-data="campaignList()" x-init="init()">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
      <template x-if="loading">
        <div class="col-span-3 text-center py-12 text-gray-400"><i class="ti ti-loader-2 animate-spin text-2xl"></i></div>
      </template>
      <template x-if="!loading && campaigns.length === 0">
        <div class="col-span-3 text-center py-12 text-gray-400 bg-white rounded-2xl border border-gray-200">
          No campaigns yet. <button onclick="document.getElementById('campaignModal').classList.remove('hidden')" class="text-gold hover:underline">Create one</button>
        </div>
      </template>
      <template x-for="c in campaigns" :key="c.id">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
          <div class="flex items-start justify-between mb-3">
            <h3 class="font-semibold text-gray-900" x-text="c.title"></h3>
            <span :class="c.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                  class="text-xs px-2.5 py-1 rounded-full font-medium"
                  x-text="c.is_active ? 'Active' : 'Closed'"></span>
          </div>
          <p class="text-sm text-gray-500 mb-4 line-clamp-2" x-text="c.description || 'No description'"></p>
          <!-- Progress bar -->
          <div class="mb-2">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
              <span x-text="formatCurrency(c.raised_amount, c.currency) + ' raised'"></span>
              <span x-text="formatCurrency(c.goal_amount, c.currency) + ' goal'"></span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
              <div class="h-full bg-gold rounded-full transition-all"
                   :style="`width: ${Math.min(100, (c.raised_amount / c.goal_amount) * 100)}%`"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1"
               x-text="`${Math.round((c.raised_amount / c.goal_amount) * 100)}% of goal`"></p>
          </div>
          <template x-if="c.deadline">
            <p class="text-xs text-gray-400 mb-4" x-text="`Deadline: ${formatDate(c.deadline)}`"></p>
          </template>
          <div class="flex gap-2 mt-4">
            <button @click="archiveCampaign(c)" x-show="c.is_active"
                    class="flex-1 text-xs py-2 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50 transition">
              Close Campaign
            </button>
            <button @click="copyCampaignLink(c)"
                    class="flex-1 text-xs py-2 bg-navy/5 hover:bg-navy/10 text-navy rounded-xl transition flex items-center justify-center gap-1">
              <i class="ti ti-share"></i> Share Link
            </button>
          </div>
        </div>
      </template>
    </div>
  </main>
</div>

<!-- Campaign Modal -->
<div id="campaignModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4" x-data="campaignForm()">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 z-10">
    <h2 class="text-xl font-bold text-gray-900 mb-6">New Campaign</h2>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Campaign Title <span class="text-red-500">*</span></label>
        <input x-model="form.title" type="text" class="input-field w-full" placeholder="e.g. Church Building Project">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Goal Amount <span class="text-red-500">*</span></label>
          <input x-model="form.goal_amount" type="number" min="1" class="input-field w-full" placeholder="10000">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Currency</label>
          <select x-model="form.currency" class="input-field w-full">
            <option>USD</option><option>UGX</option><option>GBP</option><option>EUR</option>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Deadline (optional)</label>
        <input x-model="form.deadline" type="date" class="input-field w-full">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
        <textarea x-model="form.description" rows="3" class="input-field w-full resize-none" placeholder="What is this campaign for?"></textarea>
      </div>
    </div>
    <div x-show="error" x-cloak class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3" x-text="error"></div>
    <div class="flex gap-3 mt-6">
      <button @click="closeModal()" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm">Cancel</button>
      <button @click="save()" :disabled="saving" class="flex-1 bg-navy text-white py-3 rounded-xl font-semibold text-sm disabled:opacity-60">
        <span x-text="saving ? 'Creating…' : 'Create Campaign'"></span>
      </button>
    </div>
  </div>
</div>

<style>
.input-field { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.6rem 1rem; font-size: 0.875rem; outline: none; transition: border-color 0.15s; }
.input-field:focus { border-color: #C9A84C; box-shadow: 0 0 0 1px #C9A84C; }
</style>

<script>
function campaignList() {
  return {
    campaigns: [],
    loading:   true,
    async init() {
      authGuard();
      await this.load();
    },
    async load() {
      this.loading = true;
      try { this.campaigns = await apiGet('/api/admin/campaigns'); }
      catch {} finally { this.loading = false; }
    },
    async archiveCampaign(c) {
      if (!confirmAction(`Close "${c.title}"? It will no longer be shown in the app.`)) return;
      try {
        await apiDelete(`/api/admin/campaigns/${c.id}`);
        c.is_active = 0;
        showToast('Campaign closed');
      } catch(e) { showToast(e.message, 'error'); }
    },
    copyCampaignLink(c) {
      const url = `https://roberttalemwa.online/give?campaign=${c.id}`;
      navigator.clipboard.writeText(url).then(() => showToast('Campaign link copied!'));
    }
  }
}

function campaignForm() {
  return {
    form:   { title:'', goal_amount:'', currency:'USD', deadline:'', description:'' },
    saving: false,
    error:  '',
    closeModal() { document.getElementById('campaignModal').classList.add('hidden'); this.error = ''; },
    async save() {
      if (!this.form.title || !this.form.goal_amount) { this.error = 'Title and goal are required.'; return; }
      this.saving = true; this.error = '';
      try {
        await apiPost('/api/admin/campaigns', this.form);
        showToast('Campaign created');
        this.closeModal();
        location.reload();
      } catch(e) { this.error = e.message; } finally { this.saving = false; }
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>

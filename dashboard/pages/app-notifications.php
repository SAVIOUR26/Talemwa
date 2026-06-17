<?php $pageTitle = 'Send Notification'; $activePage = 'app-notifications'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
    <h1 class="text-xl font-semibold text-gray-900">Send Push Notification</h1>
    <p class="text-sm text-gray-500 mt-0.5">Broadcast a message to app users</p>
  </header>

  <main class="flex-1 p-8" x-data="notifyForm()" x-init="init()">
    <div class="max-w-2xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-6">

      <!-- Form -->
      <div class="lg:col-span-3 space-y-5">

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Message</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
              <input x-model="form.title" @input="updatePreview()" type="text" maxlength="65"
                     class="input-field w-full" placeholder="Ministry Update">
              <p class="text-xs text-gray-400 mt-1" x-text="`${form.title.length}/65`"></p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Message <span class="text-red-500">*</span></label>
              <textarea x-model="form.message" @input="updatePreview()" rows="3" maxlength="178"
                        class="input-field w-full resize-none" placeholder="Write your message…"></textarea>
              <p class="text-xs text-gray-400 mt-1" x-text="`${form.message.length}/178`"></p>
            </div>
          </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Target & Type</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Send to</label>
              <div class="flex gap-2">
                <template x-for="t in targets" :key="t.value">
                  <button @click="form.target = t.value"
                          :class="form.target === t.value ? 'bg-navy text-white' : 'border border-gray-200 text-gray-600 hover:bg-gray-50'"
                          class="flex-1 py-2 rounded-xl text-sm font-medium transition"
                          x-text="t.label"></button>
                </template>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Notification type</label>
              <select x-model="form.dataType" @change="form.linkId = ''"
                      class="input-field w-full">
                <option value="general">General announcement</option>
                <option value="sermon">Link to a Sermon</option>
                <option value="live">Link to Live Stream</option>
                <option value="event">Link to an Event</option>
              </select>
            </div>

            <div x-show="form.dataType === 'sermon'" x-cloak>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Select Sermon</label>
              <select x-model="form.linkId" class="input-field w-full">
                <option value="">— Choose sermon —</option>
                <template x-for="s in sermons" :key="s.id">
                  <option :value="s.id" x-text="s.title"></option>
                </template>
              </select>
            </div>

            <div x-show="form.dataType === 'event'" x-cloak>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Select Event</label>
              <select x-model="form.linkId" class="input-field w-full">
                <option value="">— Choose event —</option>
                <template x-for="e in events" :key="e.id">
                  <option :value="e.id" x-text="e.title"></option>
                </template>
              </select>
            </div>
          </div>
        </div>

        <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3" x-text="error"></div>
        <div x-show="success" x-cloak class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3" x-text="success"></div>

        <button @click="send()" :disabled="sending"
                class="w-full bg-navy text-white py-3.5 rounded-xl font-semibold hover:bg-navy-700 transition disabled:opacity-60 flex items-center justify-center gap-2">
          <svg x-show="sending" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <i x-show="!sending" class="ti ti-send"></i>
          <span x-text="sending ? 'Sending…' : 'Send Notification'"></span>
        </button>

      </div>

      <!-- Phone preview -->
      <div class="lg:col-span-2">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 sticky top-24">
          <p class="text-xs font-medium text-gray-500 mb-4 uppercase tracking-wider">Preview</p>
          <div class="bg-gray-900 rounded-2xl p-4">
            <div class="bg-white/10 rounded-xl p-3">
              <div class="flex items-start gap-2">
                <div class="w-8 h-8 rounded-lg bg-gold flex items-center justify-center flex-shrink-0">
                  <i class="ti ti-broadcast text-navy text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-white text-xs font-semibold" x-text="form.title || 'Notification title'"></p>
                  <p class="text-gray-400 text-xs mt-0.5 line-clamp-2" x-text="form.message || 'Message body will appear here'"></p>
                </div>
              </div>
            </div>
            <p class="text-gray-600 text-xs text-center mt-3">Talemwa App · now</p>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>

<style>
.input-field { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.6rem 1rem; font-size: 0.875rem; outline: none; transition: border-color 0.15s; }
.input-field:focus { border-color: #C9A84C; box-shadow: 0 0 0 1px #C9A84C; }
</style>

<script>
function notifyForm() {
  return {
    form:    { title:'', message:'', target:'all', dataType:'general', linkId:'' },
    targets: [{ label:'All Users', value:'all' }, { label:'Android', value:'android' }, { label:'iOS', value:'ios' }],
    sermons: [],
    events:  [],
    sending: false,
    error:   '',
    success: '',

    async init() {
      authGuard();
      const [s, e] = await Promise.all([
        apiGet('/api/sermons?page=1').catch(()=>({items:[]})),
        apiGet('/api/events').catch(()=>[])
      ]);
      this.sermons = s.items || [];
      this.events  = Array.isArray(e) ? e : [];
    },

    updatePreview() {},

    async send() {
      if (!this.form.title || !this.form.message) { this.error = 'Title and message are required.'; return; }
      this.sending = true; this.error = ''; this.success = '';
      try {
        const data = { title: this.form.title, message: this.form.message, target: this.form.target };
        if (this.form.dataType !== 'general') {
          data.data = { type: this.form.dataType, id: String(this.form.linkId) };
        }
        const result = await apiPost('/api/admin/notify', data);
        this.success = `✓ Sent to ${result.sent} device${result.sent !== 1 ? 's' : ''}`;
        this.form    = { title:'', message:'', target:'all', dataType:'general', linkId:'' };
      } catch(e) { this.error = e.message; } finally { this.sending = false; }
    }
  }
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

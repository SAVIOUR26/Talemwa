<?php $pageTitle = 'Events'; $activePage = 'events'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">

  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Events</h1>
      <p class="text-sm text-gray-500 mt-0.5">Manage upcoming ministry events</p>
    </div>
    <button @click.prevent="openModal()" onclick="document.getElementById('eventModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-navy text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-navy-700 transition">
      <i class="ti ti-plus"></i> New Event
    </button>
  </header>

  <main class="flex-1 p-8" x-data="eventList()" x-init="init()">

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 bg-gray-50">
            <th class="text-left px-6 py-3.5 font-medium text-gray-500">Event</th>
            <th class="text-left px-4 py-3.5 font-medium text-gray-500">Date & Time</th>
            <th class="text-left px-4 py-3.5 font-medium text-gray-500">Location</th>
            <th class="text-center px-4 py-3.5 font-medium text-gray-500">Type</th>
            <th class="text-right px-6 py-3.5 font-medium text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody>
          <template x-if="loading">
            <tr><td colspan="5" class="text-center py-12 text-gray-400">
              <i class="ti ti-loader-2 animate-spin text-2xl"></i>
            </td></tr>
          </template>
          <template x-if="!loading && events.length === 0">
            <tr><td colspan="5" class="text-center py-12 text-gray-400">
              No upcoming events. <button @click="openModal()" class="text-gold hover:underline">Create one</button>
            </td></tr>
          </template>
          <template x-for="e in events" :key="e.id">
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
              <td class="px-6 py-4">
                <p class="font-medium text-gray-900" x-text="e.title"></p>
                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1" x-text="e.description || '—'"></p>
              </td>
              <td class="px-4 py-4">
                <p class="font-medium text-gray-700" x-text="formatDate(e.event_date)"></p>
                <p class="text-xs text-gray-400" x-text="e.event_time || ''"></p>
              </td>
              <td class="px-4 py-4 text-gray-600 text-sm" x-text="e.location || '—'"></td>
              <td class="px-4 py-4 text-center">
                <span :class="e.is_online ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600'"
                      class="text-xs px-2.5 py-1 rounded-full font-medium"
                      x-text="e.is_online ? 'Online' : 'In Person'"></span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="editEvent(e)"
                          class="p-1.5 text-gray-400 hover:text-navy hover:bg-gray-100 rounded-lg transition" title="Edit">
                    <i class="ti ti-edit"></i>
                  </button>
                  <button @click="notifyEvent(e)"
                          class="p-1.5 text-gray-400 hover:text-gold hover:bg-gold/10 rounded-lg transition" title="Send notification">
                    <i class="ti ti-send"></i>
                  </button>
                  <button @click="deleteEvent(e)"
                          class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete">
                    <i class="ti ti-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

  </main>
</div>

<!-- Event Modal -->
<div id="eventModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4" x-data="eventForm()">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 z-10 max-h-[90vh] overflow-y-auto">
    <h2 class="text-xl font-bold text-gray-900 mb-6" id="modal-title">New Event</h2>

    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
        <input x-model="form.title" type="text" class="input-field w-full" placeholder="e.g. Sunday Service">
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
          <input x-model="form.event_date" type="date" class="input-field w-full">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Time</label>
          <input x-model="form.event_time" type="time" class="input-field w-full">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Location</label>
        <input x-model="form.location" type="text" class="input-field w-full" placeholder="e.g. Kampala Pentecostal Church">
      </div>
      <div class="flex items-center justify-between py-2">
        <p class="text-sm font-medium text-gray-700">Online Event</p>
        <button type="button" @click="form.is_online = form.is_online ? 0 : 1"
                :class="form.is_online ? 'bg-navy' : 'bg-gray-200'"
                class="relative w-10 h-5 rounded-full transition">
          <span :class="form.is_online ? 'translate-x-5' : 'translate-x-0.5'"
                class="absolute top-0.5 left-0 w-4 h-4 bg-white rounded-full shadow transition-transform"></span>
        </button>
      </div>
      <div x-show="form.is_online">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Stream URL</label>
        <input x-model="form.stream_url" type="url" class="input-field w-full" placeholder="https://youtube.com/live/…">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
        <textarea x-model="form.description" rows="3" class="input-field w-full resize-none" placeholder="Event details…"></textarea>
      </div>
    </div>

    <div x-show="error" x-cloak class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3" x-text="error"></div>

    <div class="flex gap-3 mt-6">
      <button @click="closeModal()" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl font-medium hover:bg-gray-50 transition text-sm">Cancel</button>
      <button @click="save()" :disabled="saving"
              class="flex-1 bg-navy text-white py-3 rounded-xl font-semibold hover:bg-navy-700 transition disabled:opacity-60 text-sm">
        <span x-text="saving ? 'Saving…' : (editId ? 'Save Changes' : 'Create Event')"></span>
      </button>
    </div>
  </div>
</div>

<style>
.input-field { border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.6rem 1rem; font-size: 0.875rem; outline: none; transition: border-color 0.15s; }
.input-field:focus { border-color: #C9A84C; box-shadow: 0 0 0 1px #C9A84C; }
</style>

<script>
// List component
function eventList() {
  return {
    events:  [],
    loading: true,

    async init() {
      authGuard();
      await this.load();
    },

    async load() {
      this.loading = true;
      try {
        this.events = await apiGet('/api/events');
      } catch {} finally {
        this.loading = false;
      }
    },

    openModal()   { document.getElementById('eventModal').classList.remove('hidden'); },
    closeModal()  { document.getElementById('eventModal').classList.add('hidden'); this.load(); },

    editEvent(e) {
      window._editEvent = e;
      document.getElementById('modal-title').textContent = 'Edit Event';
      document.getElementById('eventModal').classList.remove('hidden');
      document.getElementById('eventModal').__x.$data.loadEdit(e);
    },

    async notifyEvent(e) {
      if (!confirm(`Send a push notification about "${e.title}" to all app users?`)) return;
      try {
        await apiPost('/api/admin/notify', {
          title:   '📅 Upcoming Event',
          message: `${e.title} — ${formatDate(e.event_date)}`,
          data:    { type: 'event', id: String(e.id) }
        });
        showToast('Push notification sent');
      } catch (err) {
        showToast(err.message, 'error');
      }
    },

    async deleteEvent(e) {
      if (!confirmAction(`Delete "${e.title}"?`)) return;
      try {
        await apiDelete(`/api/admin/events/${e.id}`);
        this.events = this.events.filter(x => x.id !== e.id);
        showToast('Event deleted');
      } catch (err) {
        showToast(err.message, 'error');
      }
    }
  }
}

// Form component (inside modal)
function eventForm() {
  return {
    editId: null,
    form:   { title:'', event_date:'', event_time:'', location:'', is_online:0, stream_url:'', description:'' },
    saving: false,
    error:  '',

    closeModal() {
      document.getElementById('eventModal').classList.add('hidden');
      this.reset();
    },

    reset() {
      this.editId = null;
      this.form   = { title:'', event_date:'', event_time:'', location:'', is_online:0, stream_url:'', description:'' };
      this.error  = '';
      document.getElementById('modal-title').textContent = 'New Event';
    },

    loadEdit(e) {
      this.editId = e.id;
      this.form   = { title: e.title, event_date: e.event_date, event_time: e.event_time || '',
                      location: e.location || '', is_online: e.is_online || 0,
                      stream_url: e.stream_url || '', description: e.description || '' };
    },

    async save() {
      if (!this.form.title || !this.form.event_date) { this.error = 'Title and date are required.'; return; }
      this.saving = true; this.error = '';
      try {
        if (this.editId) {
          await apiPut(`/api/admin/events/${this.editId}`, this.form);
          showToast('Event updated');
        } else {
          await apiPost('/api/admin/events', this.form);
          showToast('Event created');
        }
        this.closeModal();
      } catch (e) {
        this.error = e.message;
      } finally {
        this.saving = false;
      }
    }
  }
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

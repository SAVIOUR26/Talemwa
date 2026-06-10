<?php $pageTitle = 'Radio Schedule'; $activePage = 'radio-schedule'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Radio Schedule</h1>
      <p class="text-sm text-gray-500 mt-0.5">Weekly broadcast programme</p>
    </div>
    <button onclick="document.getElementById('scheduleModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-navy text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-navy-700 transition">
      <i class="ti ti-plus"></i> Add Slot
    </button>
  </header>

  <main class="flex-1 p-8" x-data="scheduleList()" x-init="init()">

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <template x-for="day in days" :key="day">
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
          <div class="bg-navy px-4 py-3">
            <p class="text-white font-semibold text-sm capitalize" x-text="day"></p>
          </div>
          <div class="divide-y divide-gray-50">
            <template x-if="slotsFor(day).length === 0">
              <p class="text-xs text-gray-400 px-4 py-4 italic">No programmes scheduled</p>
            </template>
            <template x-for="slot in slotsFor(day)" :key="slot.id">
              <div :class="slot.is_active ? '' : 'opacity-40'" class="px-4 py-3 flex items-start justify-between gap-2">
                <div>
                  <p class="text-sm font-medium text-gray-900" x-text="slot.program_name"></p>
                  <p class="text-xs text-gray-500 mt-0.5" x-text="`${slot.start_time} – ${slot.end_time}`"></p>
                  <p class="text-xs text-gray-400 mt-0.5 line-clamp-1" x-text="slot.description || ''"></p>
                </div>
                <div class="flex gap-1 flex-shrink-0">
                  <button @click="editSlot(slot)" class="p-1.5 text-gray-400 hover:text-navy hover:bg-gray-100 rounded-lg transition">
                    <i class="ti ti-edit text-xs"></i>
                  </button>
                </div>
              </div>
            </template>
          </div>
        </div>
      </template>
    </div>
  </main>
</div>

<!-- Schedule Slot Modal -->
<div id="scheduleModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4" x-data="scheduleForm()">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 z-10">
    <h2 class="text-xl font-bold text-gray-900 mb-6" id="schedule-modal-title">Add Schedule Slot</h2>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Day <span class="text-red-500">*</span></label>
        <select x-model="form.day_of_week" class="input-field w-full">
          <option value="">— Select day —</option>
          <option>monday</option><option>tuesday</option><option>wednesday</option>
          <option>thursday</option><option>friday</option><option>saturday</option>
          <option>sunday</option><option>daily</option>
        </select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Start Time</label>
          <input x-model="form.start_time" type="time" class="input-field w-full">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5">End Time</label>
          <input x-model="form.end_time" type="time" class="input-field w-full">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Programme Name</label>
        <input x-model="form.program_name" type="text" list="program-names" class="input-field w-full" placeholder="Sunday Service Replay">
        <datalist id="program-names">
          <option>Sunday Service Replay</option><option>Bible Study</option>
          <option>Prayer Hour</option><option>Worship Music</option>
          <option>Morning Devotion</option>
        </datalist>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
        <input x-model="form.description" type="text" class="input-field w-full" placeholder="Optional description">
      </div>
      <div class="flex items-center justify-between py-2">
        <p class="text-sm font-medium text-gray-700">Active</p>
        <button type="button" @click="form.is_active = form.is_active ? 0 : 1"
                :class="form.is_active ? 'bg-navy' : 'bg-gray-200'"
                class="relative w-10 h-5 rounded-full transition">
          <span :class="form.is_active ? 'translate-x-5' : 'translate-x-0.5'"
                class="absolute top-0.5 left-0 w-4 h-4 bg-white rounded-full shadow transition-transform"></span>
        </button>
      </div>
    </div>
    <div x-show="error" x-cloak class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3" x-text="error"></div>
    <div class="flex gap-3 mt-6">
      <button @click="closeModal()" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm">Cancel</button>
      <button @click="save()" :disabled="saving" class="flex-1 bg-navy text-white py-3 rounded-xl font-semibold text-sm disabled:opacity-60">
        <span x-text="saving ? 'Saving…' : 'Save Slot'"></span>
      </button>
    </div>
  </div>
</div>

<style>
.input-field { border:1px solid #e5e7eb; border-radius:0.75rem; padding:0.6rem 1rem; font-size:0.875rem; outline:none; transition:border-color 0.15s; }
.input-field:focus { border-color:#C9A84C; box-shadow:0 0 0 1px #C9A84C; }
</style>

<script>
const DAYS_ORDER = ['daily','monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

function scheduleList() {
  return {
    slots: [],
    days:  DAYS_ORDER,
    async init() {
      authGuard();
      await this.load();
    },
    async load() {
      try { this.slots = await apiGet('/api/radio/schedule'); } catch {}
    },
    slotsFor(day) {
      return this.slots.filter(s => s.day_of_week === day);
    },
    editSlot(slot) {
      document.getElementById('schedule-modal-title').textContent = 'Edit Slot';
      document.getElementById('scheduleModal').classList.remove('hidden');
      document.getElementById('scheduleModal').__x.$data.loadEdit(slot);
    }
  }
}

function scheduleForm() {
  return {
    editId: null,
    form:   { day_of_week:'', start_time:'', end_time:'', program_name:'', description:'', is_active:1 },
    saving: false,
    error:  '',
    closeModal() {
      document.getElementById('scheduleModal').classList.add('hidden');
      this.editId = null;
      this.form   = { day_of_week:'', start_time:'', end_time:'', program_name:'', description:'', is_active:1 };
      this.error  = '';
      document.getElementById('schedule-modal-title').textContent = 'Add Schedule Slot';
    },
    loadEdit(slot) {
      this.editId = slot.id;
      this.form   = { day_of_week:slot.day_of_week, start_time:slot.start_time, end_time:slot.end_time,
                      program_name:slot.program_name, description:slot.description||'', is_active:slot.is_active };
    },
    async save() {
      if (!this.form.day_of_week || !this.form.program_name) { this.error='Day and programme name are required.'; return; }
      this.saving=true; this.error='';
      try {
        if (this.editId) {
          await apiPut(`/api/admin/radio/schedule/${this.editId}`, this.form);
        } else {
          await apiPost('/api/admin/radio/schedule', this.form);
        }
        showToast('Schedule saved');
        this.closeModal();
        location.reload();
      } catch(e) { this.error=e.message; } finally { this.saving=false; }
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>

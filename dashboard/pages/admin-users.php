<?php $pageTitle = 'Admin Users'; $activePage = 'admin-users'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Admin Users</h1>
      <p class="text-sm text-gray-500 mt-0.5">Manage who can access this dashboard</p>
    </div>
    <button id="new-user-btn" onclick="document.getElementById('userModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-navy text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-navy-700 transition">
      <i class="ti ti-plus"></i> New Admin
    </button>
  </header>

  <main class="flex-1 p-8" x-data="adminUsers()" x-init="init()">

    <!-- Role legend -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div class="bg-white border border-gray-200 rounded-xl p-4">
        <p class="text-sm font-semibold text-navy">Super Admin</p>
        <p class="text-xs text-gray-500 mt-1">Full access — all pages, admin management, settings</p>
      </div>
      <div class="bg-white border border-gray-200 rounded-xl p-4">
        <p class="text-sm font-semibold text-gray-700">Media Team</p>
        <p class="text-xs text-gray-500 mt-1">Sermons, radio, events, prayers, notifications</p>
      </div>
      <div class="bg-white border border-gray-200 rounded-xl p-4">
        <p class="text-sm font-semibold text-gray-500">Viewer</p>
        <p class="text-xs text-gray-500 mt-1">Read-only access to stats and reports</p>
      </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-gray-100 bg-gray-50">
            <th class="text-left px-6 py-3.5 font-medium text-gray-500">Name</th>
            <th class="text-left px-4 py-3.5 font-medium text-gray-500">Email</th>
            <th class="text-left px-4 py-3.5 font-medium text-gray-500">Role</th>
            <th class="text-left px-4 py-3.5 font-medium text-gray-500">Last Login</th>
            <th class="text-right px-6 py-3.5 font-medium text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody>
          <template x-if="loading">
            <tr><td colspan="5" class="text-center py-12 text-gray-400"><i class="ti ti-loader-2 animate-spin text-2xl"></i></td></tr>
          </template>
          <template x-for="u in users" :key="u.id">
            <tr class="border-b border-gray-50 hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-navy/10 flex items-center justify-center text-navy font-bold text-xs"
                       x-text="u.name.charAt(0).toUpperCase()"></div>
                  <span class="font-medium text-gray-900" x-text="u.name"></span>
                </div>
              </td>
              <td class="px-4 py-4 text-gray-600" x-text="u.email"></td>
              <td class="px-4 py-4">
                <span :class="{
                  'bg-gold/20 text-gold-dark':  u.role === 'super_admin',
                  'bg-navy/10 text-navy':        u.role === 'media',
                  'bg-gray-100 text-gray-500':   u.role === 'viewer'
                }" class="text-xs px-2.5 py-1 rounded-full font-medium capitalize"
                   x-text="u.role.replace('_',' ')"></span>
              </td>
              <td class="px-4 py-4 text-gray-400 text-xs" x-text="u.last_login ? timeAgo(u.last_login) : 'Never'"></td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="editUser(u)" class="p-1.5 text-gray-400 hover:text-navy hover:bg-gray-100 rounded-lg transition">
                    <i class="ti ti-edit"></i>
                  </button>
                  <button @click="deleteUser(u)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
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

<!-- User Modal -->
<div id="userModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4" x-data="userForm()">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 z-10">
    <h2 class="text-xl font-bold text-gray-900 mb-6" id="user-modal-title">New Admin</h2>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name</label>
        <input x-model="form.name" type="text" class="input-field w-full" placeholder="John Doe">
      </div>
      <div x-show="!editId">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
        <input x-model="form.email" type="email" class="input-field w-full" placeholder="john@roberttalemwa.online">
      </div>
      <div x-show="!editId">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
        <input x-model="form.password" type="password" class="input-field w-full" placeholder="Strong password">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
        <select x-model="form.role" class="input-field w-full">
          <option value="media">Media Team</option>
          <option value="viewer">Viewer</option>
          <option value="super_admin">Super Admin</option>
        </select>
      </div>
    </div>
    <div x-show="error" x-cloak class="mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3" x-text="error"></div>
    <div class="flex gap-3 mt-6">
      <button @click="closeModal()" class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl text-sm">Cancel</button>
      <button @click="save()" :disabled="saving" class="flex-1 bg-navy text-white py-3 rounded-xl font-semibold text-sm disabled:opacity-60">
        <span x-text="saving ? 'Saving…' : (editId ? 'Update Role' : 'Create Admin')"></span>
      </button>
    </div>
  </div>
</div>

<style>
.input-field { border:1px solid #e5e7eb; border-radius:0.75rem; padding:0.6rem 1rem; font-size:0.875rem; outline:none; }
.input-field:focus { border-color:#C9A84C; box-shadow:0 0 0 1px #C9A84C; }
</style>

<script>
function adminUsers() {
  return {
    users:   [],
    loading: true,
    async init() {
      authGuard();
      if (!isSuperAdmin()) { window.location.href = '/pages/overview.php'; return; }
      await this.load();
    },
    async load() {
      this.loading = true;
      try { this.users = await apiGet('/api/admin/admins'); }
      catch {} finally { this.loading = false; }
    },
    editUser(u) {
      document.getElementById('user-modal-title').textContent = 'Edit Admin';
      document.getElementById('userModal').classList.remove('hidden');
      document.getElementById('userModal').__x.$data.loadEdit(u);
    },
    async deleteUser(u) {
      if (!confirmAction(`Remove ${u.name} from admin access?`)) return;
      try {
        await apiDelete(`/api/admin/admins/${u.id}`);
        this.users = this.users.filter(x => x.id !== u.id);
        showToast('Admin removed');
      } catch(e) { showToast(e.message, 'error'); }
    }
  }
}

function userForm() {
  return {
    editId: null,
    form:   { name:'', email:'', password:'', role:'media' },
    saving: false,
    error:  '',
    closeModal() {
      document.getElementById('userModal').classList.add('hidden');
      this.editId=null; this.form={ name:'', email:'', password:'', role:'media' }; this.error='';
      document.getElementById('user-modal-title').textContent = 'New Admin';
    },
    loadEdit(u) {
      this.editId  = u.id;
      this.form    = { name:u.name, email:u.email, password:'', role:u.role };
    },
    async save() {
      if (!this.form.name) { this.error='Name is required.'; return; }
      this.saving=true; this.error='';
      try {
        if (this.editId) {
          await apiPut(`/api/admin/admins/${this.editId}`, { name:this.form.name, role:this.form.role });
        } else {
          if (!this.form.email || !this.form.password) { this.error='Email and password required.'; this.saving=false; return; }
          await apiPost('/api/admin/admins', this.form);
        }
        showToast('Saved successfully');
        this.closeModal();
        location.reload();
      } catch(e) { this.error=e.message; } finally { this.saving=false; }
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>

<?php $pageTitle = 'Settings'; $activePage = 'settings'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="ml-64 flex-1 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20">
    <h1 class="text-xl font-semibold text-gray-900">Settings</h1>
    <p class="text-sm text-gray-500 mt-0.5">Platform configuration and API keys</p>
  </header>

  <main class="flex-1 p-8" x-data="settings()" x-init="init()">
    <div class="max-w-2xl mx-auto space-y-6">

      <!-- Ministry Identity -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-4">Ministry Identity</h2>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-500 mb-1">App Name</label>
              <input type="text" value="Talemwa" class="input-field w-full" disabled>
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1">Domain</label>
              <input type="text" value="roberttalemwa.online" class="input-field w-full" disabled>
            </div>
          </div>
        </div>
      </div>

      <!-- Social Links -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-4">Social Links</h2>
        <div class="space-y-3">
          <div class="flex items-center gap-3">
            <i class="ti ti-brand-youtube text-red-500 text-xl w-6"></i>
            <input type="url" value="https://www.youtube.com/@pastortalemwarobert4160" class="input-field flex-1" placeholder="YouTube channel URL">
          </div>
          <div class="flex items-center gap-3">
            <i class="ti ti-brand-facebook text-blue-600 text-xl w-6"></i>
            <input type="url" class="input-field flex-1" placeholder="Facebook page URL">
          </div>
          <div class="flex items-center gap-3">
            <i class="ti ti-brand-tiktok text-gray-900 text-xl w-6"></i>
            <input type="text" class="input-field flex-1" placeholder="TikTok handle (@username)">
          </div>
        </div>
      </div>

      <!-- API Configuration -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-1">API Configuration</h2>
        <p class="text-sm text-gray-500 mb-4">These are configured in the server <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">.env</code> file on the VPS. Contact your developer to update them.</p>

        <div class="space-y-3">
          <div class="flex items-center justify-between py-3 border-b border-gray-100">
            <div>
              <p class="text-sm font-medium text-gray-700">AzuraCast</p>
              <p class="text-xs text-gray-400">radio.roberttalemwa.online</p>
            </div>
            <a href="https://radio.roberttalemwa.online" target="_blank"
               class="text-xs text-gold hover:underline flex items-center gap-1">
              <i class="ti ti-external-link"></i> Open Panel
            </a>
          </div>
          <div class="flex items-center justify-between py-3 border-b border-gray-100">
            <div>
              <p class="text-sm font-medium text-gray-700">Firebase (FCM)</p>
              <p class="text-xs text-gray-400">Push notifications</p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">Configured in .env</span>
          </div>
          <div class="flex items-center justify-between py-3 border-b border-gray-100">
            <div>
              <p class="text-sm font-medium text-gray-700">Flutterwave</p>
              <p class="text-xs text-gray-400">African payments</p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">Configured in .env</span>
          </div>
          <div class="flex items-center justify-between py-3">
            <div>
              <p class="text-sm font-medium text-gray-700">PayPal</p>
              <p class="text-xs text-gray-400">Diaspora giving</p>
            </div>
            <span class="text-xs text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">Configured in .env</span>
          </div>
        </div>
      </div>

      <!-- Change Password -->
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900 mb-4">Change Your Password</h2>
        <div class="space-y-3">
          <input type="password" x-model="pw.current" placeholder="Current password" class="input-field w-full">
          <input type="password" x-model="pw.new" placeholder="New password" class="input-field w-full">
          <input type="password" x-model="pw.confirm" placeholder="Confirm new password" class="input-field w-full">
        </div>
        <div x-show="pw.error" x-cloak class="mt-3 text-sm text-red-600" x-text="pw.error"></div>
        <div x-show="pw.success" x-cloak class="mt-3 text-sm text-green-600" x-text="pw.success"></div>
        <button @click="changePassword()"
                class="mt-4 px-5 py-2.5 bg-navy text-white text-sm font-medium rounded-xl hover:bg-navy-700 transition">
          Update Password
        </button>
      </div>

      <!-- Danger zone -->
      <div class="bg-white border border-red-200 rounded-2xl p-6">
        <h2 class="font-semibold text-red-600 mb-2">Account</h2>
        <a href="/auth/logout.php"
           class="inline-flex items-center gap-2 text-red-500 hover:text-red-700 text-sm font-medium transition">
          <i class="ti ti-logout"></i> Sign out of this dashboard
        </a>
      </div>

    </div>
  </main>
</div>

<style>
.input-field { border:1px solid #e5e7eb; border-radius:0.75rem; padding:0.6rem 1rem; font-size:0.875rem; outline:none; transition:border-color 0.15s; }
.input-field:focus { border-color:#C9A84C; box-shadow:0 0 0 1px #C9A84C; }
.input-field:disabled { background:#f9fafb; cursor:not-allowed; }
</style>

<script>
function settings() {
  return {
    pw: { current:'', new:'', confirm:'', error:'', success:'' },

    init() {
      authGuard();
    },

    async changePassword() {
      this.pw.error = ''; this.pw.success = '';
      if (!this.pw.current || !this.pw.new) { this.pw.error = 'All fields required.'; return; }
      if (this.pw.new !== this.pw.confirm)  { this.pw.error = 'Passwords do not match.'; return; }
      if (this.pw.new.length < 8)           { this.pw.error = 'Password must be at least 8 characters.'; return; }

      // Password change requires a dedicated endpoint — notify user for now
      this.pw.success = 'Password change requires a server update. Please contact your developer or update directly in the database.';
    }
  }
}
</script>

<?php include '../partials/footer.php'; ?>

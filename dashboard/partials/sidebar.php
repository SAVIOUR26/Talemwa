<!-- Sidebar -->
<aside class="w-64 min-h-screen bg-navy flex flex-col fixed left-0 top-0 z-30" x-data="sidebar()">

  <!-- Logo -->
  <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
    <div class="w-9 h-9 rounded-lg bg-gold flex items-center justify-center flex-shrink-0">
      <i class="ti ti-broadcast text-navy text-lg"></i>
    </div>
    <div>
      <p class="text-white font-semibold text-sm leading-none">Talemwa</p>
      <p class="text-gray-500 text-xs mt-0.5">Ministry Platform</p>
    </div>
  </div>

  <!-- Nav -->
  <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">

    <?php $p = $activePage ?? ''; ?>

    <!-- Overview -->
    <a href="/pages/overview.php" class="nav-item <?= $p === 'overview' ? 'active' : '' ?>">
      <i class="ti ti-layout-dashboard w-5"></i>
      <span>Dashboard</span>
    </a>

    <!-- Radio -->
    <div class="pt-3 pb-1 px-3">
      <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">Radio</p>
    </div>
    <a href="/pages/radio-live.php" class="nav-item <?= $p === 'radio-live' ? 'active' : '' ?>">
      <i class="ti ti-radio w-5"></i>
      <span>Live Control</span>
      <span id="live-badge" class="ml-auto hidden w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
    </a>
    <a href="/pages/radio-schedule.php" class="nav-item <?= $p === 'radio-schedule' ? 'active' : '' ?>">
      <i class="ti ti-calendar-time w-5"></i>
      <span>Schedule</span>
    </a>
    <a href="/pages/radio-stats.php" class="nav-item <?= $p === 'radio-stats' ? 'active' : '' ?>">
      <i class="ti ti-chart-bar w-5"></i>
      <span>Listener Stats</span>
    </a>

    <!-- Sermons -->
    <div class="pt-3 pb-1 px-3">
      <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">Sermons</p>
    </div>
    <a href="/pages/sermons-list.php" class="nav-item <?= $p === 'sermons-list' ? 'active' : '' ?>">
      <i class="ti ti-books w-5"></i>
      <span>All Sermons</span>
    </a>
    <a href="/pages/sermons-upload.php" class="nav-item <?= $p === 'sermons-upload' ? 'active' : '' ?>">
      <i class="ti ti-upload w-5"></i>
      <span>Upload Sermon</span>
    </a>
    <a href="/pages/sermons-series.php" class="nav-item <?= $p === 'sermons-series' ? 'active' : '' ?>">
      <i class="ti ti-stack w-5"></i>
      <span>Series Manager</span>
    </a>

    <!-- App -->
    <div class="pt-3 pb-1 px-3">
      <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">App</p>
    </div>
    <a href="/pages/app-installs.php" class="nav-item <?= $p === 'app-installs' ? 'active' : '' ?>">
      <i class="ti ti-device-mobile w-5"></i>
      <span>Installs & Users</span>
    </a>
    <a href="/pages/app-notifications.php" class="nav-item <?= $p === 'app-notifications' ? 'active' : '' ?>">
      <i class="ti ti-send w-5"></i>
      <span>Send Notification</span>
    </a>
    <a href="/pages/app-notify-history.php" class="nav-item <?= $p === 'app-notify-history' ? 'active' : '' ?>">
      <i class="ti ti-history w-5"></i>
      <span>Notify History</span>
    </a>

    <!-- Giving -->
    <div class="pt-3 pb-1 px-3">
      <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">Giving</p>
    </div>
    <a href="/pages/giving-records.php" class="nav-item <?= $p === 'giving-records' ? 'active' : '' ?>">
      <i class="ti ti-coin w-5"></i>
      <span>Records</span>
    </a>
    <a href="/pages/giving-campaigns.php" class="nav-item <?= $p === 'giving-campaigns' ? 'active' : '' ?>">
      <i class="ti ti-heart w-5"></i>
      <span>Campaigns</span>
    </a>
    <a href="/pages/giving-reports.php" class="nav-item <?= $p === 'giving-reports' ? 'active' : '' ?>">
      <i class="ti ti-report-analytics w-5"></i>
      <span>Reports</span>
    </a>

    <!-- Prayer -->
    <div class="pt-3 pb-1 px-3">
      <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">Ministry</p>
    </div>
    <a href="/pages/prayers.php" class="nav-item <?= $p === 'prayers' ? 'active' : '' ?>">
      <i class="ti ti-pray w-5"></i>
      <span>Prayer Requests</span>
      <span id="prayer-badge" class="ml-auto hidden bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5 leading-none min-w-[1.2rem] text-center"></span>
    </a>
    <a href="/pages/events.php" class="nav-item <?= $p === 'events' ? 'active' : '' ?>">
      <i class="ti ti-calendar-event w-5"></i>
      <span>Events</span>
    </a>

    <!-- Admin only -->
    <div id="admin-section" class="hidden">
      <div class="pt-3 pb-1 px-3">
        <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">Admin</p>
      </div>
      <a href="/pages/admin-users.php" class="nav-item <?= $p === 'admin-users' ? 'active' : '' ?>">
        <i class="ti ti-users w-5"></i>
        <span>Admin Users</span>
      </a>
    </div>

    <div class="pt-3 pb-1 px-3">
      <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">System</p>
    </div>
    <a href="/pages/settings.php" class="nav-item <?= $p === 'settings' ? 'active' : '' ?>">
      <i class="ti ti-settings w-5"></i>
      <span>Settings</span>
    </a>

  </nav>

  <!-- User info + logout -->
  <div class="border-t border-white/10 px-4 py-4">
    <div class="flex items-center gap-3">
      <div class="w-8 h-8 rounded-full bg-gold/20 flex items-center justify-center">
        <i class="ti ti-user text-gold text-sm"></i>
      </div>
      <div class="flex-1 min-w-0">
        <p id="sidebar-name" class="text-white text-sm font-medium truncate">Admin</p>
        <p id="sidebar-role" class="text-gray-500 text-xs capitalize">media</p>
      </div>
      <a href="/auth/logout.php" title="Sign out" class="text-gray-500 hover:text-red-400 transition">
        <i class="ti ti-logout text-lg"></i>
      </a>
    </div>
  </div>

</aside>

<style>
  .nav-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    color: #6b7280;
    transition: all 0.15s;
    text-decoration: none;
  }
  .nav-item:hover { background: rgba(255,255,255,0.05); color: #fff; }
  .nav-item.active { background: rgba(201,168,76,0.15); color: #C9A84C; }
  .nav-item.active i { color: #C9A84C; }
</style>

<script>
function sidebar() {
  return {
    init() {
      // Show user info
      const name = localStorage.getItem('talemwa_name') || 'Admin';
      const role = localStorage.getItem('talemwa_role') || 'media';
      document.getElementById('sidebar-name').textContent = name;
      document.getElementById('sidebar-role').textContent = role.replace('_', ' ');

      // Show admin-only section
      if (role === 'super_admin') {
        document.getElementById('admin-section').classList.remove('hidden');
      }

      // Poll live status for badge
      this.checkLive();
      setInterval(() => this.checkLive(), 60000);

      // Poll prayer unread count
      this.checkPrayers();
      setInterval(() => this.checkPrayers(), 60000);
    },

    async checkLive() {
      try {
        const res  = await fetch(`${API}/api/live`);
        const json = await res.json();
        const badge = document.getElementById('live-badge');
        if (json.data?.is_live) {
          badge.classList.remove('hidden');
        } else {
          badge.classList.add('hidden');
        }
      } catch {}
    },

    async checkPrayers() {
      try {
        const data   = await apiGet('/api/admin/prayers?is_read=0');
        const badge  = document.getElementById('prayer-badge');
        const count  = Array.isArray(data) ? data.length : 0;
        if (count > 0) {
          badge.textContent = count > 99 ? '99+' : count;
          badge.classList.remove('hidden');
        } else {
          badge.classList.add('hidden');
        }
      } catch {}
    }
  }
}
</script>

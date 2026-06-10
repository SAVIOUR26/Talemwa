<?php $pageTitle = 'Radio · Live Control'; $activePage = 'radio-live'; ?>
<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<!-- Main content -->
<div class="ml-64 flex-1 flex flex-col min-h-screen">

  <!-- Top bar -->
  <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Live Control</h1>
      <p class="text-sm text-gray-500 mt-0.5">Manage the Sunday service live stream</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="/pages/radio-schedule.php" class="text-sm text-gray-500 hover:text-navy transition flex items-center gap-1.5">
        <i class="ti ti-calendar-time"></i> Schedule
      </a>
    </div>
  </header>

  <main class="flex-1 p-8" x-data="liveControl()" x-init="init()">

    <div class="max-w-4xl mx-auto space-y-6">

      <!-- Live Status Card -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-8 text-center">

          <!-- Status indicator -->
          <div class="flex items-center justify-center gap-3 mb-6">
            <div :class="isLive ? 'bg-red-500 animate-pulse' : 'bg-gray-300'" class="w-4 h-4 rounded-full"></div>
            <span :class="isLive ? 'text-red-500 font-bold' : 'text-gray-400'" class="text-lg" x-text="isLive ? 'LIVE NOW' : 'OFF AIR'"></span>
          </div>

          <!-- Service title when live -->
          <div x-show="isLive" x-cloak class="mb-4">
            <p class="text-2xl font-bold text-gray-900" x-text="liveTitle"></p>
            <p class="text-gray-500 text-sm mt-1">Duration: <span x-text="liveDuration" class="font-mono"></span></p>
          </div>

          <!-- Big toggle button -->
          <button
            @click="isLive ? endStream() : showGoLiveModal = true"
            :disabled="toggling"
            :class="isLive
              ? 'bg-gray-100 hover:bg-red-50 text-red-500 border-2 border-red-200 hover:border-red-400'
              : 'bg-red-500 hover:bg-red-600 text-white shadow-lg shadow-red-500/30'"
            class="inline-flex items-center gap-3 px-10 py-4 rounded-2xl text-lg font-bold transition disabled:opacity-60 mt-2"
          >
            <svg x-show="toggling" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <i x-show="!toggling" :class="isLive ? 'ti ti-player-stop-filled' : 'ti ti-broadcast'" class="text-2xl"></i>
            <span x-text="toggling ? 'Please wait…' : (isLive ? 'END STREAM' : 'GO LIVE')"></span>
          </button>

          <!-- Preview link when live -->
          <div x-show="isLive && youtubeId" x-cloak class="mt-4">
            <a :href="`https://www.youtube.com/watch?v=${youtubeId}`" target="_blank"
               class="inline-flex items-center gap-1.5 text-sm text-gold hover:text-gold-dark transition">
              <i class="ti ti-external-link"></i> Preview stream on YouTube
            </a>
          </div>

        </div>
      </div>

      <!-- Now Playing + Stats row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Now Playing -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6" x-data="nowPlaying()" x-init="init()">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-900">Now Playing · Radio</h2>
            <span class="text-xs text-gray-400">Updates every 30s</span>
          </div>

          <div x-show="!loaded" class="animate-pulse space-y-3">
            <div class="h-4 bg-gray-100 rounded w-3/4"></div>
            <div class="h-4 bg-gray-100 rounded w-1/2"></div>
          </div>

          <div x-show="loaded" x-cloak>
            <div class="flex items-start gap-4">
              <img :src="art || 'https://placehold.co/80x80/0A1628/C9A84C?text=🎵'" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
              <div class="min-w-0">
                <p class="font-semibold text-gray-900 truncate" x-text="title || 'No track info'"></p>
                <p class="text-gray-500 text-sm truncate mt-0.5" x-text="artist || ''"></p>
                <div class="flex items-center gap-4 mt-3">
                  <span :class="isOnline ? 'text-green-600 bg-green-50' : 'text-gray-500 bg-gray-100'"
                        class="text-xs font-medium px-2.5 py-1 rounded-full flex items-center gap-1">
                    <span :class="isOnline ? 'bg-green-500' : 'bg-gray-400'" class="w-1.5 h-1.5 rounded-full"></span>
                    <span x-text="isOnline ? 'Stream Online' : 'Stream Offline'"></span>
                  </span>
                  <span class="text-xs text-gray-500" x-text="`${listeners} listening`"></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Stream Health -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
          <h2 class="font-semibold text-gray-900 mb-4">Stream Info</h2>
          <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-500">Stream URL</span>
              <a href="https://radio.roberttalemwa.online/stream" target="_blank"
                 class="text-xs text-gold hover:underline truncate max-w-[180px]">radio.roberttalemwa.online/stream</a>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-50">
              <span class="text-sm text-gray-500">Radio Panel</span>
              <a href="https://radio.roberttalemwa.online" target="_blank"
                 class="text-xs text-gold hover:underline">AzuraCast Dashboard</a>
            </div>
            <div class="flex justify-between items-center py-2">
              <span class="text-sm text-gray-500">Website Live Page</span>
              <a href="https://roberttalemwa.online/live" target="_blank"
                 class="text-xs text-gold hover:underline">roberttalemwa.online/live</a>
            </div>
          </div>
        </div>

      </div>

      <!-- How it works -->
      <div class="bg-navy/5 border border-navy/10 rounded-2xl p-6">
        <h3 class="font-semibold text-navy mb-3 flex items-center gap-2">
          <i class="ti ti-info-circle"></i> How the live flow works
        </h3>
        <ol class="space-y-2 text-sm text-gray-600 list-decimal list-inside">
          <li>Click <strong>GO LIVE</strong> and enter your YouTube Video ID and service title</li>
          <li>All app users instantly receive a push notification: <em>"🔴 We Are Live!"</em></li>
          <li>The website and app show a red live banner — tapping it opens the YouTube player</li>
          <li>When the service ends, click <strong>END STREAM</strong> to take the banner down</li>
        </ol>
      </div>

    </div>

  </main>
</div>

<!-- GO LIVE Modal -->
<div x-data="liveControl()" x-cloak><!-- scoped separately below --></div>

<!-- Full GO LIVE modal (attached to liveControl Alpine scope) -->
<div
  x-show="showGoLiveModal"
  x-cloak
  x-data
  style="display:none"
  class="fixed inset-0 z-50 flex items-center justify-center px-4"
  id="goLiveModal"
>
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="document.getElementById('goLiveModal').style.display='none'"></div>
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 z-10">
    <h2 class="text-xl font-bold text-gray-900 mb-1">Go Live</h2>
    <p class="text-gray-500 text-sm mb-6">Enter the YouTube Video ID and service title to begin broadcasting</p>

    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">YouTube Video ID <span class="text-red-500">*</span></label>
        <input type="text" id="modal-youtube-id" placeholder="e.g. dQw4w9WgXcQ"
               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
        <p class="text-xs text-gray-400 mt-1">From the YouTube URL: youtube.com/watch?v=<strong>THIS_PART</strong></p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Service Title <span class="text-red-500">*</span></label>
        <input type="text" id="modal-service-title" placeholder="e.g. Sunday Morning Service — June 2026"
               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold focus:ring-1 focus:ring-gold">
      </div>
    </div>

    <div id="modal-error" class="hidden mt-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3"></div>

    <div class="flex gap-3 mt-6">
      <button onclick="document.getElementById('goLiveModal').style.display='none'"
              class="flex-1 border border-gray-200 text-gray-600 py-3 rounded-xl font-medium hover:bg-gray-50 transition text-sm">
        Cancel
      </button>
      <button onclick="confirmGoLive()"
              class="flex-1 bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-bold transition text-sm flex items-center justify-center gap-2">
        <i class="ti ti-broadcast"></i> Start Broadcasting
      </button>
    </div>
  </div>
</div>

<script>
function liveControl() {
  return {
    isLive:         false,
    liveTitle:      '',
    youtubeId:      '',
    toggling:       false,
    showGoLiveModal: false,
    liveStartedAt:  null,
    liveDuration:   '00:00:00',
    _timer:         null,

    async init() {
      authGuard();
      await this.refresh();
      setInterval(() => this.refresh(), 30000);
    },

    async refresh() {
      try {
        const data    = await apiGet('/api/live');
        this.isLive   = data.is_live;
        this.liveTitle = data.title || '';
        this.youtubeId = data.youtube_id || '';

        if (this.isLive && data.updated_at) {
          if (!this.liveStartedAt) {
            this.liveStartedAt = new Date(data.updated_at);
            this.startTimer();
          }
        } else {
          this.liveStartedAt = null;
          clearInterval(this._timer);
          this.liveDuration = '00:00:00';
        }
      } catch {}
    },

    startTimer() {
      clearInterval(this._timer);
      this._timer = setInterval(() => {
        const diff = Math.floor((Date.now() - this.liveStartedAt) / 1000);
        const h    = String(Math.floor(diff / 3600)).padStart(2, '0');
        const m    = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
        const s    = String(diff % 60).padStart(2, '0');
        this.liveDuration = `${h}:${m}:${s}`;
      }, 1000);
    },

    async endStream() {
      if (!confirm('End the live stream? The live banner will disappear for all app users.')) return;
      this.toggling = true;
      try {
        await apiPost('/api/admin/live', { is_live: false });
        showToast('Stream ended. Live banner removed.');
        await this.refresh();
      } catch (e) {
        showToast(e.message, 'error');
      } finally {
        this.toggling = false;
      }
    }
  }
}

function nowPlaying() {
  return {
    title:    '',
    artist:   '',
    art:      null,
    listeners: 0,
    isOnline: false,
    loaded:   false,

    async init() {
      await this.refresh();
      setInterval(() => this.refresh(), 30000);
    },

    async refresh() {
      try {
        const data     = await apiGet('/api/radio');
        this.isOnline  = data.is_online;
        this.title     = data.now_playing?.title || '';
        this.artist    = data.now_playing?.artist || '';
        this.art       = data.now_playing?.art || null;
        this.listeners = data.listeners || 0;
        this.loaded    = true;
      } catch {
        this.loaded = true;
      }
    }
  }
}

async function confirmGoLive() {
  const ytId  = document.getElementById('modal-youtube-id').value.trim();
  const title = document.getElementById('modal-service-title').value.trim();
  const err   = document.getElementById('modal-error');

  if (!ytId || !title) {
    err.textContent = 'Both fields are required.';
    err.classList.remove('hidden');
    return;
  }
  err.classList.add('hidden');

  try {
    await apiPost('/api/admin/live', { is_live: true, youtube_id: ytId, title });
    document.getElementById('goLiveModal').style.display = 'none';
    showToast('🔴 You are now live! Push notification sent to all users.');
    setTimeout(() => location.reload(), 1000);
  } catch (e) {
    err.textContent = e.message;
    err.classList.remove('hidden');
  }
}
</script>

<?php include '../partials/footer.php'; ?>

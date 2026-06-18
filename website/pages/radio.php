<?php
$pageTitle   = 'Online Radio';
$metaDesc    = 'Listen to Ministry Radio — sermons, worship, and devotionals 24/7 on radio.roberttalemwa.online';
$currentPage = 'radio';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/nav.php';
?>

<main class="flex-1" x-data="radioPage()" x-init="init()">

  <!-- Full player -->
  <div class="hero-gradient text-white py-16">
    <div class="max-w-lg mx-auto px-4 sm:px-6 text-center">

      <!-- Album art -->
      <div class="relative inline-block mb-8">
        <div class="w-48 h-48 rounded-3xl overflow-hidden mx-auto shadow-2xl shadow-black/50 ring-4 ring-gold/30">
          <img :src="art || 'https://placehold.co/192x192/0A1628/C9A84C?text=🎙️'"
               class="w-full h-full object-cover" alt="">
        </div>
        <div :class="playing ? 'animate-spin' : ''" style="animation-duration:8s"
             class="absolute -bottom-3 -right-3 w-12 h-12 bg-gold rounded-full flex items-center justify-center shadow-lg">
          <i class="ti ti-broadcast text-navy text-xl"></i>
        </div>
      </div>

      <!-- Now playing -->
      <div class="mb-6">
        <div class="flex items-center justify-center gap-2 mb-2">
          <span x-show="isOnline" class="flex items-center gap-1.5 text-xs font-bold text-green-400">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span> ON AIR
          </span>
          <span x-show="!isOnline" class="text-xs text-gray-500">OFFLINE</span>
        </div>
        <h1 class="text-2xl font-bold text-white" x-text="title || 'Ministry Radio'"></h1>
        <p class="text-gray-400 mt-1" x-text="artist || 'Pastor Robert Talemwa · Ministry'"></p>
        <p class="text-gray-500 text-sm mt-2" x-text="`${listeners} listening now`"></p>
      </div>

      <!-- Controls -->
      <div class="flex items-center justify-center gap-5 mb-6">
        <a href="/sermons" class="w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" title="Sermons">
          <i class="ti ti-books text-xl"></i>
        </a>
        <button @click="toggle()" :disabled="loading || !isOnline"
                class="w-20 h-20 rounded-full bg-gold hover:bg-gold-light flex items-center justify-center transition shadow-2xl shadow-gold/30 disabled:opacity-40">
          <svg x-show="loading" class="animate-spin w-8 h-8 text-navy" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
          </svg>
          <i x-show="!loading && !playing" class="ti ti-player-play-filled text-navy text-3xl"></i>
          <i x-show="!loading && playing"  class="ti ti-player-pause-filled text-navy text-3xl"></i>
        </button>
        <a :href="`https://wa.me/?text=${encodeURIComponent('Listening to Ministry Radio: https://roberttalemwa.online/radio')}`"
           target="_blank"
           class="w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" title="Share">
          <i class="ti ti-share text-xl"></i>
        </a>
      </div>

      <!-- Stream URL -->
      <p class="text-xs text-gray-500">
        Stream directly:
        <a href="https://radio.roberttalemwa.online/stream" class="text-gold hover:underline">radio.roberttalemwa.online/stream</a>
      </p>
    </div>
  </div>

  <!-- Schedule -->
  <section class="py-12 max-w-4xl mx-auto px-4 sm:px-6" x-data="scheduleSection()" x-init="init()">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Programme Schedule</h2>
    <div class="grid sm:grid-cols-2 gap-4">
      <template x-for="slot in schedule" :key="slot.id">
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
          <div class="flex items-start justify-between">
            <div>
              <span class="text-xs font-semibold text-gold uppercase tracking-wide capitalize" x-text="slot.day_of_week"></span>
              <h3 class="font-bold text-gray-900 mt-1" x-text="slot.program_name"></h3>
              <p class="text-sm text-gray-500 mt-1" x-text="`${slot.start_time} – ${slot.end_time} EAT`"></p>
              <p class="text-xs text-gray-400 mt-1" x-text="slot.description || ''"></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-navy/5 flex items-center justify-center flex-shrink-0">
              <i class="ti ti-radio text-navy"></i>
            </div>
          </div>
        </div>
      </template>
    </div>
  </section>

</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
function radioPage() {
  return {
    playing:   false,
    loading:   false,
    isOnline:  false,
    title:     'Ministry Radio',
    artist:    '',
    art:       null,
    listeners: 0,
    streamUrl: '',
    audio:     null,

    async init() {
      await this.fetchStatus();
      setInterval(() => this.fetchStatus(), 30000);
    },

    async fetchStatus() {
      try {
        const data     = await apiFetch('/api/radio');
        this.streamUrl = data.stream_url;
        this.isOnline  = data.is_online;
        this.title     = data.now_playing?.title || 'Ministry Radio';
        this.artist    = data.now_playing?.artist || '';
        this.art       = data.now_playing?.art || null;
        this.listeners = data.listeners || 0;
      } catch {}
    },

    toggle() {
      if (!this.streamUrl) return;
      if (this.playing) {
        this.audio?.pause();
        this.playing = false;
      } else {
        this.loading    = true;
        this.audio      = new Audio(this.streamUrl);
        this.audio.play()
          .then(() => { this.playing = true; this.loading = false; })
          .catch(() => { this.loading = false; });
      }
    }
  }
}

function scheduleSection() {
  return {
    schedule: [],
    async init() {
      try { this.schedule = await apiFetch('/api/radio/schedule'); } catch {}
    }
  }
}
</script>

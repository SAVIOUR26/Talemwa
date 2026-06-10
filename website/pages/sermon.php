<?php
$sermonId    = (int)($_GET['sermon_id'] ?? 0);
$pageTitle   = 'Sermon';
$currentPage = 'sermons';
include '../partials/head.php';
include '../partials/nav.php';
?>

<main x-data="sermonDetail(<?= $sermonId ?>)" x-init="init()" class="pb-8">

  <!-- Loading -->
  <div x-show="loading" class="flex items-center justify-center py-32">
    <i class="ti ti-loader-2 animate-spin text-4xl text-gold"></i>
  </div>

  <div x-show="!loading && sermon" x-cloak>

    <!-- Hero -->
    <div class="bg-navy text-white py-10">
      <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-4">
          <a href="/sermons" class="hover:text-gold transition">Sermons</a>
          <i class="ti ti-chevron-right text-xs"></i>
          <span x-text="sermon?.series || 'Message'"></span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold mb-4" x-text="sermon?.title"></h1>
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
          <span class="flex items-center gap-1.5">
            <i class="ti ti-user"></i> <span x-text="sermon?.speaker"></span>
          </span>
          <span x-show="sermon?.scripture" class="flex items-center gap-1.5">
            <i class="ti ti-book"></i> <span x-text="sermon?.scripture"></span>
          </span>
          <span x-show="sermon?.series" class="flex items-center gap-1.5">
            <i class="ti ti-stack"></i> <span x-text="sermon?.series"></span>
          </span>
          <span class="flex items-center gap-1.5">
            <i class="ti ti-player-play"></i>
            <span x-text="`${(sermon?.play_count || 0).toLocaleString()} plays`"></span>
          </span>
        </div>
      </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
      <div class="grid lg:grid-cols-3 gap-8">

        <!-- Main content -->
        <div class="lg:col-span-2 space-y-6">

          <!-- YouTube player -->
          <div x-show="sermon?.youtube_url" x-cloak class="rounded-2xl overflow-hidden shadow-lg aspect-video bg-black">
            <iframe :src="`https://www.youtube.com/embed/${getYoutubeId(sermon?.youtube_url)}`"
                    class="w-full h-full" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
          </div>

          <!-- Thumbnail if no YouTube -->
          <div x-show="!sermon?.youtube_url && sermon?.thumbnail_url" x-cloak>
            <img :src="sermon?.thumbnail_url" class="w-full rounded-2xl shadow-lg" alt="">
          </div>

          <!-- Audio player (for MP3 sermons) -->
          <div x-show="sermon?.mp3_url" x-cloak
               class="bg-navy rounded-2xl p-6 text-white"
               x-data="audioPlayer()">
            <p class="text-sm text-gray-400 mb-3">Audio Message</p>
            <div class="flex items-center gap-4">
              <button @click="toggle()" class="w-12 h-12 rounded-full bg-gold flex items-center justify-center flex-shrink-0">
                <i :class="playing ? 'ti ti-player-pause-filled' : 'ti ti-player-play-filled'" class="text-navy text-xl"></i>
              </button>
              <div class="flex-1">
                <input type="range" x-model="progress" @input="seek()" min="0" max="100"
                       class="w-full accent-gold cursor-pointer">
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                  <span x-text="currentTime"></span>
                  <span x-text="duration"></span>
                </div>
              </div>
              <select x-model="speed" @change="setSpeed()" class="bg-white/10 text-white text-xs rounded-lg px-2 py-1 border border-white/20">
                <option value="0.75">0.75x</option>
                <option value="1" selected>1x</option>
                <option value="1.25">1.25x</option>
                <option value="1.5">1.5x</option>
              </select>
            </div>
          </div>

          <!-- Description -->
          <div x-show="sermon?.description">
            <h2 class="text-lg font-bold text-gray-900 mb-3">About This Message</h2>
            <p class="text-gray-600 leading-relaxed" x-text="sermon?.description"></p>
          </div>

          <!-- Tags -->
          <div x-show="sermon?.tags" x-cloak class="flex flex-wrap gap-2">
            <template x-for="tag in (sermon?.tags || '').split(',')" :key="tag">
              <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full" x-text="tag.trim()"></span>
            </template>
          </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-5">

          <!-- Share -->
          <div class="bg-white border border-gray-200 rounded-2xl p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Share This Message</h3>
            <div class="space-y-2">
              <button @click="copyLink()"
                      class="w-full flex items-center gap-3 py-2.5 px-4 bg-gray-50 hover:bg-gray-100 rounded-xl text-sm transition">
                <i class="ti ti-link text-gray-500"></i> Copy Link
              </button>
              <a :href="`https://wa.me/?text=${encodeURIComponent(sermon?.title + ' - ' + window.location.href)}`"
                 target="_blank"
                 class="w-full flex items-center gap-3 py-2.5 px-4 bg-green-50 hover:bg-green-100 rounded-xl text-sm text-green-700 transition">
                <i class="ti ti-brand-whatsapp"></i> Share on WhatsApp
              </a>
              <a :href="sermon?.youtube_url" target="_blank" x-show="sermon?.youtube_url"
                 class="w-full flex items-center gap-3 py-2.5 px-4 bg-red-50 hover:bg-red-100 rounded-xl text-sm text-red-600 transition">
                <i class="ti ti-brand-youtube"></i> Watch on YouTube
              </a>
            </div>
          </div>

          <!-- Prayer & Giving -->
          <div class="bg-navy rounded-2xl p-5 text-white text-center">
            <i class="ti ti-pray text-gold text-3xl mb-3 block"></i>
            <p class="font-semibold mb-2">Need Prayer?</p>
            <p class="text-gray-400 text-sm mb-4">Submit a prayer request and we will pray with you.</p>
            <a href="/prayer" class="block bg-gold hover:bg-gold-light text-navy font-semibold py-2.5 rounded-xl text-sm transition">
              Submit Prayer Request
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Not found -->
  <div x-show="!loading && !sermon" x-cloak class="text-center py-32">
    <i class="ti ti-file-x text-6xl text-gray-200 block mb-4"></i>
    <p class="text-gray-500 mb-4">Sermon not found.</p>
    <a href="/sermons" class="text-gold hover:underline">Back to sermons</a>
  </div>

</main>

<?php include '../partials/radio-bar.php'; ?>
<?php include '../partials/footer.php'; ?>

<script>
function sermonDetail(id) {
  return {
    sermon:  null,
    loading: true,

    async init() {
      try { this.sermon = await apiFetch(`/api/sermons/${id}`); }
      catch {} finally { this.loading = false; }
    },

    getYoutubeId(url) {
      if (!url) return '';
      const m = url.match(/(?:v=|youtu\.be\/)([A-Za-z0-9_-]{11})/);
      return m ? m[1] : '';
    },

    copyLink() {
      navigator.clipboard.writeText(window.location.href)
        .then(() => alert('Link copied!'));
    }
  }
}

function audioPlayer() {
  return {
    audio:       null,
    playing:     false,
    progress:    0,
    speed:       1,
    currentTime: '0:00',
    duration:    '0:00',

    init() {
      const mp3 = this.$el.closest('[x-data]').__x.$data.sermon?.mp3_url;
      if (!mp3) return;
      this.audio = new Audio(mp3);
      this.audio.addEventListener('timeupdate', () => {
        if (!this.audio.duration) return;
        this.progress    = (this.audio.currentTime / this.audio.duration) * 100;
        this.currentTime = formatDuration(Math.floor(this.audio.currentTime));
      });
      this.audio.addEventListener('loadedmetadata', () => {
        this.duration = formatDuration(Math.floor(this.audio.duration));
      });
      this.audio.addEventListener('ended', () => { this.playing = false; });
    },

    toggle() {
      if (!this.audio) return;
      this.playing ? this.audio.pause() : this.audio.play();
      this.playing = !this.playing;
    },

    seek() {
      if (!this.audio?.duration) return;
      this.audio.currentTime = (this.progress / 100) * this.audio.duration;
    },

    setSpeed() {
      if (this.audio) this.audio.playbackRate = parseFloat(this.speed);
    }
  }
}
</script>

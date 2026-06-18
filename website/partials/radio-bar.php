<!-- Sticky Radio Player Bar -->
<div x-data="radioPlayer()" x-init="init()"
     x-show="isOnline"
     x-cloak
     class="fixed bottom-0 left-0 right-0 z-50 bg-navy border-t border-white/10 shadow-2xl">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-4">

    <!-- Album art -->
    <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 bg-white/10">
      <img :src="art || 'https://placehold.co/40x40/0A1628/C9A84C?text=♪'"
           class="w-full h-full object-cover" alt="">
    </div>

    <!-- Track info -->
    <div class="flex-1 min-w-0">
      <p class="text-white text-sm font-medium truncate" x-text="title"></p>
      <p class="text-gray-400 text-xs truncate" x-text="artist || 'Ministry Radio · roberttalemwa.online'"></p>
    </div>

    <!-- Listener count -->
    <div class="hidden sm:flex items-center gap-1.5 text-gray-400 text-xs flex-shrink-0">
      <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
      <span x-text="`${listeners} listening`"></span>
    </div>

    <!-- Play/pause -->
    <button @click="toggle()" :disabled="loading"
            class="w-11 h-11 rounded-full bg-gold hover:bg-gold-light flex items-center justify-center transition flex-shrink-0 disabled:opacity-60">
      <svg x-show="loading" class="animate-spin w-5 h-5 text-navy" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
      </svg>
      <i x-show="!loading && !playing" class="ti ti-player-play-filled text-navy text-lg"></i>
      <i x-show="!loading && playing"  class="ti ti-player-pause-filled text-navy text-lg"></i>
    </button>

    <!-- Volume -->
    <div class="hidden md:flex items-center gap-2 flex-shrink-0 w-28">
      <button @click="toggleMute()" class="text-gray-400 hover:text-white transition">
        <i class="ti text-base" :class="muted || volume == 0 ? 'ti-volume-3' : volume < 0.5 ? 'ti-volume-2' : 'ti-volume'"></i>
      </button>
      <input type="range" min="0" max="1" step="0.01" x-model.number="volume" @input="setVolume()"
             class="w-full h-1.5 accent-gold cursor-pointer">
    </div>

    <!-- Full radio link -->
    <a href="/radio" class="hidden sm:flex items-center gap-1 text-gray-400 hover:text-gold text-xs transition flex-shrink-0">
      <i class="ti ti-radio"></i> Full Player
    </a>

  </div>
</div>

<!-- Bottom padding so content isn't hidden behind the bar -->
<div class="h-16"></div>

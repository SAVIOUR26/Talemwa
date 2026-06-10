<?php $currentPage = $currentPage ?? ''; ?>

<!-- Live banner (shows when is_live=true) -->
<div x-data="liveBanner()" x-init="init()" x-cloak>
  <a x-show="isLive"
     :href="`/live?v=${youtubeId}`"
     class="flex items-center justify-center gap-3 bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-2.5 px-4 transition">
    <span class="flex items-center gap-1.5">
      <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
      <span class="font-bold uppercase tracking-wide text-xs">Live Now</span>
    </span>
    <span x-text="title" class="hidden sm:inline"></span>
    <span class="hidden sm:inline text-red-200">·</span>
    <span class="hidden sm:inline text-red-100 text-xs">Tap to watch →</span>
  </a>
</div>

<!-- Navbar -->
<nav class="bg-navy shadow-lg sticky top-0 z-40" x-data="{ open: false }">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-16">

      <!-- Logo -->
      <a href="/" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gold flex items-center justify-center flex-shrink-0">
          <i class="ti ti-broadcast text-navy"></i>
        </div>
        <div>
          <p class="text-white font-bold text-sm leading-none">Pastor Robert Talemwa</p>
          <p class="text-gold text-xs mt-0.5">Healing to the Nations</p>
        </div>
      </a>

      <!-- Desktop nav -->
      <div class="hidden md:flex items-center gap-1">
        <a href="/" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Home</a>
        <a href="/sermons" class="nav-link <?= $currentPage === 'sermons' ? 'active' : '' ?>">Sermons</a>
        <a href="/radio" class="nav-link <?= $currentPage === 'radio' ? 'active' : '' ?>">Radio</a>
        <a href="/events" class="nav-link <?= $currentPage === 'events' ? 'active' : '' ?>">Events</a>
        <a href="/about" class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
        <a href="/give" class="ml-2 bg-gold hover:bg-gold-light text-navy font-semibold px-4 py-2 rounded-lg text-sm transition">
          Give Online
        </a>
      </div>

      <!-- Mobile toggle -->
      <button @click="open = !open" class="md:hidden text-white p-2">
        <i :class="open ? 'ti ti-x' : 'ti ti-menu-2'" class="text-xl"></i>
      </button>
    </div>

    <!-- Mobile menu -->
    <div x-show="open" x-cloak @click.away="open=false"
         class="md:hidden border-t border-white/10 py-3 space-y-1">
      <a href="/" class="mobile-link">Home</a>
      <a href="/sermons" class="mobile-link">Sermons</a>
      <a href="/radio" class="mobile-link">Radio</a>
      <a href="/events" class="mobile-link">Events</a>
      <a href="/about" class="mobile-link">About</a>
      <a href="/give" class="mobile-link text-gold font-semibold">Give Online</a>
      <a href="/prayer" class="mobile-link">Prayer Request</a>
      <a href="/contact" class="mobile-link">Contact</a>
    </div>
  </div>
</nav>

<style>
  .nav-link { color:#9ca3af; padding:0.4rem 0.75rem; border-radius:0.5rem; font-size:0.875rem; transition:all 0.15s; text-decoration:none; }
  .nav-link:hover, .nav-link.active { color:#fff; background:rgba(255,255,255,0.08); }
  .mobile-link { display:block; color:#9ca3af; padding:0.6rem 0.75rem; border-radius:0.5rem; font-size:0.9rem; text-decoration:none; }
  .mobile-link:hover { color:#fff; background:rgba(255,255,255,0.08); }
</style>

<?php
$pageTitle  = 'Healing to the Nations';
$metaDesc   = 'Pastor Robert Talemwa — Missionary preacher from Kampala, Uganda. Watch live streams, listen to sermons, and join our global ministry.';
$currentPage = 'home';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/nav.php';
?>

<!-- Hero Section -->
<section class="hero-gradient text-white py-20 sm:py-28 relative overflow-hidden">
  <!-- Background pattern -->
  <div class="absolute inset-0 opacity-5">
    <div class="absolute inset-0" style="background-image:repeating-linear-gradient(45deg,#fff 0,#fff 1px,transparent 0,transparent 50%);background-size:30px 30px;"></div>
  </div>

  <div class="max-w-6xl mx-auto px-4 sm:px-6 relative">
    <div class="max-w-3xl">
      <div class="inline-flex items-center gap-2 bg-gold/20 border border-gold/30 text-gold rounded-full px-4 py-1.5 text-sm font-medium mb-6">
        <i class="ti ti-broadcast"></i> Ministry of Healing & Restoration
      </div>
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
        Healing to<br>
        <span class="text-gold">the Nations</span>
      </h1>
      <p class="text-lg text-gray-300 leading-relaxed mb-8 max-w-xl">
        Pastor Robert Talemwa — missionary preacher from Kampala, Uganda, taking the gospel
        and healing to nations through the power of the Holy Spirit.
      </p>
      <div class="flex flex-wrap gap-3">
        <a href="/live"
           class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3.5 rounded-xl transition shadow-lg shadow-red-900/30">
          <i class="ti ti-broadcast"></i> Watch Live
        </a>
        <a href="/radio"
           class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white font-semibold px-6 py-3.5 rounded-xl transition border border-white/20">
          <i class="ti ti-radio"></i> Listen to Radio
        </a>
        <a href="/sermons"
           class="inline-flex items-center gap-2 bg-gold hover:bg-gold-light text-navy font-semibold px-6 py-3.5 rounded-xl transition">
          <i class="ti ti-books"></i> Sermon Archive
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Featured Sermon -->
<section class="py-16 bg-gray-50" x-data="featuredSermon()" x-init="init()">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-2xl font-bold text-gray-900">Latest Sermon</h2>
      <a href="/sermons" class="text-gold hover:underline text-sm flex items-center gap-1">All sermons <i class="ti ti-arrow-right"></i></a>
    </div>

    <div x-show="sermon" x-cloak class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden md:flex">
      <a :href="`/sermons/${sermon?.id}`" class="md:w-80 flex-shrink-0 block">
        <img :src="sermon?.thumbnail_url || 'https://placehold.co/320x200/0A1628/C9A84C?text=📖'"
             class="w-full h-56 md:h-full object-cover" alt="">
      </a>
      <div class="p-8 flex flex-col justify-center">
        <span class="text-xs font-semibold text-gold uppercase tracking-wide" x-text="sermon?.series || 'Sermon'"></span>
        <h3 class="text-2xl font-bold text-gray-900 mt-2 mb-3" x-text="sermon?.title"></h3>
        <p class="text-gray-500 text-sm mb-2 flex items-center gap-2">
          <i class="ti ti-book"></i>
          <span x-text="sermon?.scripture || ''"></span>
        </p>
        <p class="text-gray-600 leading-relaxed line-clamp-2 mb-6" x-text="sermon?.description || ''"></p>
        <div class="flex gap-3">
          <a :href="`/sermons/${sermon?.id}`"
             class="inline-flex items-center gap-2 bg-navy text-white px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-navy-700 transition">
            <i class="ti ti-player-play"></i> Listen Now
          </a>
          <a :href="`https://www.youtube.com/watch?v=${sermon?.youtube_url?.split('v=')[1]}`" target="_blank"
             x-show="sermon?.youtube_url"
             class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-5 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
            <i class="ti ti-brand-youtube text-red-500"></i> Watch on YouTube
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Pastor -->
<section class="py-16">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="grid md:grid-cols-2 gap-12 items-center">
      <div>
        <span class="text-gold text-sm font-semibold uppercase tracking-wide">About the Minister</span>
        <h2 class="text-3xl font-bold text-gray-900 mt-3 mb-5">Pastor Robert Talemwa</h2>
        <p class="text-gray-600 leading-relaxed mb-4">
          Pastor Robert Talemwa is a missionary preacher based in Kampala, Uganda, with a mandate to
          preach the gospel and take healing to nations. His ministry reaches thousands locally and
          in the diaspora across the UK, US, Canada, and Europe.
        </p>
        <p class="text-gray-600 leading-relaxed mb-6">
          Through powerful preaching, healing services, and media outreach, Pastor Talemwa
          has seen countless lives transformed by the power of the Holy Spirit.
        </p>
        <a href="/about" class="inline-flex items-center gap-2 text-gold hover:underline font-medium">
          Read his full story <i class="ti ti-arrow-right"></i>
        </a>
      </div>
      <div class="bg-navy rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-gold/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <blockquote class="text-xl font-medium leading-relaxed relative z-10 mb-6">
          "Preach the gospel and take healing to nations"
        </blockquote>
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-gold flex items-center justify-center font-bold text-navy">RT</div>
          <div>
            <p class="font-semibold">Pastor Robert Talemwa</p>
            <p class="text-gray-400 text-sm">Kampala, Uganda</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Upcoming Events -->
<section class="py-16 bg-gray-50" x-data="upcomingEvents()" x-init="init()">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between mb-8">
      <h2 class="text-2xl font-bold text-gray-900">Upcoming Events</h2>
      <a href="/events" class="text-gold hover:underline text-sm flex items-center gap-1">View all <i class="ti ti-arrow-right"></i></a>
    </div>

    <div x-show="events.length === 0 && !loading" x-cloak class="text-center py-10 text-gray-400">
      No upcoming events scheduled. Check back soon.
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
      <template x-for="e in events.slice(0,3)" :key="e.id">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
          <div class="flex items-center gap-2 mb-3">
            <span :class="e.is_online ? 'bg-blue-100 text-blue-700' : 'bg-gold/10 text-gold-dark'"
                  class="text-xs font-medium px-2.5 py-1 rounded-full"
                  x-text="e.is_online ? 'Online Event' : 'In Person'"></span>
          </div>
          <h3 class="font-bold text-gray-900 text-lg mb-2" x-text="e.title"></h3>
          <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <i class="ti ti-calendar text-gold"></i>
            <span x-text="formatDate(e.event_date)"></span>
          </div>
          <div class="flex items-center gap-2 text-sm text-gray-500 mb-4" x-show="e.location">
            <i class="ti ti-map-pin text-gold"></i>
            <span x-text="e.location"></span>
          </div>
          <p class="text-gray-500 text-sm line-clamp-2" x-text="e.description || ''"></p>
        </div>
      </template>
    </div>
  </div>
</section>

<!-- App Download CTA -->
<section class="py-16 bg-navy text-white">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-2xl mx-auto">
      <h2 class="text-3xl font-bold mb-4">Get the Talemwa App</h2>
      <p class="text-gray-400 mb-8">
        Listen to sermons, watch live services, tune into the radio, and give — all from your phone.
        Available for Android and iOS.
      </p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="#" class="flex items-center gap-3 bg-white/5 hover:bg-white/10 border border-white/20 rounded-xl px-6 py-4 transition">
          <i class="ti ti-brand-google-play text-green-400 text-3xl"></i>
          <div class="text-left">
            <p class="text-xs text-gray-400">Download on</p>
            <p class="font-semibold">Google Play</p>
          </div>
        </a>
        <a href="#" class="flex items-center gap-3 bg-white/5 hover:bg-white/10 border border-white/20 rounded-xl px-6 py-4 transition">
          <i class="ti ti-brand-apple text-white text-3xl"></i>
          <div class="text-left">
            <p class="text-xs text-gray-400">Download on the</p>
            <p class="font-semibold">App Store</p>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Giving CTA -->
<section class="py-16">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gold/10 mb-6">
      <i class="ti ti-heart text-gold text-3xl"></i>
    </div>
    <h2 class="text-3xl font-bold text-gray-900 mb-4">Partner With This Ministry</h2>
    <p class="text-gray-600 mb-8 max-w-xl mx-auto">
      Your giving supports the preaching of the gospel, healing crusades, and media outreach
      that reaches nations. Every seed planted matters.
    </p>
    <p class="text-navy font-semibold italic mb-8">"Give, and it will be given to you." — Luke 6:38</p>
    <a href="/give"
       class="inline-flex items-center gap-2 bg-gold hover:bg-gold-light text-navy font-bold px-8 py-4 rounded-xl transition text-lg shadow-lg shadow-gold/20">
      <i class="ti ti-coin"></i> Give Online
    </a>
  </div>
</section>

<?php include __DIR__ . '/../partials/radio-bar.php'; ?>
<?php include __DIR__ . '/../partials/footer.php'; ?>

<script>
function featuredSermon() {
  return {
    sermon: null,
    async init() {
      try {
        const data = await apiFetch('/api/sermons?page=1');
        this.sermon = data.items?.[0] || null;
      } catch {}
    }
  }
}

function upcomingEvents() {
  return {
    events:  [],
    loading: true,
    async init() {
      try { this.events = await apiFetch('/api/events'); }
      catch {} finally { this.loading = false; }
    }
  }
}
</script>

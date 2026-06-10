<?php
$pageTitle   = 'Watch Live';
$metaDesc    = 'Watch Pastor Robert Talemwa live on Sunday. Join the live service from anywhere in the world.';
$currentPage = 'live';
include '../partials/head.php';
include '../partials/nav.php';
?>

<main x-data="livePage()" x-init="init()" class="flex-1">

  <!-- Live section -->
  <div x-show="isLive" x-cloak>
    <div class="bg-gray-900 w-full aspect-video max-h-[70vh]">
      <iframe :src="`https://www.youtube.com/embed/${youtubeId}?autoplay=1`"
              class="w-full h-full" frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen></iframe>
    </div>
    <div class="bg-navy text-white py-5">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="flex items-center gap-1.5 bg-red-600 text-white text-xs font-bold px-3 py-1 rounded-full">
            <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span> LIVE
          </span>
          <h1 class="text-white font-bold text-lg" x-text="liveTitle"></h1>
        </div>
        <div class="flex gap-3">
          <a :href="`https://wa.me/?text=${encodeURIComponent('Join us live! ' + liveTitle + ' https://roberttalemwa.online/live')}`"
             target="_blank"
             class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm transition">
            <i class="ti ti-brand-whatsapp"></i> Share
          </a>
          <a href="/radio" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-4 py-2 rounded-xl text-sm transition">
            <i class="ti ti-radio"></i> Audio Only
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Not live -->
  <div x-show="!isLive && !loading" x-cloak>
    <div class="bg-navy py-20 text-center text-white">
      <div class="max-w-2xl mx-auto px-4">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/5 mb-6">
          <i class="ti ti-broadcast text-gray-500 text-4xl"></i>
        </div>
        <h1 class="text-3xl font-bold mb-4">No Live Stream Right Now</h1>
        <p class="text-gray-400 mb-8">
          Join us live every Sunday at 10:00 AM (EAT) for our Sunday service.
          Follow our social media for announcements when we go live.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <a href="/radio"
             class="inline-flex items-center gap-2 bg-gold text-navy font-semibold px-6 py-3 rounded-xl transition hover:bg-gold-light">
            <i class="ti ti-radio"></i> Listen to Radio Instead
          </a>
          <a href="/sermons"
             class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl transition">
            <i class="ti ti-books"></i> Watch Past Sermons
          </a>
        </div>
      </div>
    </div>

    <!-- Schedule info -->
    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-12">
      <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">Service Schedule</h2>
      <div class="space-y-3">
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
          <div class="flex items-center gap-3">
            <i class="ti ti-sun text-gold text-xl"></i>
            <div>
              <p class="font-semibold text-gray-900">Sunday Morning Service</p>
              <p class="text-sm text-gray-500">Every Sunday</p>
            </div>
          </div>
          <span class="text-gold font-semibold">10:00 AM EAT</span>
        </div>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
          <div class="flex items-center gap-3">
            <i class="ti ti-book text-gold text-xl"></i>
            <div>
              <p class="font-semibold text-gray-900">Midweek Bible Study</p>
              <p class="text-sm text-gray-500">Every Wednesday</p>
            </div>
          </div>
          <span class="text-gold font-semibold">7:00 PM EAT</span>
        </div>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
          <div class="flex items-center gap-3">
            <i class="ti ti-flame text-gold text-xl"></i>
            <div>
              <p class="font-semibold text-gray-900">Friday Prayer Night</p>
              <p class="text-sm text-gray-500">Every Friday</p>
            </div>
          </div>
          <span class="text-gold font-semibold">8:00 PM EAT</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent sermons -->
  <section class="py-12 bg-gray-50" x-data="recentSermons()" x-init="init()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-900">Previous Services</h2>
        <a href="/sermons" class="text-gold hover:underline text-sm">View all</a>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="s in sermons" :key="s.id">
          <a :href="`/sermons/${s.id}`" class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden">
            <img :src="s.thumbnail_url || 'https://placehold.co/400x200/0A1628/C9A84C?text=📖'"
                 class="w-full h-36 object-cover" alt="">
            <div class="p-4">
              <p class="font-semibold text-gray-900 line-clamp-2 text-sm" x-text="s.title"></p>
              <p class="text-xs text-gray-400 mt-1" x-text="s.speaker"></p>
            </div>
          </a>
        </template>
      </div>
    </div>
  </section>

</main>

<?php include '../partials/radio-bar.php'; ?>
<?php include '../partials/footer.php'; ?>

<script>
function livePage() {
  return {
    isLive:    false,
    youtubeId: '',
    liveTitle: '',
    loading:   true,
    async init() {
      try {
        const data     = await apiFetch('/api/live');
        this.isLive    = data.is_live;
        this.youtubeId = data.youtube_id || '';
        this.liveTitle = data.title || 'Sunday Service';
      } catch {} finally { this.loading = false; }
      setInterval(async () => {
        try {
          const data = await apiFetch('/api/live');
          this.isLive    = data.is_live;
          this.youtubeId = data.youtube_id || '';
          this.liveTitle = data.title || '';
        } catch {}
      }, 60000);
    }
  }
}

function recentSermons() {
  return {
    sermons: [],
    async init() {
      try {
        const data  = await apiFetch('/api/sermons?page=1');
        this.sermons = data.items?.slice(0,6) || [];
      } catch {}
    }
  }
}
</script>

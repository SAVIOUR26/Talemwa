<?php
$pageTitle   = 'Events';
$metaDesc    = 'Upcoming ministry events, services, and crusades with Pastor Robert Talemwa.';
$currentPage = 'events';
include '../partials/head.php';
include '../partials/nav.php';
?>

<section class="bg-navy py-12 text-white text-center">
  <div class="max-w-4xl mx-auto px-4">
    <h1 class="text-3xl sm:text-4xl font-bold mb-3">Upcoming Events</h1>
    <p class="text-gray-400">Join us for services, crusades, and special programmes</p>
  </div>
</section>

<main class="max-w-4xl mx-auto px-4 sm:px-6 py-12" x-data="eventsList()" x-init="init()">

  <div x-show="loading" class="flex justify-center py-20">
    <i class="ti ti-loader-2 animate-spin text-3xl text-gold"></i>
  </div>

  <div x-show="!loading && events.length === 0" x-cloak class="text-center py-20">
    <i class="ti ti-calendar-off text-6xl text-gray-200 block mb-4"></i>
    <p class="text-gray-500 text-lg">No upcoming events scheduled.</p>
    <p class="text-gray-400 text-sm mt-2">Follow us on social media for the latest announcements.</p>
  </div>

  <div class="space-y-5">
    <template x-for="e in events" :key="e.id">
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden sm:flex hover:shadow-md transition">

        <!-- Date block -->
        <div class="bg-navy sm:w-28 flex-shrink-0 flex sm:flex-col items-center justify-center p-5 sm:p-6 gap-4 sm:gap-0">
          <div class="text-center">
            <p class="text-gold text-3xl font-bold leading-none"
               x-text="new Date(e.event_date).getDate()"></p>
            <p class="text-gray-400 text-sm mt-1"
               x-text="new Date(e.event_date).toLocaleDateString('en-GB',{month:'short'})"></p>
            <p class="text-gray-500 text-xs"
               x-text="new Date(e.event_date).getFullYear()"></p>
          </div>
        </div>

        <!-- Details -->
        <div class="flex-1 p-6">
          <div class="flex items-start justify-between gap-3 mb-3">
            <h2 class="text-lg font-bold text-gray-900" x-text="e.title"></h2>
            <span :class="e.is_online ? 'bg-blue-100 text-blue-700' : 'bg-gold/10 text-gold-dark'"
                  class="text-xs font-medium px-2.5 py-1 rounded-full flex-shrink-0"
                  x-text="e.is_online ? 'Online' : 'In Person'"></span>
          </div>

          <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-3">
            <span x-show="e.event_time" class="flex items-center gap-1.5">
              <i class="ti ti-clock text-gold"></i> <span x-text="e.event_time"></span>
            </span>
            <span x-show="e.location" class="flex items-center gap-1.5">
              <i class="ti ti-map-pin text-gold"></i> <span x-text="e.location"></span>
            </span>
          </div>

          <p class="text-gray-600 text-sm line-clamp-2 mb-4" x-text="e.description || ''"></p>

          <div class="flex gap-3">
            <a x-show="e.stream_url" :href="e.stream_url" target="_blank"
               class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm transition">
              <i class="ti ti-broadcast"></i> Watch Online
            </a>
            <a :href="`https://wa.me/?text=${encodeURIComponent(e.title + ' — ' + formatDate(e.event_date) + ' https://roberttalemwa.online/events')}`"
               target="_blank"
               class="inline-flex items-center gap-2 bg-green-50 hover:bg-green-100 text-green-700 px-4 py-2 rounded-xl text-sm transition">
              <i class="ti ti-brand-whatsapp"></i> Share
            </a>
          </div>
        </div>
      </div>
    </template>
  </div>

</main>

<?php include '../partials/radio-bar.php'; ?>
<?php include '../partials/footer.php'; ?>

<script>
function eventsList() {
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

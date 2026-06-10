<footer class="bg-navy mt-auto">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

      <!-- Brand -->
      <div class="lg:col-span-2">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-lg bg-gold flex items-center justify-center">
            <i class="ti ti-broadcast text-navy text-lg"></i>
          </div>
          <div>
            <p class="text-white font-bold">Pastor Robert Talemwa</p>
            <p class="text-gold text-xs">Healing to the Nations</p>
          </div>
        </div>
        <p class="text-gray-400 text-sm leading-relaxed max-w-xs">
          Missionary preacher based in Kampala, Uganda. Taking the gospel and healing to nations through preaching, media, and prayer.
        </p>
        <!-- Social -->
        <div class="flex items-center gap-3 mt-5">
          <a href="https://www.youtube.com/@pastortalemwarobert4160" target="_blank" rel="noopener"
             class="w-9 h-9 rounded-lg bg-white/5 hover:bg-red-600 flex items-center justify-center transition text-gray-400 hover:text-white">
            <i class="ti ti-brand-youtube text-lg"></i>
          </a>
          <a href="https://facebook.com" target="_blank" rel="noopener"
             class="w-9 h-9 rounded-lg bg-white/5 hover:bg-blue-600 flex items-center justify-center transition text-gray-400 hover:text-white">
            <i class="ti ti-brand-facebook text-lg"></i>
          </a>
          <a href="https://tiktok.com" target="_blank" rel="noopener"
             class="w-9 h-9 rounded-lg bg-white/5 hover:bg-white/20 flex items-center justify-center transition text-gray-400 hover:text-white">
            <i class="ti ti-brand-tiktok text-lg"></i>
          </a>
        </div>
      </div>

      <!-- Quick Links -->
      <div>
        <p class="text-white font-semibold text-sm mb-4">Quick Links</p>
        <div class="space-y-2">
          <a href="/sermons" class="block text-gray-400 hover:text-gold text-sm transition">Sermon Archive</a>
          <a href="/live"    class="block text-gray-400 hover:text-gold text-sm transition">Watch Live</a>
          <a href="/radio"   class="block text-gray-400 hover:text-gold text-sm transition">Online Radio</a>
          <a href="/events"  class="block text-gray-400 hover:text-gold text-sm transition">Events</a>
          <a href="/give"    class="block text-gray-400 hover:text-gold text-sm transition">Give Online</a>
          <a href="/prayer"  class="block text-gray-400 hover:text-gold text-sm transition">Prayer Request</a>
        </div>
      </div>

      <!-- Get the App -->
      <div>
        <p class="text-white font-semibold text-sm mb-4">Get the App</p>
        <p class="text-gray-400 text-sm mb-4">Listen to sermons, catch live streams, and give — all from your phone.</p>
        <div class="space-y-2">
          <a href="#" class="flex items-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl px-3 py-2.5 transition">
            <i class="ti ti-brand-google-play text-green-400 text-xl"></i>
            <div>
              <p class="text-white text-xs font-medium">Google Play</p>
              <p class="text-gray-400 text-xs">Android</p>
            </div>
          </a>
          <a href="#" class="flex items-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl px-3 py-2.5 transition">
            <i class="ti ti-brand-apple text-white text-xl"></i>
            <div>
              <p class="text-white text-xs font-medium">App Store</p>
              <p class="text-gray-400 text-xs">iPhone & iPad</p>
            </div>
          </a>
        </div>
      </div>

    </div>

    <div class="border-t border-white/10 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
      <p class="text-gray-500 text-xs">
        © <?= date('Y') ?> Pastor Robert Talemwa · Built by
        <a href="https://thirdsan.com" target="_blank" class="hover:text-gold transition">Thirdsan Enterprises Ltd</a>
      </p>
      <div class="flex items-center gap-4 text-xs text-gray-500">
        <a href="/contact" class="hover:text-gold transition">Contact</a>
        <a href="/about" class="hover:text-gold transition">About</a>
      </div>
    </div>
  </div>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="/assets/js/site.js"></script>
</body>
</html>

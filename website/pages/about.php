<?php
$pageTitle   = 'About Pastor Robert Talemwa';
$metaDesc    = 'Learn about Pastor Robert Talemwa — missionary preacher from Kampala, Uganda with a mandate to preach the gospel and take healing to nations.';
$currentPage = 'about';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/nav.php';
?>

<!-- Hero -->
<section class="hero-gradient text-white py-20">
  <div class="max-w-5xl mx-auto px-4 sm:px-6">
    <div class="grid md:grid-cols-2 gap-12 items-center">
      <div>
        <span class="text-gold text-sm font-semibold uppercase tracking-wider">About the Minister</span>
        <h1 class="text-4xl sm:text-5xl font-extrabold mt-3 mb-5 leading-tight">
          Pastor Robert<br>Talemwa
        </h1>
        <p class="text-gray-300 text-lg leading-relaxed mb-6">
          Missionary preacher, teacher, and healing revivalist based in Kampala, Uganda.
        </p>
        <blockquote class="border-l-4 border-gold pl-5 text-gray-300 italic text-lg">
          "Preach the gospel and take healing to nations"
        </blockquote>
      </div>
      <div class="hidden md:flex justify-center">
        <div class="w-72 h-80 rounded-3xl bg-white/10 flex items-center justify-center">
          <div class="text-center text-white/30">
            <i class="ti ti-user-circle text-8xl block mb-2"></i>
            <p class="text-sm">Photo coming soon</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mission -->
<section class="py-16">
  <div class="max-w-4xl mx-auto px-4 sm:px-6">
    <div class="grid md:grid-cols-3 gap-8 mb-12">
      <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-navy mx-auto flex items-center justify-center mb-4">
          <i class="ti ti-broadcast text-gold text-2xl"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">Gospel Preaching</h3>
        <p class="text-gray-500 text-sm">Proclaiming the saving power of Jesus Christ through powerful preaching and teaching</p>
      </div>
      <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-navy mx-auto flex items-center justify-center mb-4">
          <i class="ti ti-heart-handshake text-gold text-2xl"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">Divine Healing</h3>
        <p class="text-gray-500 text-sm">Ministering the healing power of God to the sick, broken, and afflicted</p>
      </div>
      <div class="text-center">
        <div class="w-14 h-14 rounded-2xl bg-navy mx-auto flex items-center justify-center mb-4">
          <i class="ti ti-world text-gold text-2xl"></i>
        </div>
        <h3 class="font-bold text-gray-900 mb-2">Nations Mission</h3>
        <p class="text-gray-500 text-sm">Taking the gospel beyond Uganda to reach the diaspora and nations globally</p>
      </div>
    </div>

    <div class="prose prose-lg max-w-none text-gray-600 space-y-4">
      <p>
        Pastor Robert Talemwa is a Spirit-filled minister of the gospel, called to preach the Word
        and demonstrate the healing power of Jesus Christ. Based at his church in Kampala, Uganda,
        Pastor Talemwa's ministry has grown from local services to an international reach,
        with congregants and followers across the United Kingdom, United States, Canada, and Europe.
      </p>
      <p>
        His ministry is marked by a deep conviction in the authority of Scripture and the present-day
        ministry of the Holy Spirit. Through crusades, healing meetings, and media outreach, thousands
        have encountered God's power and been saved, healed, and delivered.
      </p>
      <p>
        With over 11,000 followers on Facebook and an active YouTube channel, Pastor Talemwa
        has embraced digital media as a vehicle for gospel proclamation — taking the Sunday service
        into living rooms, hospitals, and phones across the world.
      </p>
    </div>
  </div>
</section>

<!-- Social + Connect -->
<section class="py-16 bg-gray-50">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center">
    <h2 class="text-2xl font-bold text-gray-900 mb-8">Connect With the Ministry</h2>
    <div class="flex flex-wrap gap-4 justify-center mb-10">
      <a href="https://www.youtube.com/@pastortalemwarobert4160" target="_blank"
         class="flex items-center gap-3 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-medium transition">
        <i class="ti ti-brand-youtube text-xl"></i> YouTube Channel
      </a>
      <a href="https://facebook.com" target="_blank"
         class="flex items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition">
        <i class="ti ti-brand-facebook text-xl"></i> Facebook Page
      </a>
      <a href="https://tiktok.com" target="_blank"
         class="flex items-center gap-3 bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-xl font-medium transition">
        <i class="ti ti-brand-tiktok text-xl"></i> TikTok
      </a>
    </div>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
      <a href="/prayer"
         class="inline-flex items-center gap-2 bg-navy text-white font-semibold px-6 py-3.5 rounded-xl hover:bg-navy-700 transition">
        <i class="ti ti-pray"></i> Submit a Prayer Request
      </a>
      <a href="/give"
         class="inline-flex items-center gap-2 bg-gold hover:bg-gold-light text-navy font-semibold px-6 py-3.5 rounded-xl transition">
        <i class="ti ti-coin"></i> Support This Ministry
      </a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../partials/radio-bar.php'; ?>
<?php include __DIR__ . '/../partials/footer.php'; ?>

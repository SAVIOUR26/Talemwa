<?php
$pageTitle   = 'Thank You for Giving';
$currentPage = '';
include '../partials/head.php';
include '../partials/nav.php';
?>

<main class="flex-1 flex items-center justify-center py-24 px-4">
  <div class="max-w-lg text-center">
    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-100 mb-8">
      <i class="ti ti-heart-filled text-green-600 text-5xl"></i>
    </div>
    <h1 class="text-3xl font-bold text-gray-900 mb-4">Thank You for Giving!</h1>
    <p class="text-gray-600 text-lg mb-6">
      Your gift has been received. May God bless you abundantly as you sow into this ministry.
    </p>
    <p class="text-navy font-semibold italic mb-8">
      "Give, and it will be given to you. A good measure, pressed down, shaken together
      and running over, will be poured into your lap." — Luke 6:38
    </p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
      <a href="/sermons" class="inline-flex items-center gap-2 bg-navy text-white px-6 py-3 rounded-xl font-medium hover:bg-navy-700 transition">
        <i class="ti ti-books"></i> Listen to a Sermon
      </a>
      <a href="/" class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-6 py-3 rounded-xl font-medium hover:bg-gray-50 transition">
        <i class="ti ti-home"></i> Back to Home
      </a>
    </div>
  </div>
</main>

<?php include '../partials/footer.php'; ?>

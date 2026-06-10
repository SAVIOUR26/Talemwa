<?php
$pageTitle   = 'Page Not Found';
$currentPage = '';
include '../partials/head.php';
include '../partials/nav.php';
?>

<main class="flex-1 flex items-center justify-center py-24 px-4">
  <div class="text-center">
    <p class="text-8xl font-extrabold text-gold mb-4">404</p>
    <h1 class="text-2xl font-bold text-gray-900 mb-3">Page Not Found</h1>
    <p class="text-gray-500 mb-8">The page you're looking for doesn't exist or has been moved.</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="/" class="inline-flex items-center gap-2 bg-navy text-white px-6 py-3 rounded-xl font-medium hover:bg-navy-700 transition">
        <i class="ti ti-home"></i> Go Home
      </a>
      <a href="/sermons" class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-6 py-3 rounded-xl font-medium hover:bg-gray-50 transition">
        <i class="ti ti-books"></i> Browse Sermons
      </a>
    </div>
  </div>
</main>

<?php include '../partials/footer.php'; ?>

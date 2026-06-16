<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Healing to the Nations') ?> — Pastor Robert Talemwa</title>
<meta name="description" content="<?= htmlspecialchars($metaDesc ?? 'Pastor Robert Talemwa — Missionary preacher. Preach the gospel and take healing to nations. Live streams, sermons, and online giving.') ?>">
<meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? 'Pastor Robert Talemwa') ?>">
<meta property="og:description" content="<?= htmlspecialchars($metaDesc ?? 'Healing to the Nations') ?>">
<meta property="og:image" content="https://roberttalemwa.online/assets/img/og-image.jpg">
<meta property="og:url" content="https://roberttalemwa.online<?= $_SERVER['REQUEST_URI'] ?>">
<meta name="theme-color" content="#0A1628">
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
<link rel="stylesheet" href="/assets/css/app.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
  [x-cloak] { display:none !important; }
  body { font-family:'Inter',system-ui,sans-serif; }
  .hero-gradient { background: linear-gradient(135deg, #0A1628 0%, #112448 50%, #0A1628 100%); }
  .gold-gradient { background: linear-gradient(135deg, #C9A84C, #d4b86a); }
  .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .line-clamp-3 { display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }
</style>
</head>
<body class="bg-white text-gray-900 min-h-screen flex flex-col" x-data>

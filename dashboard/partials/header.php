<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Dashboard' ?> — Talemwa Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.14.0/tabler-icons.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        navy: { DEFAULT: '#0A1628', 700: '#112448' },
        gold: { DEFAULT: '#C9A84C', light: '#d4b86a', dark: '#a8892f' },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    }
  }
}
</script>
<style>
  [x-cloak] { display:none !important; }
  body { font-family:'Inter',system-ui,sans-serif; }
</style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen" x-data>

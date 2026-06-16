<?php
/**
 * Talemwa Ministry Website — roberttalemwa.online
 * Simple PHP router — no framework, no build step.
 */

// Bypass LiteSpeed full-page cache so PHP always executes
header('X-LiteSpeed-Cache-Control: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

$route = trim($_GET['route'] ?? '', '/');
if ($route === '') $route = 'home';

// Map routes to page files
$pages = [
    'home'          => 'home',
    'sermons'       => 'sermons',
    'live'          => 'live',
    'radio'         => 'radio',
    'events'        => 'events',
    'give'          => 'give',
    'prayer'        => 'prayer',
    'about'         => 'about',
    'contact'       => 'contact',
];

// Handle /sermons/{id}
if (preg_match('#^sermons/(\d+)$#', $route, $m)) {
    $_GET['sermon_id'] = $m[1];
    $route = 'sermon';
    $pages['sermon'] = 'sermon';
}

// Give thanks redirect
if ($route === 'give/thanks') {
    $pages['give/thanks'] = 'give-thanks';
    $pages['give_thanks'] = 'give-thanks';
}

$page = $pages[$route] ?? null;

if ($page && file_exists(__DIR__ . "/pages/{$page}.php")) {
    require __DIR__ . "/pages/{$page}.php";
} else {
    http_response_code(404);
    require __DIR__ . '/pages/404.php';
}

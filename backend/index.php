<?php
/**
 * Ministry Platform API
 * Thirdsan Enterprises — Simple PHP + SQLite backend
 * No framework. No build step. Just routes, logic, responses.
 */

define('ROOT', __DIR__);
define('DB_PATH', ROOT . '/database/ministry.db');
define('UPLOAD_PATH', ROOT . '/uploads/sermons/');
define('JWT_SECRET', $_ENV['JWT_SECRET'] ?? 'change-this-in-production');
define('FCM_SERVER_KEY', $_ENV['FCM_SERVER_KEY'] ?? '');
define('STREAM_URL', $_ENV['STREAM_URL'] ?? 'https://radio.yourdomain.com/stream');

require_once ROOT . '/core/Database.php';
require_once ROOT . '/core/Router.php';
require_once ROOT . '/core/Auth.php';
require_once ROOT . '/core/Response.php';
require_once ROOT . '/core/Notify.php';

require_once ROOT . '/controllers/SermonController.php';
require_once ROOT . '/controllers/RadioController.php';
require_once ROOT . '/controllers/LiveController.php';
require_once ROOT . '/controllers/EventController.php';
require_once ROOT . '/controllers/GivingController.php';
require_once ROOT . '/controllers/PrayerController.php';
require_once ROOT . '/controllers/AuthController.php';
require_once ROOT . '/controllers/DeviceController.php';

// CORS — allow mobile app and website
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$router = new Router();

// ── Public routes ──────────────────────────────────────────────
$router->get('/api/sermons',           [SermonController::class,  'index']);
$router->get('/api/sermons/{id}',      [SermonController::class,  'show']);
$router->get('/api/sermons/series',    [SermonController::class,  'series']);

$router->get('/api/radio',             [RadioController::class,   'nowPlaying']);
$router->get('/api/radio/schedule',    [RadioController::class,   'schedule']);

$router->get('/api/live',              [LiveController::class,    'status']);

$router->get('/api/events',            [EventController::class,   'index']);
$router->get('/api/events/{id}',       [EventController::class,   'show']);

$router->post('/api/give/initiate',    [GivingController::class,  'initiate']);
$router->post('/api/give/webhook',     [GivingController::class,  'webhook']);

$router->post('/api/prayer',           [PrayerController::class,  'store']);

$router->post('/api/device/register',  [DeviceController::class,  'register']);

// ── Auth routes ────────────────────────────────────────────────
$router->post('/api/auth/login',       [AuthController::class,    'login']);

// ── Admin routes (JWT protected) ───────────────────────────────
$router->post('/api/admin/sermons',         [SermonController::class,  'store'],   true);
$router->put('/api/admin/sermons/{id}',     [SermonController::class,  'update'],  true);
$router->delete('/api/admin/sermons/{id}',  [SermonController::class,  'destroy'], true);

$router->post('/api/admin/live',            [LiveController::class,    'update'],  true);
$router->post('/api/admin/events',          [EventController::class,   'store'],   true);
$router->get('/api/admin/prayers',          [PrayerController::class,  'index'],   true);
$router->get('/api/admin/givings',          [GivingController::class,  'index'],   true);
$router->post('/api/admin/notify',          [DeviceController::class,  'broadcast'], true);

$router->dispatch();

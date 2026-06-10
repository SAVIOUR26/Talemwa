<?php
/**
 * Ministry Platform API
 * Thirdsan Enterprises — Simple PHP + SQLite backend
 * No framework. No build step. Just routes, logic, responses.
 */

define('ROOT', __DIR__);

// Load .env file into $_ENV if it exists
$envFile = ROOT . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$key, $val] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($val);
        putenv(trim($key) . '=' . trim($val));
    }
}

define('DB_PATH', ROOT . '/database/ministry.db');
define('UPLOAD_PATH', ROOT . '/uploads/sermons/');
define('JWT_SECRET',              $_ENV['JWT_SECRET']              ?? 'change-this-in-production');
define('FCM_SERVER_KEY',          $_ENV['FCM_SERVER_KEY']          ?? '');
define('STREAM_URL',              $_ENV['STREAM_URL']              ?? 'https://radio.roberttalemwa.online/stream');
define('AZURACAST_URL',           $_ENV['AZURACAST_URL']           ?? '');
define('AZURACAST_API_KEY',       $_ENV['AZURACAST_API_KEY']       ?? '');
define('AZURACAST_STATION',       $_ENV['AZURACAST_STATION']       ?? '1');
define('FLUTTERWAVE_SECRET_KEY',  $_ENV['FLUTTERWAVE_SECRET_KEY']  ?? '');
define('PAYPAL_CLIENT_ID',        $_ENV['PAYPAL_CLIENT_ID']        ?? '');
define('PAYPAL_CLIENT_SECRET',    $_ENV['PAYPAL_CLIENT_SECRET']    ?? '');

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
require_once ROOT . '/controllers/CampaignController.php';
require_once ROOT . '/controllers/StatsController.php';
require_once ROOT . '/controllers/NotificationController.php';
require_once ROOT . '/controllers/AdminController.php';

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

$router->get('/api/campaigns',         [CampaignController::class, 'index']);

$router->post('/api/give/initiate',    [GivingController::class,  'initiate']);
$router->post('/api/give/webhook',     [GivingController::class,  'webhook']);

$router->post('/api/prayer',           [PrayerController::class,  'store']);

$router->post('/api/device/register',  [DeviceController::class,  'register']);
$router->post('/api/install/track',    [DeviceController::class,  'trackInstall']);

// ── Auth routes ────────────────────────────────────────────────
$router->post('/api/auth/login',       [AuthController::class,    'login']);

// ── Admin routes (JWT protected) ───────────────────────────────
$router->post('/api/admin/sermons',         [SermonController::class,  'store'],   true);
$router->put('/api/admin/sermons/{id}',     [SermonController::class,  'update'],  true);
$router->delete('/api/admin/sermons/{id}',  [SermonController::class,  'destroy'], true);

$router->post('/api/admin/live',                    [LiveController::class,         'update'],        true);

$router->post('/api/admin/events',                  [EventController::class,        'store'],         true);
$router->put('/api/admin/events/{id}',              [EventController::class,        'update'],        true);
$router->delete('/api/admin/events/{id}',           [EventController::class,        'destroy'],       true);

$router->get('/api/admin/prayers',                  [PrayerController::class,       'index'],         true);
$router->put('/api/admin/prayers/{id}',             [PrayerController::class,       'update'],        true);

$router->get('/api/admin/givings',                  [GivingController::class,       'index'],         true);
$router->get('/api/admin/givings/summary',          [GivingController::class,       'summary'],       true);

$router->get('/api/admin/campaigns',                [CampaignController::class,     'index'],         true);
$router->post('/api/admin/campaigns',               [CampaignController::class,     'store'],         true);
$router->put('/api/admin/campaigns/{id}',           [CampaignController::class,     'update'],        true);
$router->delete('/api/admin/campaigns/{id}',        [CampaignController::class,     'destroy'],       true);

$router->get('/api/admin/stats',                    [StatsController::class,        'overview'],      true);
$router->get('/api/admin/stats/installs',           [StatsController::class,        'installs'],      true);
$router->get('/api/admin/stats/sermons',            [StatsController::class,        'sermons'],       true);
$router->get('/api/admin/stats/giving',             [StatsController::class,        'giving'],        true);

$router->post('/api/admin/notify',                  [NotificationController::class, 'send'],          true);
$router->get('/api/admin/notify/history',           [NotificationController::class, 'history'],       true);

$router->post('/api/admin/radio/schedule',          [RadioController::class,        'updateSchedule'], true);
$router->put('/api/admin/radio/schedule/{id}',      [RadioController::class,        'updateSchedule'], true);

$router->get('/api/admin/admins',                   [AdminController::class,        'index'],         true);
$router->post('/api/admin/admins',                  [AdminController::class,        'store'],         true);
$router->put('/api/admin/admins/{id}',              [AdminController::class,        'update'],        true);
$router->delete('/api/admin/admins/{id}',           [AdminController::class,        'destroy'],       true);

$router->dispatch();

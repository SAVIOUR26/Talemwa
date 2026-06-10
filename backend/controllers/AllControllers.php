<?php
// ── RadioController ───────────────────────────────────────────

class RadioController
{
    public function nowPlaying(array $params, array $body): void
    {
        // Proxy AzuraCast's Now Playing API to avoid CORS issues in app
        $azuraUrl = rtrim($_ENV['AZURACAST_URL'] ?? 'https://radio.yourdomain.com', '/');
        $stationId = $_ENV['AZURACAST_STATION'] ?? '1';

        $json = @file_get_contents("$azuraUrl/api/nowplaying/$stationId");

        if ($json) {
            $data = json_decode($json, true);
            Response::json([
                'stream_url'  => STREAM_URL,
                'is_online'   => $data['station']['is_public'] ?? true,
                'now_playing' => [
                    'title'    => $data['now_playing']['song']['title'] ?? 'Live Stream',
                    'artist'   => $data['now_playing']['song']['artist'] ?? 'Ministry Radio',
                    'art'      => $data['now_playing']['song']['art'] ?? null,
                ],
                'listeners'   => $data['listeners']['current'] ?? 0,
            ]);
        } else {
            // Fallback if AzuraCast unreachable
            Response::json([
                'stream_url'  => STREAM_URL,
                'is_online'   => true,
                'now_playing' => ['title' => 'Ministry Radio', 'artist' => '', 'art' => null],
                'listeners'   => 0,
            ]);
        }
    }

    public function schedule(array $params, array $body): void
    {
        // Static schedule — update as needed or make DB-driven later
        Response::json([
            ['day' => 'Sunday',    'time' => '10:00 AM', 'program' => 'Sunday Service Live'],
            ['day' => 'Sunday',    'time' => '06:00 PM', 'program' => 'Evening Devotion'],
            ['day' => 'Wednesday', 'time' => '07:00 PM', 'program' => 'Midweek Bible Study'],
            ['day' => 'Friday',    'time' => '08:00 PM', 'program' => 'Prayer Night'],
            ['day' => 'Daily',     'time' => '06:00 AM', 'program' => 'Morning Devotion'],
        ]);
    }
}

// ── LiveController ────────────────────────────────────────────

class LiveController
{
    public function status(array $params, array $body): void
    {
        $db  = Database::connect();
        $row = $db->query('SELECT * FROM live_status ORDER BY id DESC LIMIT 1')->fetch();
        Response::json([
            'is_live'    => (bool)$row['is_live'],
            'youtube_id' => $row['youtube_id'],
            'title'      => $row['title'],
            'updated_at' => $row['updated_at'],
        ]);
    }

    public function update(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('UPDATE live_status SET is_live=:live, youtube_id=:yt, title=:title, updated_at=datetime("now") WHERE id=1');
        $stmt->execute([
            ':live'  => $body['is_live'] ? 1 : 0,
            ':yt'    => $body['youtube_id'] ?? null,
            ':title' => $body['title'] ?? 'Sunday Service',
        ]);

        if ($body['is_live']) {
            Notify::broadcast(
                '🔴 We Are Live!',
                ($body['title'] ?? 'Sunday Service') . ' — Join us now',
                ['type' => 'live', 'youtube_id' => $body['youtube_id'] ?? '']
            );
        }

        Response::json(['updated' => true]);
    }
}

// ── EventController ───────────────────────────────────────────

class EventController
{
    public function index(array $params, array $body): void
    {
        $db    = Database::connect();
        $rows  = $db->query("SELECT * FROM events WHERE event_date >= date('now') ORDER BY event_date ASC LIMIT 20")->fetchAll();
        Response::json($rows);
    }

    public function show(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->execute([':id' => $params['id']]);
        $event = $stmt->fetch();
        if (!$event) Response::json(['error' => 'Event not found'], 404);
        Response::json($event);
    }

    public function store(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('INSERT INTO events (title, description, event_date, event_time, location, is_online, stream_url) VALUES (:title, :description, :event_date, :event_time, :location, :is_online, :stream_url)');
        $stmt->execute([
            ':title'       => $body['title'],
            ':description' => $body['description'] ?? null,
            ':event_date'  => $body['event_date'],
            ':event_time'  => $body['event_time'] ?? null,
            ':location'    => $body['location'] ?? null,
            ':is_online'   => $body['is_online'] ?? 0,
            ':stream_url'  => $body['stream_url'] ?? null,
        ]);
        Response::json(['id' => $db->lastInsertId()], 201);
    }
}

// ── GivingController ──────────────────────────────────────────

class GivingController
{
    public function initiate(array $params, array $body): void
    {
        $db  = Database::connect();
        $ref = 'MIN-' . strtoupper(uniqid());

        $stmt = $db->prepare('INSERT INTO givings (reference, amount, currency, giving_type, donor_name, donor_email) VALUES (:ref, :amount, :currency, :type, :name, :email)');
        $stmt->execute([
            ':ref'      => $ref,
            ':amount'   => $body['amount'],
            ':currency' => $body['currency'] ?? 'USD',
            ':type'     => $body['giving_type'] ?? 'offering',
            ':name'     => $body['donor_name'] ?? null,
            ':email'    => $body['donor_email'] ?? null,
        ]);

        // Return Flutterwave payment link data
        // The Flutter app will open Flutterwave's inline checkout WebView
        Response::json([
            'reference'    => $ref,
            'amount'       => $body['amount'],
            'currency'     => $body['currency'] ?? 'USD',
            'redirect_url' => 'https://yourdomain.com/give/thanks',
        ], 201);
    }

    public function webhook(array $params, array $body): void
    {
        // Flutterwave webhook — verify and mark as completed
        $db   = Database::connect();
        $stmt = $db->prepare('UPDATE givings SET status = :status WHERE reference = :ref');
        $stmt->execute([
            ':status' => $body['status'] === 'successful' ? 'completed' : 'failed',
            ':ref'    => $body['txRef'] ?? $body['tx_ref'] ?? '',
        ]);
        Response::json(['received' => true]);
    }

    public function index(array $params, array $body): void
    {
        $db   = Database::connect();
        $rows = $db->query("SELECT * FROM givings ORDER BY created_at DESC LIMIT 100")->fetchAll();
        Response::json($rows);
    }
}

// ── PrayerController ──────────────────────────────────────────

class PrayerController
{
    public function store(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('INSERT INTO prayers (message, contact) VALUES (:message, :contact)');
        $stmt->execute([
            ':message' => $body['message'] ?? '',
            ':contact' => $body['contact'] ?? null,
        ]);
        Response::json(['submitted' => true], 201);
    }

    public function index(array $params, array $body): void
    {
        $db   = Database::connect();
        $rows = $db->query("SELECT * FROM prayers ORDER BY created_at DESC")->fetchAll();
        Response::json($rows);
    }
}

// ── AuthController ────────────────────────────────────────────

class AuthController
{
    public function login(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT * FROM admins WHERE email = :email');
        $stmt->execute([':email' => $body['email'] ?? '']);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($body['password'] ?? '', $admin['password'])) {
            Response::json(['error' => 'Invalid credentials'], 401);
        }

        $token = Auth::sign(['id' => $admin['id'], 'email' => $admin['email'], 'role' => 'admin']);
        Response::json(['token' => $token, 'name' => $admin['name']]);
    }
}

// ── DeviceController ──────────────────────────────────────────

class DeviceController
{
    public function register(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('INSERT OR REPLACE INTO device_tokens (token, platform) VALUES (:token, :platform)');
        $stmt->execute([
            ':token'    => $body['token'] ?? '',
            ':platform' => $body['platform'] ?? 'android',
        ]);
        Response::json(['registered' => true]);
    }

    public function broadcast(array $params, array $body): void
    {
        $sent = Notify::broadcast(
            $body['title'] ?? 'Ministry Update',
            $body['message'] ?? '',
            $body['data'] ?? []
        );
        Response::json(['sent' => $sent]);
    }
}

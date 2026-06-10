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
        $db   = Database::connect();
        $rows = $db->query("SELECT * FROM radio_schedule WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        Response::json($rows);
    }

    public function updateSchedule(array $params, array $body): void
    {
        $db = Database::connect();

        if (!empty($params['id'])) {
            $stmt = $db->prepare('
                UPDATE radio_schedule
                SET day_of_week = :day, start_time = :start, end_time = :end,
                    program_name = :program, description = :description, is_active = :active
                WHERE id = :id
            ');
            $stmt->execute([
                ':day'         => $body['day_of_week'],
                ':start'       => $body['start_time'],
                ':end'         => $body['end_time'],
                ':program'     => $body['program_name'],
                ':description' => $body['description'] ?? null,
                ':active'      => isset($body['is_active']) ? (int)$body['is_active'] : 1,
                ':id'          => $params['id'],
            ]);
        } else {
            $stmt = $db->prepare('
                INSERT INTO radio_schedule (day_of_week, start_time, end_time, program_name, description)
                VALUES (:day, :start, :end, :program, :description)
            ');
            $stmt->execute([
                ':day'         => $body['day_of_week'],
                ':start'       => $body['start_time'],
                ':end'         => $body['end_time'],
                ':program'     => $body['program_name'],
                ':description' => $body['description'] ?? null,
            ]);
        }

        Response::json(['saved' => true]);
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
        if (empty($body['title']) || empty($body['event_date'])) {
            Response::json(['error' => 'title and event_date are required'], 422);
        }

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

    public function update(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('
            UPDATE events
            SET title = :title, description = :description, event_date = :event_date,
                event_time = :event_time, location = :location,
                is_online = :is_online, stream_url = :stream_url
            WHERE id = :id
        ');
        $stmt->execute([
            ':title'       => $body['title'],
            ':description' => $body['description'] ?? null,
            ':event_date'  => $body['event_date'],
            ':event_time'  => $body['event_time'] ?? null,
            ':location'    => $body['location'] ?? null,
            ':is_online'   => $body['is_online'] ?? 0,
            ':stream_url'  => $body['stream_url'] ?? null,
            ':id'          => $params['id'],
        ]);
        Response::json(['updated' => true]);
    }

    public function destroy(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('DELETE FROM events WHERE id = :id');
        $stmt->execute([':id' => $params['id']]);
        Response::json(['deleted' => true]);
    }
}

// ── GivingController ──────────────────────────────────────────

class GivingController
{
    public function initiate(array $params, array $body): void
    {
        $db  = Database::connect();
        $ref = 'MIN-' . strtoupper(uniqid());

        $stmt = $db->prepare('INSERT INTO givings (reference, amount, currency, giving_type, donor_name, donor_email, campaign_id) VALUES (:ref, :amount, :currency, :type, :name, :email, :campaign_id)');
        $stmt->execute([
            ':ref'         => $ref,
            ':amount'      => $body['amount'],
            ':currency'    => $body['currency'] ?? 'USD',
            ':type'        => $body['giving_type'] ?? 'offering',
            ':name'        => $body['donor_name'] ?? null,
            ':email'       => $body['donor_email'] ?? null,
            ':campaign_id' => $body['campaign_id'] ?? null,
        ]);

        $appUrl = $_ENV['APP_URL'] ?? 'https://roberttalemwa.online';
        Response::json([
            'reference'    => $ref,
            'amount'       => $body['amount'],
            'currency'     => $body['currency'] ?? 'USD',
            'redirect_url' => $appUrl . '/give/thanks',
        ], 201);
    }

    public function webhook(array $params, array $body): void
    {
        $db     = Database::connect();
        $ref    = $body['txRef'] ?? $body['tx_ref'] ?? $body['data']['tx_ref'] ?? '';
        $status = ($body['status'] === 'successful' || ($body['data']['status'] ?? '') === 'successful')
                  ? 'completed' : 'failed';

        $stmt = $db->prepare('UPDATE givings SET status = :status WHERE reference = :ref');
        $stmt->execute([':status' => $status, ':ref' => $ref]);

        // Update campaign raised_amount if linked
        if ($status === 'completed') {
            $giving = $db->prepare('SELECT campaign_id, amount FROM givings WHERE reference = :ref');
            $giving->execute([':ref' => $ref]);
            $row = $giving->fetch();

            if ($row && $row['campaign_id']) {
                $db->prepare('UPDATE campaigns SET raised_amount = raised_amount + :amount WHERE id = :id')
                   ->execute([':amount' => $row['amount'], ':id' => $row['campaign_id']]);
            }
        }

        Response::json(['received' => true]);
    }

    public function index(array $params, array $body): void
    {
        $db     = Database::connect();
        $where  = ['1=1'];
        $binds  = [];

        if (!empty($_GET['currency'])) {
            $where[] = 'currency = :currency';
            $binds[':currency'] = $_GET['currency'];
        }
        if (!empty($_GET['giving_type'])) {
            $where[] = 'giving_type = :giving_type';
            $binds[':giving_type'] = $_GET['giving_type'];
        }
        if (!empty($_GET['status'])) {
            $where[] = 'status = :status';
            $binds[':status'] = $_GET['status'];
        }
        if (!empty($_GET['from'])) {
            $where[] = 'created_at >= :from';
            $binds[':from'] = $_GET['from'];
        }
        if (!empty($_GET['to'])) {
            $where[] = 'created_at <= :to';
            $binds[':to'] = $_GET['to'];
        }

        $sql  = 'SELECT * FROM givings WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC LIMIT 200';
        $stmt = $db->prepare($sql);
        $stmt->execute($binds);
        Response::json($stmt->fetchAll());
    }

    public function summary(array $params, array $body): void
    {
        $db = Database::connect();

        $byCurrency = $db->query("
            SELECT currency, SUM(amount) as total, COUNT(*) as count
            FROM givings WHERE status = 'completed'
            GROUP BY currency
        ")->fetchAll();

        $byType = $db->query("
            SELECT giving_type, currency, SUM(amount) as total, COUNT(*) as count
            FROM givings WHERE status = 'completed'
            GROUP BY giving_type, currency
        ")->fetchAll();

        $byMonth = $db->query("
            SELECT strftime('%Y-%m', created_at) as month, currency, SUM(amount) as total
            FROM givings WHERE status = 'completed'
            GROUP BY month, currency
            ORDER BY month DESC
            LIMIT 12
        ")->fetchAll();

        Response::json([
            'by_currency' => $byCurrency,
            'by_type'     => $byType,
            'by_month'    => $byMonth,
        ]);
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
        $db    = Database::connect();
        $where = ['1=1'];
        $binds = [];

        if (isset($_GET['is_read'])) {
            $where[] = 'is_read = :is_read';
            $binds[':is_read'] = (int)$_GET['is_read'];
        }
        if (isset($_GET['is_urgent'])) {
            $where[] = 'is_urgent = :is_urgent';
            $binds[':is_urgent'] = (int)$_GET['is_urgent'];
        }
        if (isset($_GET['is_prayed'])) {
            $where[] = 'is_prayed = :is_prayed';
            $binds[':is_prayed'] = (int)$_GET['is_prayed'];
        }

        $sql  = 'SELECT * FROM prayers WHERE ' . implode(' AND ', $where) . ' ORDER BY is_urgent DESC, created_at DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute($binds);
        Response::json($stmt->fetchAll());
    }

    public function update(array $params, array $body): void
    {
        $db     = Database::connect();
        $fields = [];
        $binds  = [':id' => $params['id']];

        if (isset($body['is_read'])) {
            $fields[] = 'is_read = :is_read';
            $binds[':is_read'] = (int)$body['is_read'];
        }
        if (isset($body['is_prayed'])) {
            $fields[] = 'is_prayed = :is_prayed';
            $binds[':is_prayed'] = (int)$body['is_prayed'];
        }
        if (isset($body['is_urgent'])) {
            $fields[] = 'is_urgent = :is_urgent';
            $binds[':is_urgent'] = (int)$body['is_urgent'];
        }

        if (empty($fields)) {
            Response::json(['error' => 'Nothing to update'], 422);
        }

        $stmt = $db->prepare('UPDATE prayers SET ' . implode(', ', $fields) . ' WHERE id = :id');
        $stmt->execute($binds);
        Response::json(['updated' => true]);
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

        // Update last_login timestamp
        $db->prepare('UPDATE admins SET last_login = datetime("now") WHERE id = :id')
           ->execute([':id' => $admin['id']]);

        $token = Auth::sign(['id' => $admin['id'], 'email' => $admin['email'], 'role' => $admin['role']]);
        Response::json(['token' => $token, 'name' => $admin['name'], 'role' => $admin['role']]);
    }
}

// ── DeviceController ──────────────────────────────────────────

class DeviceController
{
    public function register(array $params, array $body): void
    {
        if (empty($body['token'])) {
            Response::json(['error' => 'token is required'], 422);
        }

        $db   = Database::connect();
        $stmt = $db->prepare('INSERT OR REPLACE INTO device_tokens (token, platform, country) VALUES (:token, :platform, :country)');
        $stmt->execute([
            ':token'    => $body['token'],
            ':platform' => $body['platform'] ?? 'android',
            ':country'  => $body['country'] ?? null,
        ]);
        Response::json(['registered' => true]);
    }

    public function trackInstall(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('INSERT INTO app_installs (platform, country, app_version) VALUES (:platform, :country, :version)');
        $stmt->execute([
            ':platform' => $body['platform'] ?? 'android',
            ':country'  => $body['country'] ?? null,
            ':version'  => $body['app_version'] ?? null,
        ]);
        Response::json(['tracked' => true], 201);
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

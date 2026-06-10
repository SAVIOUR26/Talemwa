<?php

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $dir = dirname(DB_PATH);
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            self::$instance = new PDO('sqlite:' . DB_PATH);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // WAL mode — handles concurrent reads/writes efficiently
            self::$instance->exec('PRAGMA journal_mode=WAL');
            self::$instance->exec('PRAGMA foreign_keys=ON');

            self::migrate(self::$instance);
        }
        return self::$instance;
    }

    private static function migrate(PDO $db): void
    {
        $db->exec("
            CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'media',
                last_login TEXT,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS sermons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                series TEXT,
                speaker TEXT DEFAULT 'Pastor Robert Talemwa',
                description TEXT,
                youtube_url TEXT,
                mp3_url TEXT,
                duration_seconds INTEGER DEFAULT 0,
                thumbnail_url TEXT,
                scripture TEXT,
                tags TEXT,
                published INTEGER DEFAULT 1,
                download_count INTEGER DEFAULT 0,
                play_count INTEGER DEFAULT 0,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                event_date TEXT NOT NULL,
                event_time TEXT,
                location TEXT,
                is_online INTEGER DEFAULT 0,
                stream_url TEXT,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS live_status (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                is_live INTEGER DEFAULT 0,
                youtube_id TEXT,
                title TEXT DEFAULT 'Sunday Service',
                updated_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS givings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                reference TEXT UNIQUE NOT NULL,
                amount REAL NOT NULL,
                currency TEXT DEFAULT 'USD',
                giving_type TEXT DEFAULT 'offering',
                donor_name TEXT,
                donor_email TEXT,
                status TEXT DEFAULT 'pending',
                provider TEXT DEFAULT 'flutterwave',
                campaign_id INTEGER,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS campaigns (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT,
                goal_amount REAL NOT NULL,
                currency TEXT DEFAULT 'USD',
                raised_amount REAL DEFAULT 0,
                deadline TEXT,
                is_active INTEGER DEFAULT 1,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS prayers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                message TEXT NOT NULL,
                contact TEXT,
                is_read INTEGER DEFAULT 0,
                is_prayed INTEGER DEFAULT 0,
                is_urgent INTEGER DEFAULT 0,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS device_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                token TEXT UNIQUE NOT NULL,
                platform TEXT DEFAULT 'android',
                country TEXT,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS app_installs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                platform TEXT NOT NULL,
                country TEXT,
                app_version TEXT,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS notifications_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                message TEXT NOT NULL,
                target TEXT DEFAULT 'all',
                sent_count INTEGER DEFAULT 0,
                delivered_count INTEGER DEFAULT 0,
                opened_count INTEGER DEFAULT 0,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS radio_schedule (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                day_of_week TEXT NOT NULL,
                start_time TEXT NOT NULL,
                end_time TEXT NOT NULL,
                program_name TEXT NOT NULL,
                description TEXT,
                is_active INTEGER DEFAULT 1
            );
        ");

        // Seed live_status row if empty
        $row = $db->query('SELECT COUNT(*) as c FROM live_status')->fetch();
        if ($row['c'] == 0) {
            $db->exec("INSERT INTO live_status (is_live, title) VALUES (0, 'Sunday Service')");
        }

        // Seed default admin if empty
        $row = $db->query('SELECT COUNT(*) as c FROM admins')->fetch();
        if ($row['c'] == 0) {
            $hash = password_hash('TalemwaAdmin2024!', PASSWORD_BCRYPT);
            $db->exec("INSERT INTO admins (name, email, password, role) VALUES ('Admin', 'admin@roberttalemwa.online', '$hash', 'super_admin')");
        }

        // Seed default radio schedule if empty
        $row = $db->query('SELECT COUNT(*) as c FROM radio_schedule')->fetch();
        if ($row['c'] == 0) {
            $db->exec("
                INSERT INTO radio_schedule (day_of_week, start_time, end_time, program_name, description) VALUES
                ('sunday',    '10:00', '13:00', 'Sunday Service Live',    'Live Sunday worship service'),
                ('sunday',    '18:00', '19:00', 'Evening Devotion',       'Evening devotional session'),
                ('wednesday', '19:00', '21:00', 'Midweek Bible Study',    'In-depth Bible teaching'),
                ('friday',    '20:00', '22:00', 'Prayer Night',           'Corporate prayer session'),
                ('daily',     '06:00', '07:00', 'Morning Devotion',       'Daily morning devotion');
            ");
        }
    }
}

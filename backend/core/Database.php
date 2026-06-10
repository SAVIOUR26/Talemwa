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
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS sermons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                series TEXT,
                speaker TEXT DEFAULT 'Pastor',
                description TEXT,
                youtube_url TEXT,
                mp3_url TEXT,
                duration_seconds INTEGER DEFAULT 0,
                thumbnail_url TEXT,
                scripture TEXT,
                tags TEXT,
                published INTEGER DEFAULT 1,
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
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS prayers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                message TEXT NOT NULL,
                contact TEXT,
                is_read INTEGER DEFAULT 0,
                created_at TEXT DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS device_tokens (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                token TEXT UNIQUE NOT NULL,
                platform TEXT DEFAULT 'android',
                created_at TEXT DEFAULT (datetime('now'))
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
            $hash = password_hash('admin123', PASSWORD_BCRYPT);
            $db->exec("INSERT INTO admins (name, email, password) VALUES ('Admin', 'admin@ministry.com', '$hash')");
        }
    }
}

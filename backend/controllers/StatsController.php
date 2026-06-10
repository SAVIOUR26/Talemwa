<?php

class StatsController
{
    public function overview(array $params, array $body): void
    {
        $db = Database::connect();

        $sermonCount = $db->query("SELECT COUNT(*) as c FROM sermons WHERE published = 1")->fetch()['c'];

        $liveStatus  = $db->query("SELECT is_live, title FROM live_status WHERE id = 1")->fetch();

        $installsThisWeek = $db->query("
            SELECT COUNT(*) as c FROM app_installs
            WHERE created_at >= datetime('now', '-7 days')
        ")->fetch()['c'];

        $totalInstalls = $db->query("SELECT COUNT(*) as c FROM app_installs")->fetch()['c'];

        $givingThisMonth = $db->query("
            SELECT currency, SUM(amount) as total
            FROM givings
            WHERE status = 'completed'
              AND strftime('%Y-%m', created_at) = strftime('%Y-%m', 'now')
            GROUP BY currency
        ")->fetchAll();

        $unreadPrayers = $db->query("SELECT COUNT(*) as c FROM prayers WHERE is_read = 0")->fetch()['c'];

        $urgentPrayers = $db->query("SELECT COUNT(*) as c FROM prayers WHERE is_urgent = 1 AND is_prayed = 0")->fetch()['c'];

        Response::json([
            'sermons'           => (int)$sermonCount,
            'live'              => [
                'is_live' => (bool)$liveStatus['is_live'],
                'title'   => $liveStatus['title'],
            ],
            'installs'          => [
                'total'     => (int)$totalInstalls,
                'this_week' => (int)$installsThisWeek,
            ],
            'giving_this_month' => $givingThisMonth,
            'prayers'           => [
                'unread' => (int)$unreadPrayers,
                'urgent' => (int)$urgentPrayers,
            ],
        ]);
    }

    public function installs(array $params, array $body): void
    {
        $db = Database::connect();

        // Daily installs — last 30 days
        $daily = $db->query("
            SELECT
                strftime('%Y-%m-%d', created_at) as date,
                platform,
                COUNT(*) as count
            FROM app_installs
            WHERE created_at >= datetime('now', '-30 days')
            GROUP BY date, platform
            ORDER BY date ASC
        ")->fetchAll();

        // Country breakdown — top 20
        $byCountry = $db->query("
            SELECT country, COUNT(*) as count
            FROM app_installs
            WHERE country IS NOT NULL
            GROUP BY country
            ORDER BY count DESC
            LIMIT 20
        ")->fetchAll();

        // Platform totals
        $byPlatform = $db->query("
            SELECT platform, COUNT(*) as count
            FROM app_installs
            GROUP BY platform
        ")->fetchAll();

        // Version breakdown
        $byVersion = $db->query("
            SELECT app_version, COUNT(*) as count
            FROM app_installs
            WHERE app_version IS NOT NULL
            GROUP BY app_version
            ORDER BY count DESC
        ")->fetchAll();

        Response::json([
            'daily'       => $daily,
            'by_country'  => $byCountry,
            'by_platform' => $byPlatform,
            'by_version'  => $byVersion,
        ]);
    }

    public function sermons(array $params, array $body): void
    {
        $db = Database::connect();

        $topByPlays = $db->query("
            SELECT id, title, series, speaker, play_count, download_count, created_at
            FROM sermons
            WHERE published = 1
            ORDER BY play_count DESC
            LIMIT 10
        ")->fetchAll();

        $topByDownloads = $db->query("
            SELECT id, title, series, download_count
            FROM sermons
            WHERE published = 1
            ORDER BY download_count DESC
            LIMIT 10
        ")->fetchAll();

        $seriesSummary = $db->query("
            SELECT series, COUNT(*) as count, SUM(play_count) as total_plays
            FROM sermons
            WHERE published = 1 AND series IS NOT NULL
            GROUP BY series
            ORDER BY total_plays DESC
        ")->fetchAll();

        Response::json([
            'top_by_plays'     => $topByPlays,
            'top_by_downloads' => $topByDownloads,
            'by_series'        => $seriesSummary,
        ]);
    }

    public function giving(array $params, array $body): void
    {
        $db = Database::connect();

        // Monthly totals — last 6 months, grouped by currency
        $monthly = $db->query("
            SELECT
                strftime('%Y-%m', created_at) as month,
                currency,
                SUM(amount) as total,
                COUNT(*) as count
            FROM givings
            WHERE status = 'completed'
              AND created_at >= datetime('now', '-6 months')
            GROUP BY month, currency
            ORDER BY month ASC
        ")->fetchAll();

        // By giving type
        $byType = $db->query("
            SELECT giving_type, currency, SUM(amount) as total, COUNT(*) as count
            FROM givings
            WHERE status = 'completed'
            GROUP BY giving_type, currency
        ")->fetchAll();

        // Recent givings
        $recent = $db->query("
            SELECT reference, amount, currency, giving_type, donor_name, status, created_at
            FROM givings
            ORDER BY created_at DESC
            LIMIT 20
        ")->fetchAll();

        Response::json([
            'monthly'  => $monthly,
            'by_type'  => $byType,
            'recent'   => $recent,
        ]);
    }
}

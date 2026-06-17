<?php

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

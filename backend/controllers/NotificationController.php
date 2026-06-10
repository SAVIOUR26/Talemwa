<?php

class NotificationController
{
    public function send(array $params, array $body): void
    {
        if (empty($body['title']) || empty($body['message'])) {
            Response::json(['error' => 'title and message are required'], 422);
        }

        $db     = Database::connect();
        $target = $body['target'] ?? 'all';
        $data   = $body['data'] ?? [];

        // Count tokens being targeted
        if ($target === 'all') {
            $tokenCount = $db->query("SELECT COUNT(*) as c FROM device_tokens")->fetch()['c'];
        } else {
            $stmt = $db->prepare("SELECT COUNT(*) as c FROM device_tokens WHERE platform = :platform");
            $stmt->execute([':platform' => $target]);
            $tokenCount = $stmt->fetch()['c'];
        }

        $sent = Notify::broadcast($body['title'], $body['message'], $data, $target);

        // Log the notification
        $stmt = $db->prepare('
            INSERT INTO notifications_log (title, message, target, sent_count)
            VALUES (:title, :message, :target, :sent_count)
        ');
        $stmt->execute([
            ':title'      => $body['title'],
            ':message'    => $body['message'],
            ':target'     => $target,
            ':sent_count' => $sent,
        ]);

        Response::json([
            'sent'    => $sent,
            'log_id'  => $db->lastInsertId(),
        ]);
    }

    public function history(array $params, array $body): void
    {
        $db   = Database::connect();
        $rows = $db->query("SELECT * FROM notifications_log ORDER BY created_at DESC LIMIT 50")->fetchAll();
        Response::json($rows);
    }
}

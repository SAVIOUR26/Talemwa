<?php

class Response
{
    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        echo json_encode(['status' => $status < 400 ? 'success' : 'error', 'data' => $data]);
        exit;
    }

    public static function paginate(array $items, int $total, int $page, int $perPage): void
    {
        self::json([
            'items'       => $items,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ]);
    }
}

class Notify
{
    public static function broadcast(string $title, string $body, array $data = []): bool
    {
        if (!FCM_SERVER_KEY) return false;

        $db     = Database::connect();
        $tokens = $db->query('SELECT token FROM device_tokens')->fetchAll(PDO::FETCH_COLUMN);
        if (empty($tokens)) return false;

        $payload = [
            'registration_ids' => $tokens,
            'notification'     => ['title' => $title, 'body' => $body, 'sound' => 'default'],
            'data'             => $data,
        ];

        $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: key=' . FCM_SERVER_KEY,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);
        curl_exec($ch);
        curl_close($ch);
        return true;
    }
}

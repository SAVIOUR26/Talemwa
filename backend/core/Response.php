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
    // target: 'all' | 'android' | 'ios'
    public static function broadcast(string $title, string $body, array $data = [], string $target = 'all'): int
    {
        if (!FCM_SERVER_KEY) return 0;

        $db = Database::connect();

        if ($target === 'all') {
            $tokens = $db->query('SELECT token FROM device_tokens')->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $stmt = $db->prepare('SELECT token FROM device_tokens WHERE platform = :platform');
            $stmt->execute([':platform' => $target]);
            $tokens = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        if (empty($tokens)) return 0;

        // FCM allows max 500 tokens per request — chunk if needed
        $chunks = array_chunk($tokens, 500);
        $sent   = 0;

        foreach ($chunks as $chunk) {
            $payload = [
                'registration_ids' => $chunk,
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
            $sent += count($chunk);
        }

        return $sent;
    }
}

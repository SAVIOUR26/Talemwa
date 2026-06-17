<?php

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

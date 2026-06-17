<?php

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

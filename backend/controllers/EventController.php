<?php

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

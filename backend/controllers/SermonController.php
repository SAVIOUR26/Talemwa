<?php
// ── SermonController ──────────────────────────────────────────

class SermonController
{
    public function index(array $params, array $body): void
    {
        $db      = Database::connect();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;
        $search  = $_GET['search'] ?? '';
        $series  = $_GET['series'] ?? '';

        $where = ['published = 1'];
        $bind  = [];

        if ($search) {
            $where[] = '(title LIKE :search OR speaker LIKE :search OR scripture LIKE :search)';
            $bind[':search'] = "%$search%";
        }
        if ($series) {
            $where[] = 'series = :series';
            $bind[':series'] = $series;
        }

        $whereStr = 'WHERE ' . implode(' AND ', $where);
        $total = $db->prepare("SELECT COUNT(*) FROM sermons $whereStr");
        $total->execute($bind);
        $total = (int)$total->fetchColumn();

        $stmt = $db->prepare("SELECT * FROM sermons $whereStr ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        foreach ($bind as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        Response::paginate($stmt->fetchAll(), $total, $page, $perPage);
    }

    public function show(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT * FROM sermons WHERE id = :id AND published = 1');
        $stmt->execute([':id' => $params['id']]);
        $sermon = $stmt->fetch();
        if (!$sermon) Response::json(['error' => 'Sermon not found'], 404);
        Response::json($sermon);
    }

    public function series(array $params, array $body): void
    {
        $db   = Database::connect();
        $rows = $db->query("SELECT series, COUNT(*) as count FROM sermons WHERE series IS NOT NULL AND published = 1 GROUP BY series ORDER BY series")->fetchAll();
        Response::json($rows);
    }

    public function store(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('INSERT INTO sermons (title, series, speaker, description, youtube_url, mp3_url, duration_seconds, thumbnail_url, scripture, tags) VALUES (:title, :series, :speaker, :description, :youtube_url, :mp3_url, :duration_seconds, :thumbnail_url, :scripture, :tags)');
        $stmt->execute([
            ':title'            => $body['title'] ?? '',
            ':series'           => $body['series'] ?? null,
            ':speaker'          => $body['speaker'] ?? 'Pastor',
            ':description'      => $body['description'] ?? null,
            ':youtube_url'      => $body['youtube_url'] ?? null,
            ':mp3_url'          => $body['mp3_url'] ?? null,
            ':duration_seconds' => $body['duration_seconds'] ?? 0,
            ':thumbnail_url'    => $body['thumbnail_url'] ?? null,
            ':scripture'        => $body['scripture'] ?? null,
            ':tags'             => $body['tags'] ?? null,
        ]);
        $id = $db->lastInsertId();

        // Notify all devices
        Notify::broadcast(
            '🎙️ New Sermon Posted',
            $body['title'] ?? 'A new message has been uploaded',
            ['type' => 'sermon', 'id' => $id]
        );

        Response::json(['id' => $id], 201);
    }

    public function update(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('UPDATE sermons SET title=:title, series=:series, speaker=:speaker, description=:description, youtube_url=:youtube_url, mp3_url=:mp3_url, scripture=:scripture, tags=:tags WHERE id=:id');
        $stmt->execute(array_merge($body, [':id' => $params['id']]));
        Response::json(['updated' => true]);
    }

    public function destroy(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('UPDATE sermons SET published = 0 WHERE id = :id');
        $stmt->execute([':id' => $params['id']]);
        Response::json(['deleted' => true]);
    }
}

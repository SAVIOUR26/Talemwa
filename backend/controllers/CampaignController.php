<?php

class CampaignController
{
    public function index(array $params, array $body): void
    {
        $db   = Database::connect();
        $rows = $db->query("SELECT * FROM campaigns WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();
        Response::json($rows);
    }

    public function store(array $params, array $body): void
    {
        if (empty($body['title']) || empty($body['goal_amount'])) {
            Response::json(['error' => 'title and goal_amount are required'], 422);
        }

        $db   = Database::connect();
        $stmt = $db->prepare('
            INSERT INTO campaigns (title, description, goal_amount, currency, deadline)
            VALUES (:title, :description, :goal_amount, :currency, :deadline)
        ');
        $stmt->execute([
            ':title'       => $body['title'],
            ':description' => $body['description'] ?? null,
            ':goal_amount' => $body['goal_amount'],
            ':currency'    => $body['currency'] ?? 'USD',
            ':deadline'    => $body['deadline'] ?? null,
        ]);
        Response::json(['id' => $db->lastInsertId()], 201);
    }

    public function update(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('
            UPDATE campaigns
            SET title = :title, description = :description, goal_amount = :goal_amount,
                currency = :currency, deadline = :deadline, is_active = :is_active
            WHERE id = :id
        ');
        $stmt->execute([
            ':title'       => $body['title'],
            ':description' => $body['description'] ?? null,
            ':goal_amount' => $body['goal_amount'],
            ':currency'    => $body['currency'] ?? 'USD',
            ':deadline'    => $body['deadline'] ?? null,
            ':is_active'   => isset($body['is_active']) ? (int)$body['is_active'] : 1,
            ':id'          => $params['id'],
        ]);
        Response::json(['updated' => true]);
    }

    public function destroy(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('UPDATE campaigns SET is_active = 0 WHERE id = :id');
        $stmt->execute([':id' => $params['id']]);
        Response::json(['archived' => true]);
    }
}

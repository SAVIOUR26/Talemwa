<?php

class AdminController
{
    public function index(array $params, array $body): void
    {
        $db   = Database::connect();
        $rows = $db->query("SELECT id, name, email, role, last_login, created_at FROM admins ORDER BY created_at ASC")->fetchAll();
        Response::json($rows);
    }

    public function store(array $params, array $body): void
    {
        if (empty($body['name']) || empty($body['email']) || empty($body['password'])) {
            Response::json(['error' => 'name, email and password are required'], 422);
        }

        $db   = Database::connect();
        $hash = password_hash($body['password'], PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO admins (name, email, password, role) VALUES (:name, :email, :password, :role)');

        try {
            $stmt->execute([
                ':name'     => $body['name'],
                ':email'    => $body['email'],
                ':password' => $hash,
                ':role'     => $body['role'] ?? 'media',
            ]);
            Response::json(['id' => $db->lastInsertId()], 201);
        } catch (\PDOException $e) {
            Response::json(['error' => 'Email already exists'], 409);
        }
    }

    public function update(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('UPDATE admins SET name = :name, role = :role WHERE id = :id');
        $stmt->execute([
            ':name' => $body['name'],
            ':role' => $body['role'] ?? 'media',
            ':id'   => $params['id'],
        ]);
        Response::json(['updated' => true]);
    }

    public function destroy(array $params, array $body): void
    {
        // Prevent deleting yourself
        $currentAdmin = Auth::user();
        if ((int)$currentAdmin['id'] === (int)$params['id']) {
            Response::json(['error' => 'Cannot delete your own account'], 403);
        }

        $db   = Database::connect();
        $stmt = $db->prepare('DELETE FROM admins WHERE id = :id');
        $stmt->execute([':id' => $params['id']]);
        Response::json(['deleted' => true]);
    }
}

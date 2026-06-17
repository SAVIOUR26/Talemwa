<?php

class AuthController
{
    public function login(array $params, array $body): void
    {
        $db   = Database::connect();
        $stmt = $db->prepare('SELECT * FROM admins WHERE email = :email');
        $stmt->execute([':email' => $body['email'] ?? '']);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($body['password'] ?? '', $admin['password'])) {
            Response::json(['error' => 'Invalid credentials'], 401);
        }

        // Update last_login timestamp
        $db->prepare('UPDATE admins SET last_login = datetime("now") WHERE id = :id')
           ->execute([':id' => $admin['id']]);

        $token = Auth::sign(['id' => $admin['id'], 'email' => $admin['email'], 'role' => $admin['role']]);
        Response::json(['token' => $token, 'name' => $admin['name'], 'role' => $admin['role']]);
    }
}

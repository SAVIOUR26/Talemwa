<?php

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, bool $auth = false): void
    {
        $this->routes[] = ['GET', $path, $handler, $auth];
    }

    public function post(string $path, array $handler, bool $auth = false): void
    {
        $this->routes[] = ['POST', $path, $handler, $auth];
    }

    public function put(string $path, array $handler, bool $auth = false): void
    {
        $this->routes[] = ['PUT', $path, $handler, $auth];
    }

    public function delete(string $path, array $handler, bool $auth = false): void
    {
        $this->routes[] = ['DELETE', $path, $handler, $auth];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = strtok($_SERVER['REQUEST_URI'], '?');

        foreach ($this->routes as [$routeMethod, $routePath, $handler, $requiresAuth]) {
            if ($method !== $routeMethod) continue;

            $params = $this->match($routePath, $uri);
            if ($params === false) continue;

            // Auth gate
            if ($requiresAuth) {
                $admin = Auth::guard();
                if (!$admin) {
                    Response::json(['error' => 'Unauthorized'], 401);
                    return;
                }
            }

            // Call controller method
            [$class, $action] = $handler;
            $controller = new $class();
            $body = json_decode(file_get_contents('php://input'), true) ?? [];
            $controller->$action($params, $body);
            return;
        }

        Response::json(['error' => 'Route not found'], 404);
    }

    private function match(string $routePath, string $uri): array|false
    {
        $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        preg_match_all('/\{([^}]+)\}/', $routePath, $keys);
        if (!preg_match($pattern, $uri, $values)) return false;

        array_shift($values);
        return array_combine($keys[1], $values) ?: [];
    }
}

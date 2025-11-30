<?php

class Router {
    protected $routes = [];

    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch($uri) {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Simple matching for now
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                $controllerClass = $route['controller'];
                $action = $route['action'];
                
                $controller = new $controllerClass();
                $controller->$action();
                return;
            }
        }

        // 404 Not Found
        echo "404 Not Found";
    }
}

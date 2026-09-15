<?php
namespace App\Core;

class Router {
    private array $routes = [];

    public function addRoute(string $method, string $path, string $controller, string $action) {
        // Convertir parámetros {id} a regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => "@^" . $pattern . "$@D",
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function get(string $path, string $controller, string $action) {
        $this->addRoute('GET', $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action) {
        $this->addRoute('POST', $path, $controller, $action);
    }

    public function dispatch(string $uri, string $method) {
        // Limpiar URI (quitar query string y ruta base)
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Obtener ruta base del subdirectorio (ej. /chinabox/php)
        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1));
        
        if (strpos($uri, $basepath) === 0) {
            $uri = substr($uri, strlen($basepath));
        }
        $uri = '/' . trim($uri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Quitar coincidencia completa
                
                $controllerName = "App\\Controllers\\" . $route['controller'];
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $route['action'])) {
                        // Pasar los parámetros capturados al método
                        return call_user_func_array([$controller, $route['action']], $matches);
                    }
                }
            }
        }

        // 404 No encontrado
        http_response_code(404);
        echo "404 - Página no encontrada ($uri)";
        exit;
    }
}

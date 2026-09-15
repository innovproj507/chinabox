<?php
namespace App\Core;

class Request {
    public static function get(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    public static function post(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    public static function all(): array {
        return array_merge($_GET, $_POST);
    }
    
    public static function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    public static function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
    
    public static function getUri(): string {
        return rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }
}

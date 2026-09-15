<?php
namespace App\Core;

class Response {
    public static function redirect(string $url) {
        $baseUrl = $_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? getenv('APP_URL') ?? '';
        
        // Si no hay APP_URL definida, intentamos autodetectar
        if (empty($baseUrl)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $baseUrl = "{$protocol}://{$host}/";
        }

        $baseUrl = ltrim(rtrim($baseUrl, '/'), '/'); // Evitar problemas de barras
        $url = ltrim($url, '/');
        
        // Cerrar sesión para evitar bloqueos antes de redireccionar
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Construir URL completa
        $fullUrl = $baseUrl . '/' . $url;
        if (!str_starts_with($fullUrl, 'http')) {
            $fullUrl = 'https://' . ltrim($fullUrl, '/');
        }

        header("Location: " . $fullUrl);
        exit;
    }

    public static function json($data, int $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

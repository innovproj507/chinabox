<?php
namespace App\Core;

class View {
    public static function render(string $view, array $data = [], bool $withBase = true) {
        // Extraer variables para que estén disponibles en la vista
        extract($data);
        
        // Obtener el usuario autenticado para la base
        $user = Auth::user();
        
        // Base URL disponible en todas las vistas
        $baseUrl = $_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? getenv('APP_URL') ?? '';
        if (empty($baseUrl)) {
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = "{$protocol}://{$host}/";
        }
        $baseUrl = rtrim($baseUrl, '/') . '/';
        
        // PRE-CARGAR MENSAJES FLASH antes de cerrar la sesión
        // Esto evita que los mensajes se queden "pegados" en la sesión
        $flash_messages = [];
        if (Flash::hasMessages()) {
            $flash_messages = Flash::get();
        }

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            // LIBERAR LA SESIÓN aquí.
            // Al haber ya extraído el usuario y los mensajes flash, podemos soltar el bloqueo.
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_write_close();
            }

            // Empezar a capturar el contenido
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            // Si la vista define que no usa base (ej. login, imprimir), se renderiza directo
            if ((isset($useBase) && $useBase === false) || !$withBase) {
                echo $content;
            } else {
                // Inyectar mensajes flash pre-cargados para que base.php los use si Flash::get() falla por estar cerrada la sesión
                // Aunque base.php usa Flash::get(), podemos pasar una variable por si acaso
                require __DIR__ . '/../../views/layout/base.php';
            }
        } else {
            throw new \Exception("Vista no encontrada: $viewFile");
        }
    }
}

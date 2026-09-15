<?php

require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Iniciar sesión
\App\Core\Session::start();

// Configurar y ejecutar enrutador
$router = new \App\Core\Router();

// Registrar rutas
require_once __DIR__ . '/src/routes.php';

// Despachar la petición
$router->dispatch(
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD']
);

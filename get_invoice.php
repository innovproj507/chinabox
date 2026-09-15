<?php
require 'vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__)->load();
$pdo = new PDO('mysql:host=localhost;dbname=chinabox', 'root', '');
$stmt = $pdo->query('SELECT id_factura FROM tbl_facturas ORDER BY id_factura DESC LIMIT 1');
echo $stmt->fetchColumn() . PHP_EOL;

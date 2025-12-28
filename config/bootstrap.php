<?php

session_start();

// definir base de directorios
define('BASE_DIR', dirname(__DIR__));

// Cargar el autoloader de Composer para poder usar librerías externas
require_once BASE_DIR . '/vendor/autoload.php';

// Cargar funciones globales
require_once BASE_DIR . '/config/functions.php';

use Dotenv\Dotenv;

// Cargar las variables del archivo .env
try {
    $dotenv = Dotenv::createImmutable(BASE_DIR);
    $dotenv->load();
} catch (Exception $e) {
    die("Error: No se pudo cargar el archivo .env (Revisa que el archivo exista en la raíz)");
}

// definir constante BASE_URL
define('BASE_URL', $_ENV['APP_URL'] ?? '');

// Crear conexión a la base de datos con PDO usando $_ENV
try {
    
    $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=utf8mb4";
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], $options);

} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}
?>
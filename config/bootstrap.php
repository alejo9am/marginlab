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
  if (file_exists(BASE_DIR . '/.env')) {
    $dotenv->load(); // Léelo y llena $_ENV
  }
} catch (Exception $e) {
    die("Error: No se pudo cargar el archivo .env (Revisa que el archivo exista en la raíz)");
}

// definir constante BASE_URL
define('BASE_URL', $_ENV['APP_URL'] ?? '');

// Definir el manejo de errores según el entorno
if ($_ENV['APP_ENV'] === 'local') {
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', 0); // En producción, silencio absoluto
  ini_set('display_startup_errors', 0);
}

// Crear conexión a la base de datos con PDO usando $_ENV INCLUYENDO el puerto
try {
    
  $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_DATABASE'] . ";charset=utf8mb4";

  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    // Añade estas líneas para TiDB:
    PDO::MYSQL_ATTR_SSL_CA => '', // Forzar SSL
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // No verificar estrictamente el certificado (vital para Vercel/TiDB a veces)
  ];

  $pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $options);

} catch (PDOException $e) {
  error_log('DB Connection Error: ' . $e->getMessage());
  die('Error crítico del sistema. Por favor intente más tarde.');
}

// LÓGICA DE FUNCIONAMIENTO DE LA SANDBOX

// AUTO-LOGIN (Persistencia de Sesión)
// Si NO hay sesión iniciada, PERO el navegador trae la cookie 'marginlab_token'...
if (!isset($_SESSION['sandbox_prefix']) && isset($_COOKIE['marginlab_token'])) {
  $token = $_COOKIE['marginlab_token'];
  // Validación de formato hexadecimal por seguridad
  if (ctype_xdigit($token)) {
    $_SESSION['sandbox_prefix'] = 'sbx_' . $token . '_';
  }
}

// SEGURIDAD SANDBOX (Aislamiento de Usuarios)
// Si estamos en modo demo, vigilamos las peticiones
if (isset($_SESSION['sandbox_prefix'])) {
  $prefix = $_SESSION['sandbox_prefix'];

  // Lista de parámetros que suelen llevar IDs en tu app
  $params_to_check = ['id', 'cod_presupuesto', 'cod_oferta'];

  // Función anónima para inyectar seguridad en un valor
  $asegurar_id = function ($valor) use ($prefix) {
    if (empty($valor)) return $valor;

    // CASO A: INTENTO DE HACK (Trae un token 'sbx_' que NO es el mío)
    if (strpos($valor, 'sbx_') === 0 && strpos($valor, $prefix) !== 0) {
      error_log("SEGURIDAD: Intento de acceso cruzado. User $prefix intentó $valor");
      die("<h1>ACCESO DENEGADO</h1><p>No tienes permiso para acceder a este Sandbox ajeno.</p>");
    }

    // CASO B: ID LIMPIO (Viene '10001') -> Le ponemos mi prefijo ('sbx_mio_10001')
    if (strpos($valor, 'sbx_') === false) {
      return $prefix . $valor;
    }

    // CASO C: ID CORRECTO (Ya trae mi prefijo) -> Lo dejamos pasar
    return $valor;
  };

  // Aplicamos el interceptor a GET, POST y REQUEST automáticamente
  foreach ($params_to_check as $key) {

    // PROCESAMIENTO $_GET
    if (isset($_GET[$key])) {
      $_GET[$key . '_visual'] = $_GET[$key];
      $_GET[$key] = $asegurar_id($_GET[$key]);
    }

    // PROCESAMIENTO $_POST
    if (isset($_POST[$key])) {
      $_POST[$key . '_visual'] = $_POST[$key];
      $_POST[$key] = $asegurar_id($_POST[$key]);
    }

    // PROCESAMIENTO $_REQUEST
    if (isset($_REQUEST[$key])) {
      $_REQUEST[$key . '_visual'] = $_REQUEST[$key];
      $_REQUEST[$key] = $asegurar_id($_REQUEST[$key]);
    }
  }
}

return $pdo;

?>
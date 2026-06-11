<?php
// index.php — router principal

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/Auth.php';

Auth::start();

$page = $_GET['page'] ?? 'dashboard';

// Rutas públicas (sin sesión)
$public = ['login'];

if (!in_array($page, $public)) {
    Auth::requireLogin();
}

// Mapa de página → controlador
$routes = [
    'login'         => ['controllers/AuthController.php',     'AuthController'],
    'logout'        => ['controllers/AuthController.php',     'AuthController'],
    'dashboard'     => ['controllers/DashboardController.php','DashboardController'],
    // Docentes
    'docentes'      => ['controllers/DocenteController.php',  'DocenteController'],
    // Grupos
    'grupos'        => ['controllers/GrupoController.php',    'GrupoController'],
    // Áreas
    'areas'         => ['controllers/AreaController.php',     'AreaController'],
    // Salones
    'salones'       => ['controllers/SalonController.php',    'SalonController'],
    // Temas
    'temas'         => ['controllers/TemaController.php',     'TemaController'],
    // Horarios
    'horarios'      => ['controllers/HorarioController.php',  'HorarioController'],
    // Seguimiento
    'seguimiento'   => ['controllers/SeguimientoController.php','SeguimientoController'],
];

if (!isset($routes[$page])) {
    http_response_code(404);
    echo '<h1>Página no encontrada</h1>';
    exit;
}

[$file, $class] = $routes[$page];
require_once __DIR__ . '/' . $file;

$controller = new $class();
$action = $_GET['action'] ?? 'index';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->index();
}

<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

session_start();

// Eliminar los datos de la sesión
$_SESSION = [];

// Eliminar la cookie de sesión
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destruir la sesión
session_destroy();

echo json_encode([
    'ok' => true,
    'message' => 'Sesión cerrada correctamente'
]);

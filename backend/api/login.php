<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

require_once __DIR__ . '/../config/database.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Recibir y validar los datos JSON
try {
    $data = json_decode(
        file_get_contents('php://input'),
        true,
        512,
        JSON_THROW_ON_ERROR
    );
} catch (JsonException $e) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'message' => 'Formato JSON no válido'
    ]);
    exit;
}

$usuario = trim((string)($data['usuario'] ?? ''));
$contrasena = (string)($data['contrasena'] ?? '');

if ($usuario === '' || $contrasena === '') {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'message' => 'Usuario y contraseña son obligatorios'
    ]);
    exit;
}

// Consultar y verificar al usuario en MySQL
try {
    $pdo = getPDO();

    $sql = 'SELECT
                id,
                usuario,
                nombre,
                password_hash,
                rol,
                activo
            FROM usuarios
            WHERE usuario = :usuario
            LIMIT 1';

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':usuario' => $usuario
    ]);

    $user = $stmt->fetch();

    if (
        !$user ||
        (int)$user['activo'] !== 1 ||
        !password_verify($contrasena, $user['password_hash'])
    ) {
        http_response_code(401);
        echo json_encode([
            'ok' => false,
            'message' => 'Usuario o contraseña incorrectos'
        ]);
        exit;
    }

    // Regenerar el identificador de sesión
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'usuario' => $user['usuario'],
        'nombre' => $user['nombre'],
        'rol' => $user['rol']
    ];

    echo json_encode([
        'ok' => true,
        'user' => $_SESSION['user']
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error interno del servidor'
    ]);
}

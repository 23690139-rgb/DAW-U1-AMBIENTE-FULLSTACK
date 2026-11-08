<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

requireRole(['ADMIN_SISTEMA']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, [
        'ok' => false,
        'message' => 'Método no permitido'
    ]);
}

$data = readJson();

$usuario = trim($data['usuario'] ?? '');
$nombre = trim($data['nombre'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$rol = $data['rol'] ?? 'CLIENTE';
$activo = $data['activo'] ?? 1;

if ($usuario === '' || $nombre === '' || $email === '' || $password === '') {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Todos los campos son obligatorios'
    ]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Correo electrónico no válido'
    ]);
}

if (strlen($password) < 8) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'La contraseña debe tener al menos 8 caracteres'
    ]);
}

$rolesPermitidos = [
    'ADMIN_SISTEMA',
    'ADMIN_SERVICIOS',
    'CLIENTE'
];

if (!in_array($rol, $rolesPermitidos, true)) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Rol no válido'
    ]);
}

if (!in_array($activo, [0, 1], true)) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Estado no válido'
    ]);
}

try {
    $pdo = getPDO();

    $check = $pdo->prepare(
        'SELECT id FROM usuarios WHERE usuario = ? OR email = ?'
    );
    $check->execute([$usuario, $email]);

    if ($check->fetch()) {
        jsonResponse(409, [
            'ok' => false,
            'message' => 'El usuario o correo ya existe'
        ]);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO usuarios
            (usuario, nombre, email, password_hash, rol, activo)
        VALUES
            (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $usuario,
        $nombre,
        $email,
        $hash,
        $rol,
        $activo
    ]);

    jsonResponse(201, [
        'ok' => true,
        'message' => 'Usuario registrado correctamente',
        'id' => (int) $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    jsonResponse(500, [
        'ok' => false,
        'message' => 'Error al registrar el usuario'
    ]);
}

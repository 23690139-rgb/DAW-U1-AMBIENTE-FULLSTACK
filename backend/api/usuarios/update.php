<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

$currentUser = requireRole(['ADMIN_SISTEMA']);

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    jsonResponse(405, [
        'ok' => false,
        'message' => 'Método no permitido'
    ]);
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'ID no válido'
    ]);
}

$data = readJson();

$usuario = trim($data['usuario'] ?? '');
$nombre = trim($data['nombre'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$rol = $data['rol'] ?? '';
$activo = $data['activo'] ?? null;

if ($usuario === '' || $nombre === '' || $email === '') {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Usuario, nombre y correo son obligatorios'
    ]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Correo electrónico no válido'
    ]);
}

if ($password !== '' && strlen($password) < 8) {
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

if ((int) $currentUser['id'] === $id && $activo === 0) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'No puedes desactivar tu propio usuario'
    ]);
}

try {
    $pdo = getPDO();

    $check = $pdo->prepare("
        SELECT id
        FROM usuarios
        WHERE (usuario = ? OR email = ?)
          AND id <> ?
    ");

    $check->execute([$usuario, $email, $id]);

    if ($check->fetch()) {
        jsonResponse(409, [
            'ok' => false,
            'message' => 'El usuario o correo ya existe'
        ]);
    }

    $checkUser = $pdo->prepare(
        'SELECT id FROM usuarios WHERE id = ?'
    );
    $checkUser->execute([$id]);

    if (!$checkUser->fetch()) {
        jsonResponse(404, [
            'ok' => false,
            'message' => 'Usuario no encontrado'
        ]);
    }

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET usuario = ?, nombre = ?, email = ?,
                password_hash = ?, rol = ?, activo = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $usuario,
            $nombre,
            $email,
            $hash,
            $rol,
            $activo,
            $id
        ]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET usuario = ?, nombre = ?, email = ?,
                rol = ?, activo = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $usuario,
            $nombre,
            $email,
            $rol,
            $activo,
            $id
        ]);
    }

    jsonResponse(200, [
        'ok' => true,
        'message' => 'Usuario actualizado correctamente'
    ]);

} catch (PDOException $e) {
    jsonResponse(500, [
        'ok' => false,
        'message' => 'Error al actualizar el usuario'
    ]);
}

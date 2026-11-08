<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

requireRole(['ADMIN_SISTEMA', 'ADMIN_SERVICIOS']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, [
        'ok' => false,
        'message' => 'Método no permitido'
    ]);
}

$data = readJson();

$nombre = trim($data['nombre'] ?? '');
$descripcion = trim($data['descripcion'] ?? '');
$precio = $data['precio'] ?? null;
$duracion = $data['duracion_minutos'] ?? null;
$activo = $data['activo'] ?? 1;

if ($nombre === '' || $descripcion === '' || $precio === null || $duracion === null) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Todos los campos son obligatorios'
    ]);
}

if (!is_numeric($precio) || (float) $precio < 0) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Precio no válido'
    ]);
}

if (filter_var($duracion, FILTER_VALIDATE_INT) === false || (int) $duracion <= 0) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'Duración no válida'
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

    $stmt = $pdo->prepare("
        INSERT INTO servicios
            (nombre, descripcion, precio, duracion_minutos, activo)
        VALUES
            (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $nombre,
        $descripcion,
        $precio,
        $duracion,
        $activo
    ]);

    jsonResponse(201, [
        'ok' => true,
        'message' => 'Servicio registrado correctamente',
        'id' => (int) $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    jsonResponse(500, [
        'ok' => false,
        'message' => 'Error al registrar el servicio'
    ]);
}

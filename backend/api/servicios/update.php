<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

requireRole(['ADMIN_SISTEMA', 'ADMIN_SERVICIOS']);

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

$nombre = trim($data['nombre'] ?? '');
$descripcion = trim($data['descripcion'] ?? '');
$precio = $data['precio'] ?? null;
$duracion = $data['duracion_minutos'] ?? null;
$activo = $data['activo'] ?? null;

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

    $check = $pdo->prepare(
        'SELECT id FROM servicios WHERE id = ?'
    );
    $check->execute([$id]);

    if (!$check->fetch()) {
        jsonResponse(404, [
            'ok' => false,
            'message' => 'Servicio no encontrado'
        ]);
    }

    $stmt = $pdo->prepare("
        UPDATE servicios
        SET nombre = ?,
            descripcion = ?,
            precio = ?,
            duracion_minutos = ?,
            activo = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $nombre,
        $descripcion,
        $precio,
        $duracion,
        $activo,
        $id
    ]);

    jsonResponse(200, [
        'ok' => true,
        'message' => 'Servicio actualizado correctamente'
    ]);

} catch (PDOException $e) {
    jsonResponse(500, [
        'ok' => false,
        'message' => 'Error al actualizar el servicio'
    ]);
}

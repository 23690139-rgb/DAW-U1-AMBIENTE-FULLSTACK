<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

requireRole(['ADMIN_SISTEMA', 'ADMIN_SERVICIOS']);

if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
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
$activo = $data['activo'] ?? null;

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
        SET activo = ?
        WHERE id = ?
    ");

    $stmt->execute([$activo, $id]);

    jsonResponse(200, [
        'ok' => true,
        'message' => $activo === 1
            ? 'Servicio activado correctamente'
            : 'Servicio desactivado correctamente'
    ]);

} catch (PDOException $e) {
    jsonResponse(500, [
        'ok' => false,
        'message' => 'Error al cambiar el estado del servicio'
    ]);
}

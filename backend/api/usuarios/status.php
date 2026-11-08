<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/request.php';
require_once __DIR__ . '/../../helpers/auth.php';

$currentUser = requireRole(['ADMIN_SISTEMA']);

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

if ((int) $currentUser['id'] === $id && $activo === 0) {
    jsonResponse(400, [
        'ok' => false,
        'message' => 'No puedes desactivar tu propio usuario'
    ]);
}

try {
    $pdo = getPDO();

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT id, rol, activo
        FROM usuarios
        WHERE id = ?
        FOR UPDATE
    ");

    $stmt->execute([$id]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$target) {
        $pdo->rollBack();

        jsonResponse(404, [
            'ok' => false,
            'message' => 'Usuario no encontrado'
        ]);
    }

    if ($activo === 0 && $target['rol'] === 'ADMIN_SISTEMA' && (int) $target['activo'] === 1) {
        $stmt = $pdo->query("
            SELECT COUNT(*)
            FROM usuarios
            WHERE rol = 'ADMIN_SISTEMA'
              AND activo = 1
        ");

        $adminActivos = (int) $stmt->fetchColumn();

        if ($adminActivos <= 1) {
            $pdo->rollBack();

            jsonResponse(400, [
                'ok' => false,
                'message' => 'No puedes desactivar al último administrador del sistema'
            ]);
        }
    }

    $update = $pdo->prepare("
        UPDATE usuarios
        SET activo = ?
        WHERE id = ?
    ");

    $update->execute([$activo, $id]);

    $pdo->commit();

    jsonResponse(200, [
        'ok' => true,
        'message' => $activo === 1
            ? 'Usuario activado correctamente'
            : 'Usuario desactivado correctamente'
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    jsonResponse(500, [
        'ok' => false,
        'message' => 'Error al cambiar el estado del usuario'
    ]);
}

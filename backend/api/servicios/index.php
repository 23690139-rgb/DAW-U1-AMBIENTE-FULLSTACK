<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/auth.php';

requireAuth();

try {
    $pdo = getPDO();

    $stmt = $pdo->query("
        SELECT
            id,
            nombre,
            descripcion,
            precio,
            duracion_minutos,
            activo,
            created_at,
            updated_at
        FROM servicios
        ORDER BY id DESC
    ");

    jsonResponse(200, [
        'ok' => true,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);

} catch (PDOException $e) {
    jsonResponse(500, [
        'ok' => false,
        'message' => 'Error al consultar los servicios'
    ]);
}

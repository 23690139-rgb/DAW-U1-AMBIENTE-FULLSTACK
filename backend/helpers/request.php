<?php
declare(strict_types=1);

require_once __DIR__ . '/response.php';

function readJson(): array {
    try {
        $data = json_decode(
            file_get_contents('php://input'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($data)) {
            jsonResponse(400, [
                'ok' => false,
                'message' => 'JSON no válido'
            ]);
        }

        return $data;

    } catch (JsonException $e) {
        jsonResponse(400, [
            'ok' => false,
            'message' => 'JSON no válido'
        ]);
    }
}

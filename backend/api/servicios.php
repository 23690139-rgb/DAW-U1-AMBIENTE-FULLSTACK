<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(401);

    echo json_encode([
        'ok' => false,
        'message' => 'Acceso no autorizado'
    ]);

    exit;
}

$servicios = [
    [
        'id' => 1,
        'nombre' => 'Desarrollo Web',
        'descripcion' => 'Desarrollo de aplicaciones web empresariales.',
        'precio' => 8500
    ],
    [
        'id' => 2,
        'nombre' => 'Soporte Tecnico',
        'descripcion' => 'Servicio de soporte para infraestructura tecnologica.',
        'precio' => 3500
    ],
    [
        'id' => 3,
        'nombre' => 'Consultar',
        'descripcion' => 'Analisis y asesori­a para proyectos tecnologicos.',
        'precio' => 5000
    ]
];

echo json_encode($servicios);

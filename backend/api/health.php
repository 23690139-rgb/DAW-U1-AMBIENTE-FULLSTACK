<?php
header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'message' => 'El backend PHP funciona correctamente',
    'service' => 'Backend API'
]);



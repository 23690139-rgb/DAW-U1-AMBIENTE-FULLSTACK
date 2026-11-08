<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getPDO();

    $usuario = 'alumno';
    $nombre = 'Alumno de prueba';
    $password = 'alumno2026';
    $rol = 'Usuario';

    // Generar hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar usuario en la base de datos
    $sql = $pdo->prepare(
        'INSERT INTO usuarios
        (usuario, nombre, password_hash, rol, activo)
        VALUES
        (:usuario, :nombre, :password_hash, :rol, 1)'
    );

    $sql->execute([
        ':usuario' => $usuario,
        ':nombre' => $nombre,
        ':password_hash' => $passwordHash,
        ':rol' => $rol
    ]);

    echo "Usuario alumno creado correctamente.\n";

} catch (Throwable $e) {
    echo "Error al crear el usuario alumno.\n";
}

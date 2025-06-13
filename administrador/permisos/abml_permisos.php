<?php
session_start();
require '../conexion.php';

// Validar ID de usuario
if (!isset($_POST['id_usuario']) || !is_numeric($_POST['id_usuario'])) {
    exit("Error: ID de usuario no válido.");
}

$id_usuario = (int) $_POST['id_usuario'];
$nuevos_permisos = $_POST['permisos'] ?? [];

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // Eliminar permisos actuales
    if (!$conexion->query("DELETE FROM permiso_usuarios WHERE id_usuario = $id_usuario")) {
        throw new Exception("Error al eliminar permisos existentes: " . $conexion->error);
    }

    // Preparar inserción
    $stmt = $conexion->prepare("INSERT INTO permiso_usuarios (id_usuario, id_permiso, fecha) VALUES (?, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Error al preparar la inserción: " . $conexion->error);
    }

    foreach ($nuevos_permisos as $permiso) {
        $id_permiso = (int) $permiso;
        $stmt->bind_param("ii", $id_usuario, $id_permiso);
        if (!$stmt->execute()) {
            throw new Exception("Error al insertar permiso $id_permiso para usuario $id_usuario: " . $stmt->error);
        }
    }

    $stmt->close();
    $conexion->commit();

    header("Location: index.php?success=1");
    exit();

} catch (Exception $e) {
    $conexion->rollback();
    echo "<h3 style='color:red;'>Se produjo un error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='index.php'>Volver</a></p>";
    exit();
}

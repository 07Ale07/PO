<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');
session_start();

if (!isset($_GET['id'])) {
    echo '<script>alert("ID de vehículo no especificado"); window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";</script>';
    exit;
}

$id = intval($_GET['id']);

$consulta = $conexion->prepare("SELECT gama, precio FROM vehiculos WHERE id_vehiculo = ?");
$consulta->bind_param("i", $id);
$consulta->execute();
$consulta->store_result();

if ($consulta->num_rows === 0) {
    echo '<script>alert("Vehículo no encontrado"); window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";</script>';
    exit;
}

$consulta->bind_result($gama, $precio);
$consulta->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Vehículo</title>
</head>
<body>
    <h1>Modificar Vehículo</h1>
    <form action="procesar_modificar_vehiculo.php" method="POST">
        <input type="hidden" name="id_vehiculo" value="<?= htmlspecialchars($id) ?>">
        <label for="gama_vehiculo">Gama:</label>
        <input type="text" name="gama_vehiculo" id="gama_vehiculo" value="<?= htmlspecialchars($gama) ?>" required>
        <br>
        <label for="precio">Precio:</label>
        <input type="number" step="0.01" name="precio" id="precio" value="<?= htmlspecialchars($precio) ?>" required>
        <br>
        <button type="submit" name="modificar_vehiculo">Guardar Cambios</button>
    </form>
    <br>
    <a href="<?= BASE_URL ?>/administrador/vehiculos/vehiculos.php">Volver</a>
</body>
</html>

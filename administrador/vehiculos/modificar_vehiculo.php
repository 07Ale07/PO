<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');
session_start();

if (isset($_POST['modificar_vehiculo'])) {
    $id = intval($_POST['id_vehiculo']);
    $gama_vehiculo = $_POST['gama_vehiculo'];
    $precio = floatval($_POST['precio']);

    $actualizar = $conexion->prepare("UPDATE vehiculos SET gama = ?, precio = ? WHERE id_vehiculo = ?");
    $actualizar->bind_param("sdi", $gama_vehiculo, $precio, $id);

    if ($actualizar->execute()) {
        echo '<script>alert("Vehículo modificado correctamente"); window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";</script>';
    } else {
        echo '<script>alert("Error al modificar el vehículo"); window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";</script>';
    }
} else {
    echo '<script>alert("Petición no válida"); window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";</script>';
}
?>

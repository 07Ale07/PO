<?php
require '../../variable_global.php';
require_once(ROOT_PATH . '/administrador/conexion.php');
session_start();

function tienePermisoActivar($conexion) {
    if (!isset($_SESSION['id_usuario_adm'])) {
        return false;
    }

    $id_usuario = $_SESSION['id_usuario_adm'];
    $resultado = $conexion->query("SELECT * FROM permiso_usuarios WHERE id_usuario = $id_usuario AND id_permiso = 3 LIMIT 1");

    return $resultado && $resultado->num_rows > 0;
}

if (!tienePermisoActivar($conexion)) {
    echo "<script>alert('No tenés permiso para activar ni desactivar'); window.history.back();</script>";
    exit;
}

if (isset($_GET['id'])) {
    $id_vehiculo = (int)$_GET['id'];

    // Obtener estado actual
    $consulta_estado = "SELECT activo FROM vehiculos WHERE id_vehiculo = $id_vehiculo";
    $resultado = $conexion->query($consulta_estado);
    $fila = $resultado->fetch_assoc();

    if ($fila) {
        $nuevo_estado = ($fila['activo'] == 1) ? 0 : 1;
        $actualizar_estado = "UPDATE vehiculos SET activo = $nuevo_estado WHERE id_vehiculo = $id_vehiculo";
        $conexion->query($actualizar_estado);

        $mensaje = ($nuevo_estado == 1) ? "activado" : "desactivado";

        echo '<script>
            alert("El vehículo ha sido ' . $mensaje . ' correctamente");
            window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";
        </script>';
    } else {
        echo '<script>
            alert("Vehículo no encontrado");
            window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";
        </script>';
    }
} else {
    echo '<script>
        alert("ID de vehículo no proporcionado");
        window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";
    </script>';
}
?>

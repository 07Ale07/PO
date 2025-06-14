<?php
require '../conexion.php';
session_start();
function puedeAgregar($conexion) {
    if (!isset($_SESSION['id_usuario_adm'])) {
        return false;
    }

    $id_usuario = $_SESSION['id_usuario_adm'];

    $resultado = $conexion->query("SELECT * FROM permiso_usuarios WHERE id_usuario = $id_usuario AND id_permiso = 3 LIMIT 1");

    return $resultado && $resultado->num_rows > 0;
}


if (!puedeAgregar($conexion)) {
    echo "<script>alert('no tenés permiso para activar ni desactivar'); window.history.back();</script>";
    exit;
}else{
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');

if (isset($_GET['id'])) {
    $id_paquete = (int)$_GET['id'];

    // Obtener estado actual
    $consulta_estado = "SELECT activo FROM paquetes WHERE id_paquete = $id_paquete";
    $resultado = $conexion->query($consulta_estado);
    $fila = $resultado->fetch_assoc();

    if ($fila) {
        $nuevo_estado = ($fila['activo'] == 1) ? 0 : 1;
        $actualizar_estado = "UPDATE paquetes SET activo = $nuevo_estado WHERE id_paquete = $id_paquete";
        $conexion->query($actualizar_estado);

        $mensaje = ($nuevo_estado == 1) ? "activado" : "desactivado";

        echo '<script language="javascript">
            alert("El paquete ha sido ' . $mensaje . ' correctamente");
            window.location.href = "' . BASE_URL . '/administrador/paquetes/paquetes.php";
        </script>';
    } else {
        echo '<script language="javascript">
            alert("Paquete no encontrado");
            window.location.href = "' . BASE_URL . '/administrador/paquetes/paquetes.php";
        </script>';
    }
} else {
    echo '<script language="javascript">
        alert("ID de paquete no proporcionado");
        window.location.href = "' . BASE_URL . '/administrador/paquetes/paquetes.php";
    </script>';
}
}
?>

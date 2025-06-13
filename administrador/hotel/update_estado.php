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
    echo "<script>alert('no tenes permiso para cambiar el estado'); window.history.back();</script>";
    exit;
}else{


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_hotel = isset($_POST['id_hotel']) ? intval($_POST['id_hotel']) : 0;
    $estado = isset($_POST['estado']) ? intval($_POST['estado']) : 0;

    if ($id_hotel > 0 && ($estado === 0 || $estado === 1)) {
        $stmt = $conexion->prepare("UPDATE hoteles SET estado = ? WHERE id_hotel = ?");
        $stmt->bind_param("ii", $estado, $id_hotel);
        if ($stmt->execute()) {
            echo "OK";
        } else {
            echo "Error al actualizar";
        }
        $stmt->close();
    } else {
        echo "Datos inválidos";
    }
} else {
    echo "Método no permitido";
}

$conexion->close();
}
?>

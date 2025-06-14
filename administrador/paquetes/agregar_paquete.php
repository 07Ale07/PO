<?php
session_start();
function puedeAgregar($conexion) {
    if (!isset($_SESSION['id_usuario_adm'])) {
        return false;
    }

    $id_usuario = $_SESSION['id_usuario_adm'];

    $resultado = $conexion->query("SELECT * FROM permiso_usuarios WHERE id_usuario = $id_usuario AND id_permiso = 1 LIMIT 1");

    return $resultado && $resultado->num_rows > 0;
}


if (!puedeAgregar($conexion)) {
    echo "<script>alert('no tenés permiso para Agregar'); window.history.back();</script>";
    exit;
}else{
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que se recibieron todos los campos
    if (
        isset($_POST['id_categoria'], $_POST['id_vuelo'], $_POST['id_hotel_estadia'], $_POST['id_vehiculo'], $_POST['activo'])
    ) {
        $id_categoria = (int) $_POST['id_categoria'];
        $id_vuelo = (int) $_POST['id_vuelo'];
        $id_hotel_estadia = (int) $_POST['id_hotel_estadia'];
        $id_vehiculo = (int) $_POST['id_vehiculo'];
        $activo = (int) $_POST['activo']; // Asegúrate que venga como 0 o 1

        $query = "INSERT INTO paquetes (id_categoria, id_vuelo, id_hotel_estadia, id_vehiculo, activo) 
                  VALUES ($id_categoria, $id_vuelo, $id_hotel_estadia, $id_vehiculo, $activo)";

        if ($conexion->query($query)) {
            header("Location: " . BASE_URL . "/administrador/paquetes/paquetes.php?success=1");
            exit();
        } else {
            die("Error al guardar el paquete: " . $conexion->error);
        }
    } else {
        die("Faltan campos en el formulario.");
    }
} else {
    header("Location: " . BASE_URL . "/administrador/paquetes/agregar_paquete.php");
    exit();
}
}


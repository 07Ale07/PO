<?php
session_start();
function puedeAgregar($conexion) {
    if (!isset($_SESSION['id_usuario_adm'])) {
        return false;
    }

    $id_usuario = $_SESSION['id_usuario_adm'];

    $resultado = $conexion->query("SELECT * FROM permiso_usuarios WHERE id_usuario = $id_usuario AND id_permiso = 2 LIMIT 1");

    return $resultado && $resultado->num_rows > 0;
}


if (!puedeAgregar($conexion)) {
    echo "<script>alert('no tenés permiso para Modificar'); window.history.back();</script>";
    exit;
}else{
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['id_paquete'], $_POST['id_categoria'], $_POST['id_vuelo'])
    ) {
        $id_paquete = (int) $_POST['id_paquete'];
        $id_categoria = (int) $_POST['id_categoria'];
        $id_vuelo = (int) $_POST['id_vuelo'];
        $id_hotel_estadia = (int) $_POST['id_hotel_estadia'];
        $id_vehiculo = (int) $_POST['id_vehiculo'];

        $query = "UPDATE paquetes 
                  SET id_categoria = $id_categoria, 
                      id_vuelo = $id_vuelo, 
                      id_hotel_estadia = $id_hotel_estadia, 
                      id_vehiculo = $id_vehiculo
                  WHERE id_paquete = $id_paquete";

        if ($conexion->query($query)) {
            header("Location: " . BASE_URL . "/administrador/paquetes/paquetes.php?updated=1");
            exit();
        } else {
            die("Error al actualizar el paquete: " . $conexion->error);
        }
    } else {
        die("Faltan campos obligatorios.");
    }
} else {
    header("Location: " . BASE_URL . "/administrador/paquetes/paquetes.php");
    exit();
}
}

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');
session_start();

// Validación de entrada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_paquete = isset($_POST['id_paquete']) ? (int)$_POST['id_paquete'] : 0;
    $id_categoria = isset($_POST['id_categoria']) ? (int)$_POST['id_categoria'] : 0;
    $id_vuelo = isset($_POST['id_vuelo']) ? (int)$_POST['id_vuelo'] : 0;
    $id_hotel_estadia = isset($_POST['id_hotel_estadia']) ? (int)$_POST['id_hotel_estadia'] : 0;
    $id_vehiculo = isset($_POST['id_vehiculo']) ? (int)$_POST['id_vehiculo'] : 0;
    $id_actividad = isset($_POST['id_actividad']) ? (int)$_POST['id_actividad'] : 0;

    // Verifica que todos los campos estén completos
    if ($id_paquete > 0 && $id_categoria > 0 && $id_vuelo > 0 && $id_hotel_estadia > 0 && $id_vehiculo > 0) {
        $query = "UPDATE paquetes 
                  SET id_categoria = ?, 
                      id_vuelo = ?, 
                      id_hotel_estadia = ?, 
                      id_vehiculo = ? 
                  WHERE id_paquete = ?";

        $stmt = $conexion->prepare($query);
        if ($stmt) {
            $stmt->bind_param("iiiii",$id_categoria, $id_vuelo, $id_hotel_estadia, $id_vehiculo, $id_paquete);
            if ($stmt->execute()) {
                // Redirigir con éxito
                header("Location: paquetes.php?mensaje=modificado");
                exit();
            } else {
                echo "Error al ejecutar la consulta: " . $stmt->error;
            }
        } else {
            echo "Error al preparar la consulta: " . $conexion->error;
        }
    } else {
        echo "Todos los campos son obligatorios.";
    }
} else {
    echo "Acceso no autorizado.";
}
?>

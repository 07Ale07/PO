<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');
session_start();

$gama_vehiculo = $_POST['gama_vehiculo'];
$precio = $_POST['precio'];


if(isset($_POST['subir_new_vehiculo'])){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $insertar_new_vehiculo = $conexion->prepare('INSERT INTO vehiculos(gama, precio,activo) VALUES (?,?,true)');
        $insertar_new_vehiculo->bind_param("sd",$gama_vehiculo,$precio);
        $insertar_new_vehiculo->execute();

        echo '<script language="javascript">
            alert("nuevo vehiculo agregado");
            window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";
        </script>';

    }else{
        echo '<script language="javascript">
            alert("error en la peticion post");
            window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";
        </script>';
    }
}else{
    echo '<script language="javascript">
            alert("error al enviar el formulario");
            window.location.href = "' . BASE_URL . '/administrador/vehiculos/vehiculos.php";
        </script>';
}
?>

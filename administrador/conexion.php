<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_de_datos = "turismo";

$conexion = new mysqli($servidor, $usuario, $password, $base_de_datos);

if ($conexion->connect_errno) {
    echo "No se pudo realizar la conexión: (" 
    . $conexion->connect_errno . ") " . $conexion->connect_error;
}
?>

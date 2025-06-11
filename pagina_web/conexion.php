<?php
// conexion.php

$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_de_datos = "turismo"; // Reemplaza con el nombre real de tu base de datos

$enlace = mysqli_connect($host, $usuario, $contrasena, $base_de_datos);

if (!$enlace) {
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

// Opcional: establecer codificación UTF-8
mysqli_set_charset($enlace, "utf8");
?>

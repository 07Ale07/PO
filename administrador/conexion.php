<?php
$servidor="localhost";
$usuario="root";
$password="";
$base_de_datos="turismo";
$mysqli= new mysqli($servidor, $usuario, $password, $base_de_datos);
if ($mysqli->connect_errno) {
    echo "No se pudo realizar la conexión: (" 
    . $mysqli->connect_errno . ") " . $mysqli->connect_errno;
}//else{
//      echo "se conecto a ". $base_de_datos;
//  }
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');
session_start();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Agregar Vehiculo</h1>
    <form action="<?= BASE_URL ?>/administrador/vehiculos/nuevo_vehiculo.php" method = "post">
        <table>
            <tr>
                <td><label for="">Gama</label></td>
                <td><input type="text" name = "gama_vehiculo"></td>
            </tr>
            <tr>
                <td><label for="">Precio</label></td>
                <td><input type="number" name = "precio"></td>
            </tr>
            <tr>
                <td><input type="submit" name = "subir_new_vehiculo"></td>
            </tr>
        </table>
    </form>

    
</body>
</html>
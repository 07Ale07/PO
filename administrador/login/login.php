<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    require_once('../../variable_global.php');
    require_once(ROOT_PATH . '/administrador/conexion.php');
    
    ?>
    <h1>Inicie sesion con su cuenta</h1>
    <form action="<?= BASE_URL ?>/administrador/login/iniciar_sesion.php" method = "post">
        <table border = "1">
            <tr>
                <td><label for="nombre">Ingrese su nombre</label></td>
                <td><input type="text" name = "nombre_usuario"></td>
            </tr>
            <tr>
                <td><label for="contrasena">Ingrese su contraseña</label></td>
                <td><input type="password" name = "contrasena"></td>
            </tr>
            <tr>
                <td><input type="submit" name = "iniciar_sesion"></td>
            </tr>
            
        </table>

        <h3>No tienes cuenta?
            Create una
        </h3>
        <a href="<?= BASE_URL ?>/administrador/login/agregar_nuevo_usuario.php">Crear cuenta</a>
    </form>
    
</body>
</html>
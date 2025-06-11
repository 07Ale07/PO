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
    ?>
    <h1>Cree un nuevo usuario</h1>
    <form action="<?= BASE_URL ?>/administrador/login/crear_usuario.php" method = "post">
        <table border = "1">
            <tr>
                <td><label for="name">Nombre Usuario</label></td>
                <td><input type="text" name = "nombre_usuario"></td>
            </tr>
            <tr>
                <td><label for="contrasena">Contraseña</label></td>
                <td><input type="password" name = "contrasena"></td>
            </tr>
            <tr>
                <td><label for="correo_electronico">Correo Electronico</label></td>
                <td><input type="email" name = "correo_electronico"></td>
            </tr>
            <tr>
                <td><input type="submit" name = "subir_nuevo_usuario"></td>
            </tr>
        </table>
    </form>
    
</body>
</html>
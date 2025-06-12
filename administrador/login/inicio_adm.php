<?php
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
    <?php
    require_once('../../variable_global.php');
    ?>
    <h1>Hola admin</h1>
    <nav>
        <div>
            <a href="<?= BASE_URL ?>/administrador/login/inicio_adm.php">Inicio Admin</a>
            <a href="<?= BASE_URL ?>/administrador/logouts/ver_logous.php">Historial de logueos</a>
            
        </div>
    </nav>
    <form action="<?= BASE_URL ?>/administrador/logouts/cerrar_sesion.php" method="POST">
    <input type="hidden" name = "cerrar_sesion" value = "1">
    <button type="submit">
        
        Cerrar sesión
    </button>
    </form>

    
</body>
</html>
<?php
session_start();
require_once('../../variable_global.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Logouts</h1>
    <nav>
        <div>
            <a href="<?= BASE_URL ?>/administrador/logouts/ver_logous.php">Historial de logueos</a>
            <a href="<?= BASE_URL ?>/administrador/login/inicio_adm.php">Inicio Admin</a>
        </div>
    </nav>
    <br>

    <?php
    require_once(ROOT_PATH . '/administrador/conexion.php');
    $buscar_logeos = "SELECT historial_logeos.id_historial_logeos, usuarios.usuario, historial_logeos.fecha_inicio, historial_logeos.fecha_cerro FROM historial_logeos
    INNER JOIN usuarios ON historial_logeos.id_usuario = usuarios.id_usuario";
    $ejecutar_logues = $mysqli->query($buscar_logeos);

    if($ejecutar_logues->num_rows > 0){
        echo "<table>
                <tr>
                    <th>id historial</th>
                    <th>Nombre</th>
                    <th>Fecha inicio</th>
                    <th>Fecha cerro</th>
                </tr>";
        
        while($bucle = $ejecutar_logues->fetch_assoc()){
            echo "<tr>
                    <td>" . htmlspecialchars($row['id_historial_logeos']) . "</td>
                    <td>" . htmlspecialchars($row['usuario']) . "</td>
                    <td>" . htmlspecialchars($row['fecha_inicio']) . "</td>
                    <td>" . htmlspecialchars($row['fecha_cerro']) . "</td>
            ";

        }
        echo "</table>";
    }else{
        echo "<p>No se encontraron obras sociales.</p>";
    }
    ?>

    <a href="<?= BASE_URL ?>/administrador/logouts/cerrar_sesion.php">Cerrar sesion</a>
    
</body>
</html>
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
    <h1>Vehiculos</h1>
    <?php
    $traer_vehiculos = "SELECT id_vehiculo, gama, precio,activo FROM vehiculos";
    $ejecutar_consulta_vehiculos = $conexion->query($traer_vehiculos);

    if ($ejecutar_consulta_vehiculos && $ejecutar_consulta_vehiculos->num_rows > 0) {
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>ID vehiculo</th>
                        <th>Gama</th>
                        <th>Precio</th>
                        <th>Activo</th>
                        <th>Modificar</th>
                        <th>Modificar Actividad</th>
                    </tr>
                </thead>
                <tbody>";
        
        while($bucle = $ejecutar_consulta_vehiculos->fetch_assoc()){
            echo "<tr>
                    <td>{$bucle['id_vehiculo']}</td>
                    <td>{$bucle['gama']}</td>
                    <td>{$bucle['precio']}</td>
                    ";
                    if($bucle['activo'] == 1){
                            echo"<td>Activo</td>";
                        }elseif($bucle['activo'] == 0){
                            echo"<td>Inactivo</td>";
                        }else{
                            echo"<td>Hubo un fallo trayecto su activo</td>";
                        }
                    echo "
                    <td><a href='" . BASE_URL . "/administrador/vehiculos/formulario_modificar_vehiculo.php?id={$bucle['id_vehiculo']}'>Modificar</a></td>";

                    // Botón activar/desactivar
                    echo "<td><a href='" . BASE_URL . "/administrador/vehiculos/activar_desactivar_vehiculo.php?id={$bucle['id_vehiculo']}'>" . 
                    ($bucle['activo'] == 1 ? 'Desactivar' : 'Activar') . "</a></td>
                </tr>";

        }
        echo "</tbody></table>";
        echo "<br><a href='" . BASE_URL . "/administrador/vehiculos/formulario_agregar_vehiculo.php' class='add-btn'>Agregar nuevo vehiculo</a>";
    }else{
        echo '<script language="javascript">
            alert("No hay vehiculos disponibles");
            window.location.href = "' . BASE_URL . '/vista/vista_login/vista_login.php";
        </script>';
    }

    ?>
    
</body>
</html>
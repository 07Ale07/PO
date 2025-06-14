<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paquetes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="p.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../../variable_global.php');
    require_once(ROOT_PATH . '/administrador/conexion.php');
    session_start();

    ?>
     <div class="flex space-x-1 md:space-x-6">
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Vuelos</a>
                    <a href="../hotel/hoteles.php" class="menu-item px-2 py-1 text-gray-800 font-medium">Hoteles</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Paquetes</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Autos</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Asistencias</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium text-blue-600">Actividades</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Micros</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Blog</a>

                    <div class="mt-4 md:mt-0">
                    <form action="<?= BASE_URL ?>/administrador/logouts/cerrar_sesion.php" method="POST">
                        <input type="hidden" name="cerrar_sesion" value="1">
                        <button type="submit" class="flex items-center text-red-600 hover:text-red-800 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                        </button>
                    </form>
                    </div>
                    
    </div>

    <?php
    $traer_paquetes = "
    SELECT paquetes.id_paquete, 
           categoria_paquetes.categoria,
           paises.nombre_pais,
           ciudades.nombre_ciudad,
           hoteles.hotel,
           hotel_estadias.estadia,
           hotel_estadias.habitacion,
           tiempo.tiempo,
           vehiculos.gama,
           paquetes.activo
    FROM paquetes
    INNER JOIN categoria_paquetes ON categoria_paquetes.id_categoria = paquetes.id_categoria
    INNER JOIN vuelos ON vuelos.id_vuelo = paquetes.id_vuelo
    INNER JOIN ciudades ON ciudades.id_ciudad = vuelos.id_ciudad_destino
    INNER JOIN paises ON paises.id_pais = ciudades.id_pais
    INNER JOIN hotel_estadias ON hotel_estadias.id_hotel_estadia = paquetes.id_hotel_estadia
    INNER JOIN hoteles ON hoteles.id_hotel = hotel_estadias.id_hotel
    INNER JOIN tiempo ON hotel_estadias.id_tiempo = tiempo.id_tiempo
    INNER JOIN vehiculos ON vehiculos.id_vehiculo = paquetes.id_vehiculo";

    $ejecutar_consulta = $conexion->query($traer_paquetes);

    if ($ejecutar_consulta && $ejecutar_consulta->num_rows > 0) {
        echo "<table border='1'>
                <thead>
                    <tr>
                        <th>ID Paquete</th>
                        <th>Categoría</th>
                        <th>País</th>
                        <th>Ciudad</th>
                        <th>Hotel</th>
                        <th>Estadía</th>
                        <th>Habitación</th>
                        <th>Tiempo</th>
                        <th>Vehículo</th>
                        <th>Estado</th>
                        <th>Detalle</th>
                        <th>Modificar</th>
                        <th>Activar/Desactivar</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $ejecutar_consulta->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id_paquete']}</td>
                    <td>{$row['categoria']}</td>
                    <td>{$row['nombre_pais']}</td>
                    <td>{$row['nombre_ciudad']}</td>
                    <td>{$row['hotel']}</td>
                    <td>{$row['estadia']}</td>
                    <td>{$row['habitacion']}</td>
                    <td>{$row['tiempo']}</td>
                    <td>{$row['gama']}</td>
                    <td>" . ($row['activo'] == 1 ? 'Activo' : 'Inactivo') . "</td>
                    <td><a href='" . BASE_URL . "/administrador/paquetes/detalle_paquete.php?id={$row['id_paquete']}'>Detalle</a></td>
                    <td><a href='" . BASE_URL . "/administrador/paquetes/formulario_modificar_paquete.php?id={$row['id_paquete']}'>Modificar</a></td>
                    <td><a href='" . BASE_URL . "/administrador/paquetes/eliminar_paquete.php?id={$row['id_paquete']}'>" . 
                        ($row['activo'] == 1 ? 'Desactivar' : 'Activar') . "</a></td>
                </tr>";
        }

        echo "</tbody></table>";

        echo "<br><a href='" . BASE_URL . "/administrador/paquetes/formulario_agregar_paquete.php' class='add-btn'>Agregar nuevo paquete</a>";
    } else {
        echo '<script language="javascript">
            alert("No hay paquetes disponibles");
            window.location.href = "' . BASE_URL . '/vista/vista_login/vista_login.php";
        </script>';
    }
    ?>
</body>
</html>

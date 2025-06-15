<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Procesar agregar al carrito desde GET
if (isset($_GET['agregar_paquete'])) {
    $id_paquete = (int)$_GET['agregar_paquete'];

    // Consulta para obtener toda la info del paquete
    $sql = "
        SELECT 
    paquetes.id_paquete,
    ciudades.nombre_ciudad AS destino,
    hoteles.hotel AS nombre_hotel,
    vehiculos.gama AS vehiculo,
    tiempo.tiempo,
    hotel_estadias.estadia,
    vuelos.precio AS precio_vuelo,
    vehiculos.precio AS precio_vehiculo,
    hotel_estadias.precio AS precio_hotel

        FROM paquetes
        INNER JOIN vuelos ON vuelos.id_vuelo = paquetes.id_vuelo
        INNER JOIN ciudades ON ciudades.id_ciudad = vuelos.id_ciudad_destino
        INNER JOIN hotel_estadias ON hotel_estadias.id_hotel_estadia = paquetes.id_hotel_estadia
        INNER JOIN hoteles ON hoteles.id_hotel = hotel_estadias.id_hotel
        INNER JOIN tiempo ON tiempo.id_tiempo = hotel_estadias.id_tiempo
        INNER JOIN vehiculos ON vehiculos.id_vehiculo = paquetes.id_vehiculo
        WHERE paquetes.id_paquete = $id_paquete
        LIMIT 1
    ";
    
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $paquete = $resultado->fetch_assoc();

        $precio_total = $paquete['precio_vuelo'] + $paquete['precio_vehiculo'] + $paquete['precio_hotel'];

        $item_carrito = [
            'id_paquete'   => $paquete['id_paquete'],
            'destino'      => $paquete['destino'],
            'nombre_hotel' => $paquete['nombre_hotel'],
            'vehiculo'     => $paquete['vehiculo'],
            'tiempo'       => $paquete['tiempo'],
            'estadia'       => $paquete['estadia'],
            'precio'       => $precio_total,
            'cantidad'     => 1
        ];

        $existe = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id_paquete'] === $item_carrito['id_paquete']) {
                $item['cantidad'] += 1;
                $existe = true;
                break;
            }
        }

        if (!$existe) {
            $_SESSION['carrito'][] = $item_carrito;
        }
    }

    header("Location: vista_paquetes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Paquetes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="p.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../carrito/carrito.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Menú superior -->
<div class="flex space-x-1 md:space-x-6">
    <!-- ... tus links de navegación ... -->
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
// Consulta de paquetes
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
    echo "<table border='1' class='table-auto w-full text-sm text-left text-gray-700 mt-4'>
            <thead class='bg-gray-200'>
                <tr>
                    <th>Categoría</th>
                    <th>País</th>
                    <th>Ciudad</th>
                    <th>Hotel</th>
                    <th>Estadía</th>
                    <th>Habitación</th>
                    <th>Tiempo</th>
                    <th>Vehículo</th>
                    <th>Agregar</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $ejecutar_consulta->fetch_assoc()) {
        echo "<tr>
                <td>{$row['categoria']}</td>
                <td>{$row['nombre_pais']}</td>
                <td>{$row['nombre_ciudad']}</td>
                <td>{$row['hotel']}</td>
                <td>{$row['estadia']}</td>
                <td>{$row['habitacion']}</td>
                <td>{$row['tiempo']}</td>
                <td>{$row['gama']}</td>
                <td><a href='?agregar_paquete={$row['id_paquete']}' class='text-blue-600 hover:underline'>Agregar</a></td>
                <td><a href='" . BASE_URL . "/administrador/paquetes/detalle_paquete.php?id={$row['id_paquete']}'>Detalle</a></td>
            </tr>";
    }

    echo "</tbody></table>";

} else {
    echo '<script language="javascript">
        alert("No hay paquetes disponibles");
        window.location.href = "' . BASE_URL . '/vista/vista_login/vista_login.php";
    </script>';
}
?>

<!-- Carrito -->
<?php include '../carrito/carrito.php'; ?>

</body>
</html>

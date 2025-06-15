<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');
session_start();

// Validar ID del paquete
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_URL . "/administrador/paquetes/paquetes.php");
    exit();
}

$id_paquete = (int)$_GET['id'];

// Obtener datos del paquete
$consulta_paquete = "SELECT * FROM paquetes WHERE id_paquete = ?";
$stmt = $conexion->prepare($consulta_paquete);
$stmt->bind_param("i", $id_paquete);
$stmt->execute();
$res_paquete = $stmt->get_result();

if ($res_paquete->num_rows === 0) {
    echo "<script>alert('Paquete no encontrado.'); window.location.href='paquetes.php';</script>";
    exit();
}

$paquete = $res_paquete->fetch_assoc();

// Obtener ciudad destino desde el vuelo
$sql_ciudad = "
    SELECT c.id_ciudad 
    FROM vuelos v
    JOIN ciudades c ON v.id_ciudad_destino = c.id_ciudad
    WHERE v.id_vuelo = ?
";
$stmt = $conexion->prepare($sql_ciudad);
$stmt->bind_param("i", $paquete['id_vuelo']);
$stmt->execute();
$res_ciudad = $stmt->get_result();
$id_ciudad_destino = $res_ciudad->fetch_assoc()['id_ciudad'] ?? null;

// Actividades disponibles
$sql_actividades = "
    SELECT a.id_actividad, a.actividad 
    FROM actividad a
    JOIN actividad_ciudad ac ON a.id_actividad = ac.id_actividad
    WHERE ac.id_ciudad = ? AND a.estado = 1
";
$stmt = $conexion->prepare($sql_actividades);
$stmt->bind_param("i", $id_ciudad_destino);
$stmt->execute();
$actividades = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Paquete #<?= $paquete['id_paquete'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-8">
        <h1 class="text-2xl font-bold mb-6">Editar Paquete #<?= $paquete['id_paquete'] ?></h1>
        <form action="actualizar_paquete.php" method="POST" class="space-y-6">
            <input type="hidden" name="id_paquete" value="<?= $paquete['id_paquete'] ?>">

            <!-- Categoría -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Categoría</label>
                <select name="id_categoria" required class="w-full border rounded px-3 py-2">
                    <?php
                    $res = $conexion->query("SELECT id_categoria, categoria FROM categoria_paquetes");
                    while ($row = $res->fetch_assoc()) {
                        $sel = ($row['id_categoria'] == $paquete['id_categoria']) ? 'selected' : '';
                        echo "<option value='{$row['id_categoria']}' $sel>{$row['categoria']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Vuelo -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Destino (Vuelo)</label>
                <select name="id_vuelo" required class="w-full border rounded px-3 py-2">
                    <?php
                    $res = $conexion->query("
                        SELECT v.id_vuelo, c.nombre_ciudad, p.nombre_pais 
                        FROM vuelos v 
                        JOIN ciudades c ON v.id_ciudad_destino = c.id_ciudad
                        JOIN paises p ON c.id_pais = p.id_pais
                    ");
                    while ($row = $res->fetch_assoc()) {
                        $sel = ($row['id_vuelo'] == $paquete['id_vuelo']) ? 'selected' : '';
                        echo "<option value='{$row['id_vuelo']}' $sel>{$row['nombre_pais']} - {$row['nombre_ciudad']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Hotel/Estadía -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Hotel y Estadía</label>
                <select name="id_hotel_estadia" required class="w-full border rounded px-3 py-2">
                    <?php
                    $res = $conexion->query("
                        SELECT he.id_hotel_estadia, h.hotel, he.estadia, t.tiempo 
                        FROM hotel_estadias he
                        JOIN hoteles h ON he.id_hotel = h.id_hotel
                        JOIN tiempo t ON he.id_tiempo = t.id_tiempo
                    ");
                    while ($row = $res->fetch_assoc()) {
                        $sel = ($row['id_hotel_estadia'] == $paquete['id_hotel_estadia']) ? 'selected' : '';
                        echo "<option value='{$row['id_hotel_estadia']}' $sel>{$row['hotel']} - {$row['estadia']}  {$row['tiempo']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Vehículo -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Vehículo</label>
                <select name="id_vehiculo" required class="w-full border rounded px-3 py-2">
                    <?php
                    $res = $conexion->query("SELECT id_vehiculo, gama FROM vehiculos");
                    while ($row = $res->fetch_assoc()) {
                        $sel = ($row['id_vehiculo'] == $paquete['id_vehiculo']) ? 'selected' : '';
                        echo "<option value='{$row['id_vehiculo']}' $sel>{$row['gama']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Actividad -->
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Actividad</label>
                <select name="id_actividad" required class="w-full border rounded px-3 py-2">
                    <option value="">Seleccione una actividad</option>
                    <?php while ($act = $actividades->fetch_assoc()): ?>
                        <option value="<?= $act['id_actividad'] ?>"><?= $act['actividad'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Botones -->
            <div class="flex justify-between items-center pt-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    Guardar Cambios
                </button>
                <a href="<?= BASE_URL ?>/administrador/paquetes/paquetes.php" class="text-red-600 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

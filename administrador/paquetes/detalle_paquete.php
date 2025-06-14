<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');

// Verificar si se recibió el ID del paquete
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_URL . "/administrador/paquetes/paquetes.php");
    exit();
}

$id_paquete = (int)$_GET['id'];

// Consulta para obtener los detalles principales del paquete
$consulta_paquete = "
SELECT 
    p.id_paquete,
    cp.categoria,
    pa.nombre_pais,
    c.nombre_ciudad,
    h.hotel,
    he.estadia,
    he.habitacion,
    t.tiempo,
    v.gama,
    p.activo,
    v.precio AS precio_vehiculo,
    h.precio,
    vu.precio AS precio_vuelo
FROM paquetes p
INNER JOIN categoria_paquetes cp ON cp.id_categoria = p.id_categoria
INNER JOIN vuelos vu ON vu.id_vuelo = p.id_vuelo
INNER JOIN ciudades c ON c.id_ciudad = vu.id_ciudad_destino
INNER JOIN paises pa ON pa.id_pais = c.id_pais
INNER JOIN hotel_estadias he ON he.id_hotel_estadia = p.id_hotel_estadia
INNER JOIN hoteles h ON h.id_hotel = he.id_hotel
INNER JOIN tiempo t ON he.id_tiempo = t.id_tiempo
INNER JOIN vehiculos v ON v.id_vehiculo = p.id_vehiculo
WHERE p.id_paquete = ?";

$stmt = $conexion->prepare($consulta_paquete);
$stmt->bind_param("i", $id_paquete);
$stmt->execute();
$resultado_paquete = $stmt->get_result();

if ($resultado_paquete->num_rows === 0) {
    echo '<script>alert("Paquete no encontrado"); window.location.href="lista_paquetes.php";</script>';
    exit();
}

$paquete = $resultado_paquete->fetch_assoc();

// Consulta para obtener las actividades del paquete
$consulta_actividades = "
SELECT 
    a.id_actividad,
    a.actividad,
    a.descripcion,
    a.precio,
    a.imagenes
FROM actividad_ciudad ac
JOIN actividad a ON a.id_actividad = ac.id_actividad
WHERE ac.id_ciudad = (
    SELECT vu.id_ciudad_destino 
    FROM paquetes p
    JOIN vuelos vu ON vu.id_vuelo = p.id_vuelo
    WHERE p.id_paquete = ?
)";

$stmt = $conexion->prepare($consulta_actividades);
$stmt->bind_param("i", $id_paquete);
$stmt->execute();
$actividades = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Paquete #<?= $paquete['id_paquete'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .activity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .package-header {
            background: linear-gradient(135deg, #3490dc 0%, #6574cd 100%);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto px-4 py-8">
        <!-- Botón de volver -->
        <a href="<?= BASE_URL ?>/administrador/paquetes/paquetes.php" class="inline-flex items-center mb-6 text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i> Volver a la lista
        </a>

        <!-- Encabezado del paquete -->
        <div class="package-header rounded-lg shadow-md p-6 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">Paquete #<?= $paquete['id_paquete'] ?></h1>
            <h2 class="text-xl mb-4"><?= $paquete['nombre_ciudad'] ?>, <?= $paquete['nombre_pais'] ?></h2>
            <span class="inline-block bg-<?= $paquete['activo'] ? 'green' : 'red' ?>-500 text-white px-3 py-1 rounded-full text-sm">
                <?= $paquete['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>
        </div>

        <!-- Información principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Detalles del Viaje</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600">Categoría:</p>
                        <p class="font-medium"><?= $paquete['categoria'] ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Hotel:</p>
                        <p class="font-medium"><?= $paquete['hotel'] ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Tipo de Habitación:</p>
                        <p class="font-medium"><?= $paquete['habitacion'] ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Estadia</p>
                        <p class="font-medium"><?= $paquete['estadia'] ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Duración:</p>
                        <p class="font-medium"><?= $paquete['tiempo'] ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Vehículo:</p>
                        <p class="font-medium"><?= $paquete['gama'] ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold mb-4 border-b pb-2">Información de Precios</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-gray-600">Precio por noche en hotel:</p>
                        <p class="font-medium">$<?= number_format($paquete['precio'], 2) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Precio del vuelo:</p>
                        <p class="font-medium">$<?= number_format($paquete['precio_vuelo'], 2) ?></p>
                    </div>
                    <div>
                        <p class="text-gray-600">Precio del vehículo:</p>
                        <p class="font-medium">$<?= number_format($paquete['precio_vehiculo'], 2) ?></p>
                    </div>
                    <div class="pt-4 border-t">
                        <p class="text-gray-600">Total estimado:</p>
                        <p class="text-2xl font-bold text-blue-600">
                            $<?= number_format(
                                ($paquete['precio'] * $paquete['estadia']) + 
                                $paquete['precio_vuelo'] + 
                                $paquete['precio_vehiculo'], 
                                2
                            ) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Actividades disponibles -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold mb-4 border-b pb-2">Actividades Disponibles</h3>
            
            <?php if ($actividades->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while ($actividad = $actividades->fetch_assoc()): ?>
                        <div class="activity-card bg-gray-50 rounded-lg overflow-hidden shadow-md transition duration-300">
                            <?php if ($actividad['imagenes']): ?>
                                <img src="<?= BASE_URL . '/img/actividades/' . $actividad['imagenes'] ?>" 
                                     alt="<?= $actividad['actividad'] ?>" 
                                     class="w-full h-48 object-cover">
                            <?php else: ?>
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-camera text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-4">
                                <h4 class="font-bold text-lg mb-2"><?= $actividad['actividad'] ?></h4>
                                <p class="text-gray-600 mb-3"><?= $actividad['descripcion'] ?></p>
                                <p class="text-blue-600 font-semibold">$<?= number_format($actividad['precio'], 2) ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500 italic">No hay actividades registradas para este destino.</p>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
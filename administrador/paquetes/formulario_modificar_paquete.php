<?php
require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');

$id_paquete = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_paquete <= 0) {
    die("ID de paquete no válido.");
}

$query = "SELECT * FROM paquetes WHERE id_paquete = $id_paquete";
$result = $conexion->query($query);

if (!$result || $result->num_rows === 0) {
    die("Paquete no encontrado.");
}

$paquete = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Paquete</title>
    <link rel="stylesheet" href="p.css">
    <style>
        <?php // Estilos iguales que en agregar ?>
    </style>
</head>
<body>
    <h1>Editar Paquete</h1>
    
    <div class="form-container">
        <form action="actualizar_paquete.php" method="POST">
            <input type="hidden" name="id_paquete" value="<?php echo $paquete['id_paquete']; ?>">

            <!-- Categoría -->
            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <select name="id_categoria" required>
                    <?php
                    $res = $conexion->query("SELECT id_categoria, categoria FROM categoria_paquetes");
                    while ($row = $res->fetch_assoc()) {
                        $selected = ($row['id_categoria'] == $paquete['id_categoria']) ? 'selected' : '';
                        echo "<option value='{$row['id_categoria']}' $selected>{$row['categoria']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Vuelo -->
            <div class="form-group">
                <label for="vuelo">Destino (Vuelo):</label>
                <select name="id_vuelo" required>
                    <?php
                    $res = $conexion->query("SELECT v.id_vuelo, p.nombre_pais, c.nombre_ciudad 
                                             FROM vuelos v
                                             JOIN ciudades c ON v.id_ciudad_destino = c.id_ciudad
                                             JOIN paises p ON c.id_pais = p.id_pais");
                    while ($row = $res->fetch_assoc()) {
                        $selected = ($row['id_vuelo'] == $paquete['id_vuelo']) ? 'selected' : '';
                        echo "<option value='{$row['id_vuelo']}' $selected>{$row['nombre_pais']} - {$row['nombre_ciudad']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Hotel Estadía -->
            <div class="form-group">
                <label for="hotel_estadia">Hotel y Estadía:</label>
                <select name="id_hotel_estadia" required>
                    <?php
                    $res = $conexion->query("SELECT he.id_hotel_estadia, h.hotel, he.estadia, t.tiempo 
                                             FROM hotel_estadias he
                                             JOIN hoteles h ON he.id_hotel = h.id_hotel
                                             JOIN tiempo t ON he.id_tiempo = t.id_tiempo");
                    while ($row = $res->fetch_assoc()) {
                        $selected = ($row['id_hotel_estadia'] == $paquete['id_hotel_estadia']) ? 'selected' : '';
                        echo "<option value='{$row['id_hotel_estadia']}' $selected>{$row['hotel']} - {$row['estadia']} noches - {$row['tiempo']}</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Vehículo -->
            <div class="form-group">
                <label for="vehiculo">Vehículo:</label>
                <select name="id_vehiculo" required>
                    <?php
                    $res = $conexion->query("SELECT id_vehiculo, gama FROM vehiculos");
                    while ($row = $res->fetch_assoc()) {
                        $selected = ($row['id_vehiculo'] == $paquete['id_vehiculo']) ? 'selected' : '';
                        echo "<option value='{$row['id_vehiculo']}' $selected>{$row['gama']}</option>";
                    }
                    ?>
                </select>
            </div>


            <div class="form-group">
                <input type="submit" value="Actualizar Paquete" class="btn-submit">
                <a href="<?php echo BASE_URL; ?>/administrador/paquetes/paquetes.php">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>

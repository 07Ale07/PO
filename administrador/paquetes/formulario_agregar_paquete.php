<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Paquete</title>
    <style>
        .form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php
    session_start(); 
    ?>
    <h1>Agregar Nuevo Paquete</h1>
    
    <div class="form-container">
        <form action="agregar_paquete.php" method="POST">
            <!-- Categoría -->
            <div class="form-group">
                <label for="categoria">Categoría:</label>
                <select name="id_categoria" id="categoria" required>
                    <?php
                    require_once('../../variable_global.php');
                    require_once(ROOT_PATH . '/administrador/conexion.php');
                    
                    $query = "SELECT id_categoria, categoria FROM categoria_paquetes";
                    $result = $conexion->query($query);
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_categoria']}'>{$row['categoria']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <!-- Vuelo/Destino -->
            <div class="form-group">
                <label for="vuelo">Destino (Vuelo):</label>
                <select name="id_vuelo" id="vuelo" required>
                    <?php
                    $query = "SELECT v.id_vuelo, p.nombre_pais, c.nombre_ciudad 
                              FROM vuelos v
                              JOIN ciudades c ON v.id_ciudad_destino = c.id_ciudad
                              JOIN paises p ON c.id_pais = p.id_pais";
                    $result = $conexion->query($query);
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_vuelo']}'>{$row['nombre_pais']} - {$row['nombre_ciudad']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <!-- Hotel y Estadía -->
            <div class="form-group">
                <label for="hotel_estadia">Hotel y Estadía:</label>
                <select name="id_hotel_estadia" id="hotel_estadia" required>
                    <?php
                    $query = "SELECT he.id_hotel_estadia, h.hotel, he.estadia, t.tiempo 
                              FROM hotel_estadias he
                              JOIN hoteles h ON he.id_hotel = h.id_hotel
                              JOIN tiempo t ON he.id_tiempo = t.id_tiempo";
                    $result = $conexion->query($query);
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_hotel_estadia']}'>{$row['hotel']} - {$row['estadia']} noches - {$row['tiempo']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <!-- Vehículo -->
            <div class="form-group">
                <label for="vehiculo">Vehículo:</label>
                <select name="id_vehiculo" id="vehiculo" required>
                    <?php
                    $query = "SELECT id_vehiculo, gama FROM vehiculos";
                    $result = $conexion->query($query);
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id_vehiculo']}'>{$row['gama']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <!-- Activo -->
            <div class="form-group">
                <label for="activo">Estado:</label>
                <select name="activo" id="activo" required>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            
            
            <div class="form-group">
                <input type="submit" value="Guardar Paquete" class="btn-submit">
                <a href="<?php echo BASE_URL; ?>/administrador/paquetes/paquetes.php" style="margin-left:10px;">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
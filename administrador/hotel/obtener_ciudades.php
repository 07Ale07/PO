<?php
require '../conexion.php';
session_start();

header('Content-Type: application/json');

$idPais = filter_input(INPUT_GET, 'id_pais', FILTER_VALIDATE_INT);

if (!$idPais || $idPais <= 0) {
    echo json_encode([]);
    exit();
}

$query = "SELECT id_ciudad, nombre_ciudad 
          FROM ciudades 
          WHERE id_pais = ? 
          ORDER BY nombre_ciudad";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $idPais);
$stmt->execute();
$result = $stmt->get_result();

$ciudades = [];
while ($row = $result->fetch_assoc()) {
    $ciudades[] = $row;
}

echo json_encode($ciudades);
?>
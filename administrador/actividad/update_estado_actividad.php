<?php
require '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_actividad = $_POST['id_actividad'] ?? null;
    $estado = $_POST['estado'] ?? null;
    
    if ($id_actividad && $estado !== null) {
        // Preparar la consulta
        $sql = "UPDATE actividad SET estado = ? WHERE id_actividad = ?";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ii", $estado, $id_actividad);
            if ($stmt->execute()) {
                echo "OK";
            } else {
                echo "Error al ejecutar la consulta";
            }
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta";
        }
    } else {
        echo "Datos incompletos";
    }
} else {
    echo "Método no permitido";
}

$conexion->close();
?>
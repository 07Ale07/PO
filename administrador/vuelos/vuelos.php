<?php
session_start();
require_once('../../variable_global.php');
    
require_once(ROOT_PATH . '/administrador/conexion.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (isset($_GET['tipo']) && $_GET['tipo'] === 'paises') {
    $res = mysqli_query($conexion, "SELECT * FROM paises");
    $paises = [];
    while ($fila = mysqli_fetch_assoc($res)) {
      $paises[] = $fila;
    }
    echo json_encode($paises);
    exit;
  }

  $res = mysqli_query($conexion, "SELECT * FROM vuelos");
  $vuelos = [];
  while ($fila = mysqli_fetch_assoc($res)) {
    $vuelos[] = $fila;
  }
  echo json_encode($vuelos);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  if (isset($data['nombre_pais'])) {
    // Añadir país
    $nombre = $data['nombre_pais'];
    mysqli_query($conexion, "INSERT INTO paises (nombre_pais) VALUES ('$nombre')");
    exit;
  }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"), true);

  $partida = $data['lugar_partida'];
  $destino = $data['destino'];
  $fecha_hora = $data['fecha_hora'];
  $precio = $data['precio'];
  $clase = $data['clase'];
  $img = $data['img'];

  mysqli_query($conexion, "INSERT INTO vuelos (lugar_partida, destino, fecha_hora, precio, clase, estado, img) 
  VALUES ('$partida', '$destino', '$fecha_hora', '$precio', '$clase', 1, '$img')");
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
  $data = json_decode(file_get_contents("php://input"), true);
  $id = $data['id_vuelo'];
  $partida = $data['lugar_partida'];
  $destino = $data['destino'];
  $fecha_hora = $data['fecha_hora'];
  $precio = $data['precio'];
  $clase = $data['clase'];
  $img = $data['img'];

  mysqli_query($conexion, "UPDATE vuelos SET lugar_partida='$partida', destino='$destino', fecha_hora='$fecha_hora', precio='$precio', clase='$clase', img='$img' WHERE id_vuelo=$id");
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
  $data = json_decode(file_get_contents("php://input"), true);
  $id = $data['id_vuelo'];

  $res = mysqli_query($conexion, "SELECT estado FROM vuelos WHERE id_vuelo = $id");
  if ($fila = mysqli_fetch_assoc($res)) {
    $estado_actual = $fila['estado'];
    $nuevo_estado = $estado_actual == 1 ? 0 : 1;
    mysqli_query($conexion, "UPDATE vuelos SET estado = $nuevo_estado WHERE id_vuelo = $id");
  }
}
?>

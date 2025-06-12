<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
    
require_once(ROOT_PATH . '/administrador/conexion.php');
?>
<!DOCTYPE html>
<html>
<head>
  <title>Gestión de Vuelos</title>
</head>
<body>

  <nav>
        <div>
            <a href="<?= BASE_URL ?>/administrador/vuelos/ad.php">Vuelos</a>
            <a href="<?= BASE_URL ?>/administrador/hotel/hoteles.php">Hoteles</a>
            <a href="<?= BASE_URL ?>/administrador/actividad/actividad.php">Vuelos</a>
            <a href="<?= BASE_URL ?>/administrador/logouts/ver_logous.php">Historial de logueos</a>

            <form action="<?= BASE_URL ?>/administrador/logouts/cerrar_sesion.php" method="POST">
                <input type="hidden" name = "cerrar_sesion" value = "1">
                <button type="submit">
        
                Cerrar sesión
                </button>
              </form>
            
        </div>
  </nav>
    

<h2>Agregar Vuelo</h2>
<form id="form-agregar">
  Partida: 
  <select name="lugar_partida" class="select-pais"></select>
  <button type="button" id="btn-agregar-pais">Añadir País</button><br>
  
  Destino: 
  <select name="destino" class="select-pais"></select><br>
  
  Fecha y Hora: <input type="datetime-local" name="fecha_hora"><br>
  Precio: <input type="number" name="precio" step="0.01"><br>
  Clase: <input type="text" name="clase"><br>
  Imagen URL: <input type="text" name="img"><br>
  <button type="submit">Agregar</button>
</form>

<h2>Vuelos</h2>
<table border="1" id="tabla-vuelos">
  <thead>
    <tr>
      <th>ID</th><th>Partida</th><th>Destino</th><th>Fecha y Hora</th>
      <th>Precio</th><th>Clase</th><th>Estado</th><th>Imagen</th><th>Acciones</th>
    </tr>
  </thead>
  <tbody></tbody>
</table>

<div id="form-modificar-contenedor" style="display:none;">
  <h2>Modificar Vuelo</h2>
  <form id="form-modificar">
    <input type="hidden" name="id_vuelo">
    
    Partida: 
    <select name="lugar_partida" class="select-pais"></select>
    <button type="button" id="btn-agregar-pais-mod">Añadir País</button><br>
    
    Destino: 
    <select name="destino" class="select-pais"></select><br>
    
    Fecha y Hora: <input type="datetime-local" name="fecha_hora"><br>
    Precio: <input type="number" name="precio"><br>
    Clase: <input type="text" name="clase"><br>
    Imagen URL: <input type="text" name="img"><br>
    <button type="submit">Guardar Cambios</button>
  </form>
</div>

<script src="script.js"></script>
</body>
</html>

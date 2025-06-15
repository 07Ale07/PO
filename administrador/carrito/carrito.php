<?php

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$carrito = $_SESSION['carrito'];
?>

<!-- Estilos del carrito -->
<link rel="stylesheet" href="carrito.css">

<div id="carrito-burbuja" onclick="toggleCarrito()">
  🛒 <?= count($carrito) ?>
</div>

<div id="carrito-ventana">
  <header>Tu Carrito</header>
  <ul>
    <?php if (empty($carrito)): ?>
      <li>El carrito está vacío</li>
    <?php else: ?>
      <?php foreach ($carrito as $producto): ?>
        <li style="margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
          <strong>Destino:</strong> <?= htmlspecialchars($producto['destino']) ?><br>

          <?php if (!empty($producto['nombre_hotel'])): ?>
            <strong>Hotel:</strong> <?= htmlspecialchars($producto['nombre_hotel']) ?><br>
          <?php endif; ?>

          <?php if (!empty($producto['vehiculo'])): ?>
            <strong>Vehículo:</strong> <?= htmlspecialchars($producto['vehiculo']) ?><br>
          <?php endif; ?>

          <?php
            $estadia = isset($producto['estadia']) ? htmlspecialchars($producto['estadia']) : '—';
            $tiempo = isset($producto['tiempo']) ? htmlspecialchars($producto['tiempo']) : '';
          ?>
          <?php if ($estadia !== '—' || $tiempo): ?>
            <strong>Período:</strong> <?= $estadia . ($tiempo ? ' ' . $tiempo : '') ?><br>
          <?php endif; ?>

          <strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?><br>
          <strong>Cantidad:</strong> <?= (int)$producto['cantidad'] ?><br>

          <!-- Formulario para quitar elementos -->
          <form method="POST" action="acciones_carrito.php" style="margin-top: 5px;">
            <input type="hidden" name="id_paquete" value="<?= $producto['id_paquete'] ?>">

            <button name="quitar_paquete" value="<?= $producto['id_paquete'] ?>" class="text-red-600 hover:underline">Quitar paquete</button><br>

            <?php if (isset($producto['precio_vuelo'])): ?>
              <button name="quitar_parte" value="vuelo" class="text-blue-600 hover:underline">Quitar vuelo</button>
            <?php endif; ?>

            <?php if (isset($producto['precio_hotel'])): ?>
              <button name="quitar_parte" value="hotel" class="text-blue-600 hover:underline">Quitar hotel</button>
            <?php endif; ?>

            <?php if (isset($producto['precio_vehiculo'])): ?>
              <button name="quitar_parte" value="vehiculo" class="text-blue-600 hover:underline">Quitar vehículo</button>
            <?php endif; ?>
          </form>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>

  <!-- Acciones generales -->
  <?php include 'acciones_carrito.php'; ?>
</div>

<script>
  function toggleCarrito() {
    const ventana = document.getElementById('carrito-ventana');
    ventana.style.display = ventana.style.display === 'block' ? 'none' : 'block';
  }

  window.addEventListener('click', function(e) {
    const ventana = document.getElementById('carrito-ventana');
    const burbuja = document.getElementById('carrito-burbuja');
    if (!ventana.contains(e.target) && !burbuja.contains(e.target)) {
      ventana.style.display = 'none';
    }
  });
</script>

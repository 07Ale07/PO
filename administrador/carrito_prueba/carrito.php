<?php
session_start();

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$carrito = $_SESSION['carrito'];
?>

<!-- Estilos y burbuja del carrito -->
<style>
  #carrito-burbuja {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background-color: #3498db;
    border-radius: 50%;
    color: white;
    font-weight: bold;
    font-size: 18px;
    text-align: center;
    line-height: 60px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    z-index: 1000;
    transition: transform 0.3s ease;
  }

  #carrito-burbuja:hover {
    transform: scale(1.1);
  }

  #carrito-ventana {
    display: none;
    position: fixed;
    bottom: 100px;
    right: 30px;
    width: 320px;
    max-height: 400px;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    overflow-y: auto;
    z-index: 1001;
    animation: fadeIn 0.3s ease;
  }

  #carrito-ventana header {
    background-color: #3498db;
    color: white;
    padding: 12px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    font-weight: bold;
  }

  #carrito-ventana ul {
    list-style: none;
    margin: 0;
    padding: 12px;
  }

  #carrito-ventana ul li {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

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
        <li>
          <?= htmlspecialchars($producto['nombre']) ?> - <?= $producto['cantidad'] ?> × $<?= number_format($producto['precio'], 2) ?>
        </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
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

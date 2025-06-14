<?php
session_start();

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Procesar "agregar" producto desde botón
if (isset($_GET['agregar'])) {
    $producto = $_GET['agregar'];
    $precios = [
        'producto_a' => 19.99,
        'producto_b' => 34.50,
        'producto_c' => 12.00,
    ];

    // Si existe, incrementar cantidad; si no, añadir nuevo
    $encontrado = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['nombre'] === $producto) {
            $item['cantidad'] += 1;
            $encontrado = true;
            break;
        }
    }
    if (!$encontrado && isset($precios[$producto])) {
        $_SESSION['carrito'][] = [
            'nombre' => $producto,
            'cantidad' => 1,
            'precio' => $precios[$producto]
        ];
    }

    // Redireccionar para evitar múltiples envíos al recargar
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Página de Prueba - Carrito</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
    }

    .producto {
      padding: 20px;
      margin-bottom: 12px;
      background: #f4f4f4;
      border-radius: 8px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 400px;
    }

    .producto button {
      background: #3498db;
      border: none;
      color: white;
      padding: 10px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s;
    }

    .producto button:hover {
      background: #2980b9;
    }
  </style>
</head>
<body>
  <h1>Productos disponibles</h1>

  <div class="producto">
    <span>Producto A - $19.99</span>
    <a href="?agregar=producto_a"><button>Agregar</button></a>
  </div>

  <div class="producto">
    <span>Producto B - $34.50</span>
    <a href="?agregar=producto_b"><button>Agregar</button></a>
  </div>

  <div class="producto">
    <span>Producto C - $12.00</span>
    <a href="?agregar=producto_c"><button>Agregar</button></a>
  </div>

  <!-- Importar componente carrito -->
  <?php include 'carrito.php'; ?>
</body>
</html>

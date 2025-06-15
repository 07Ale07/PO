<?php
session_start();

if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "<p>Tu carrito está vacío. <a href='paquetes.php'>Volver a paquetes</a></p>";
    exit();
}

$carrito = $_SESSION['carrito'];

$total_general = 0;
foreach ($carrito as $item) {
    $total_general += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Detalle de Compra</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6 max-w-4xl">
        <h1 class="text-3xl font-bold mb-6">Detalle de Compra</h1>

        <?php foreach ($carrito as $item): ?>
            <div class="bg-white rounded shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-3">
                    Destino: <?= htmlspecialchars($item['destino']) ?> 
                </h2>

                <?php if (!empty($item['nombre_hotel'])): ?>
                    <p><strong>Hotel:</strong> <?= htmlspecialchars($item['nombre_hotel']) ?></p>
                <?php endif; ?>

                <?php if (!empty($item['vehiculo'])): ?>
                    <p><strong>Vehículo:</strong> <?= htmlspecialchars($item['vehiculo']) ?></p>
                <?php endif; ?>

                <?php
                    $estadia = isset($item['estadia']) ? htmlspecialchars($item['estadia']) : '—';
                    $tiempo = isset($item['tiempo']) ? htmlspecialchars($item['tiempo']) : '';
                ?>
                <?php if ($estadia !== '—' || $tiempo): ?>
                    <p><strong>Período:</strong> <?= $estadia . ($tiempo ? ' ' . $tiempo : '') ?></p>
                <?php endif; ?>

                <p><strong>Cantidad:</strong> <?= (int)$item['cantidad'] ?></p>
            </div>
        <?php endforeach; ?>

        <div class="bg-white rounded shadow p-6">
            <h2 class="text-2xl font-bold">Total a Pagar: $<?= number_format($total_general, 2) ?></h2>
        </div>

        <div class="mt-6">
            <a href="paquetes.php" class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-4">
                Volver a paquetes
            </a>

            <form method="POST" action="acciones_carrito.php" style="display:inline;">
                <button 
                    type="submit" 
                    name="realizar_compra" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded"
                >
                    Confirmar Compra
                </button>
            </form>
        </div>
    </div>
</body>
</html>

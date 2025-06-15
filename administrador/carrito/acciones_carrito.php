<?php

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['vaciar_carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_POST['quitar_paquete'])) {
        $id = $_POST['quitar_paquete'];
        $_SESSION['carrito'] = array_filter($_SESSION['carrito'], function ($item) use ($id) {
            return $item['id_paquete'] != $id;
        });
    }           

    if (isset($_POST['quitar_parte']) && isset($_POST['id_paquete']) && isset($_POST['parte'])) {
        $id = $_POST['id_paquete'];
        $parte = $_POST['parte'];

        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id_paquete'] == $id) {
                switch ($parte) {
                    case 'vuelo':
                        $item['precio'] -= ($item['precio_vuelo'] ?? 0);
                        $item['precio_vuelo'] = 0;
                        break;
                    case 'hotel':
                        $item['precio'] -= ($item['precio_hotel'] ?? 0);
                        unset($item['nombre_hotel'], $item['estadia'], $item['tiempo'], $item['precio_hotel']);
                        break;
                    case 'vehiculo':
                        $item['precio'] -= ($item['precio_vehiculo'] ?? 0);
                        unset($item['vehiculo'], $item['precio_vehiculo']);
                        break;
                }
                break;
            }
        }
    }

    if (isset($_POST['realizar_compra'])) {
        // Acá podés guardar en base de datos, enviar mails, etc.
        $_SESSION['carrito'] = []; // Por ahora simplemente lo vacía
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>

<div style="margin-top: 20px; padding: 10px;">
    <form method="POST" style="display: inline;">
        <button name="realizar_compra" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Realizar compra</button>
    </form>

    <form method="POST" style="display: inline;">
        <button name="vaciar_carrito" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Vaciar carrito</button>
    </form>
</div>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Hoteles</title>
    <style>
        table {
            width: 80%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #888;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Listado de Hoteles</h1>
    <?php
    require '../conexion.php'; // ajustá si lo moviste

    $sql = "SELECT * FROM hoteles";
    $result = $mysqli->query($sql);

    if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Hotel</th>
                    <th>ID Ciudad</th>
                    <th>id_pais</th>
                    <th>Eliminar</th>
                    <th>Editar</th>
                    <th>Añadir</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fila['id_hotel']); ?></td>
                        <td><?php echo htmlspecialchars($fila['hotel']); ?></td>
                        <td><?php echo htmlspecialchars($fila['id_pais']); ?></td>
                        <td><?php echo htmlspecialchars($fila['id_ciudad']); ?></td>
                        <td>
                             <a href="./H.delete.php?codigo=<?php echo $fila['id_hotel']; ?>" class="action-btn delete-btn">
                                <i class="fas fa-trash"></i> Eliminar
                             </a>
                             <a href="./updateform.php?codigo=<?php echo $fila['id_hotel']; ?>" class="action-btn edit-btn">
                                <i class="fas fa-edit"></i> Editar
                             </a>
                             <a href="./H.agregarform.php?codigo=<?php echo $fila['id_hotel']; ?>" class="action-btn cart-btn">
                                <i class="fas fa-cart-plus"></i> Añadir
                             </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align: center;">No se encontraron hoteles en la base de datos.</p>
    <?php endif; ?>
</body>
</html>

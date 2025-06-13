<?php
session_start();
require '../conexion.php';

// Obtener todos los permisos disponibles
$permisosDisponibles = $conexion->query("SELECT id_permiso, permiso FROM permisos")->fetch_all(MYSQLI_ASSOC);

// Obtener usuarios con sus permisos individuales
$usuarios = $conexion->query("SELECT id_usuario, usuario FROM usuarios");

// Mapeo de permisos por usuario
$permisosPorUsuario = [];
$result = $conexion->query("SELECT id_usuario, id_permiso FROM permiso_usuarios");
while ($row = $result->fetch_assoc()) {
    $permisosPorUsuario[$row['id_usuario']][] = $row['id_permiso'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Permisos</title>
    <link rel="stylesheet" href="ap.css">
</head>
<body>
    <h1>Administrar permisos de usuarios</h1>

    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Permisos asignados</th>
                <th>Modificar</th>
            </tr>
        </thead>
        <tbody>
        <?php while($usuario = $usuarios->fetch_assoc()): 
            $id = $usuario['id_usuario'];
            $nombre = htmlspecialchars($usuario['usuario']);
            $permisosAsignados = $permisosPorUsuario[$id] ?? [];
        ?>
            <tr>
                <td><?= $nombre ?></td>
                <td>
                    <?php
                    if (empty($permisosAsignados)) {
                        echo "<em>Sin permisos</em>";
                    } else {
                        $nombres = array_map(function($p) use ($permisosDisponibles) {
                            foreach ($permisosDisponibles as $permiso) {
                                if ($permiso['id_permiso'] == $p) return $permiso['permiso'];
                            }
                            return '';
                        }, $permisosAsignados);
                        echo implode(', ', array_filter($nombres));
                    }
                    ?>
                </td>
                <td>
                    <form action="abml_permisos.php" method="POST">
                        <input type="hidden" name="id_usuario" value="<?= $id ?>">
                        <?php foreach ($permisosDisponibles as $permiso): ?>
                            <label>
                                <input type="checkbox" name="permisos[]"
                                    value="<?= $permiso['id_permiso'] ?>"
                                    <?= in_array($permiso['id_permiso'], $permisosAsignados) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($permiso['permiso']) ?>
                            </label>
                        <?php endforeach; ?>
                        <button type="submit">Actualizar</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
require '../conexion.php';

if (!isset($_GET['codigo'])) {
    header("Location: actividades.php?error=no_codigo");
    exit;
}

$id_actividad = (int)$_GET['codigo'];

$sql_actividad = "SELECT a.*, c.nombre_ciudad, c.id_pais, p.nombre_pais 
              FROM actividad a
              LEFT JOIN ciudades c ON a.id_ciudad = c.id_ciudad
              LEFT JOIN paises p ON c.id_pais = p.id_pais
              WHERE a.id_actividad = ?";

$stmt_actividad = $conexion->prepare($sql_actividad);
$stmt_actividad->bind_param("i", $id_actividad);
$stmt_actividad->execute();
$resultado = $stmt_actividad->get_result();

if ($resultado->num_rows === 0) {
    header("Location: actividades.php?error=actividad_no_encontrada");
    exit;
}

$actividad = $resultado->fetch_assoc();
$imagenes_actividad = json_decode($actividad['imagenes'], true) ?: [];

// Obtener ciudades del país actual
$sql_ciudades = "SELECT id_ciudad, nombre_ciudad FROM ciudades 
                 WHERE id_pais = ? 
                 ORDER BY nombre_ciudad";
$stmt_ciudades = $conexion->prepare($sql_ciudades);
$stmt_ciudades->bind_param("i", $actividad['id_pais']);
$stmt_ciudades->execute();
$ciudades = $stmt_ciudades->get_result();

// Obtener todos los países
$sql_paises = "SELECT id_pais, nombre_pais FROM paises ORDER BY nombre_pais";
$paises = $conexion->query($sql_paises);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Actividad</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .imagen-actual { display: inline-block; margin-right: 10px; margin-bottom: 10px; position: relative; }
        .eliminar-imagen { position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 20px; cursor: pointer; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto py-8 max-w-4xl">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold text-blue-800 mb-6">Editar Actividad: <?= htmlspecialchars($actividad['actividad']) ?></h1>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="actividadcod.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_actividad" value="<?= $id_actividad ?>">
                <input type="hidden" name="imagenes_actuales" value="<?= htmlspecialchars($actividad['imagenes']) ?>">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($actividad['actividad']) ?>" 
                           class="w-full px-3 py-2 border rounded" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" class="w-full px-3 py-2 border rounded" rows="4" required><?= htmlspecialchars($actividad['descripcion']) ?></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 mb-2">País</label>
                        <select name="pais" id="pais" class="w-full px-3 py-2 border rounded" required onchange="cargarCiudades()">
                            <option value="">Seleccione país</option>
                            <?php while($pais = $paises->fetch_assoc()): ?>
                                <option value="<?= $pais['id_pais'] ?>" <?= $pais['id_pais'] == $actividad['id_pais'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pais['nombre_pais']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Ciudad</label>
                        <select name="ciudad" id="ciudad" class="w-full px-3 py-2 border rounded" required>
                            <option value="">Seleccione ciudad</option>
                            <?php while($ciudad = $ciudades->fetch_assoc()): ?>
                                <option value="<?= $ciudad['id_ciudad'] ?>" <?= $ciudad['id_ciudad'] == $actividad['id_ciudad'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ciudad['nombre_ciudad']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div id="loadingCiudades" class="text-sm text-gray-500 italic hidden">Cargando ciudades...</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Precio</label>
                    <input type="number" name="precio" value="<?= htmlspecialchars($actividad['precio']) ?>" 
                           class="w-full px-3 py-2 border rounded" min="0" step="0.01" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Imágenes actuales</label>
                    <div class="flex flex-wrap">
                        <?php foreach($imagenes_actividad as $index => $imagen): ?>
                            <div class="imagen-actual">
                                <img src="<?= htmlspecialchars($imagen) ?>" alt="Imagen actividad" class="h-32 object-cover">
                                <span class="eliminar-imagen" onclick="marcarParaEliminar(this)">×</span>
                                <input type="hidden" name="eliminar_imagenes[<?= $index ?>]" value="0">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Nuevas imágenes (opcional)</label>
                    <input type="file" name="imagenes[]" multiple accept="image/*" class="w-full px-3 py-2 border rounded">
                    <p class="text-sm text-gray-500">Formatos: JPG, PNG. Máx. 5 imágenes en total.</p>
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="estado" class="form-checkbox" <?= $actividad['estado'] ? 'checked' : '' ?>>
                        <span class="ml-2">Activo</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="actividades.php" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function cargarCiudades() {
            const paisId = document.getElementById('pais').value;
            const ciudadSelect = document.getElementById('ciudad');
            const loading = document.getElementById('loadingCiudades');
            
            if (!paisId) return;
            
            loading.classList.remove('hidden');
            ciudadSelect.disabled = true;
            
            fetch(`obtener_ciudades.php?id_pais=${paisId}`)
                .then(response => response.json())
                .then(ciudades => {
                    ciudadSelect.innerHTML = '<option value="">Seleccione ciudad</option>';
                    ciudades.forEach(ciudad => {
                        const option = document.createElement('option');
                        option.value = ciudad.id_ciudad;
                        option.textContent = ciudad.nombre_ciudad;
                        ciudadSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    ciudadSelect.innerHTML = '<option value="">Error al cargar</option>';
                })
                .finally(() => {
                    loading.classList.add('hidden');
                    ciudadSelect.disabled = false;
                });
        }

        function marcarParaEliminar(elemento) {
            const contenedor = elemento.parentElement;
            const inputEliminar = contenedor.querySelector('[name^="eliminar_imagenes"]');
            
            if (inputEliminar.value === "0") {
                inputEliminar.value = "1";
                contenedor.style.opacity = "0.5";
                elemento.textContent = "✓";
                elemento.style.background = "green";
            } else {
                inputEliminar.value = "0";
                contenedor.style.opacity = "1";
                elemento.textContent = "×";
                elemento.style.background = "red";
            }
        }
    </script>
</body>
</html>

<?php
$conexion->close();
?>
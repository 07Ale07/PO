<?php
require '../conexion.php';

if (!isset($_GET['codigo'])) {
    header("Location: hoteles.php?error=no_codigo");
    exit;
}

$id_hotel = (int)$_GET['codigo'];

$sql_hotel = "SELECT h.*, c.nombre_ciudad, p.nombre_pais 
              FROM hoteles h
              LEFT JOIN ciudades c ON h.id_ciudad = c.id_ciudad
              LEFT JOIN paises p ON h.id_pais = p.id_pais
              WHERE h.id_hotel = ?";

$stmt_hotel = $conexion->prepare($sql_hotel);
$stmt_hotel->bind_param("i", $id_hotel);
$stmt_hotel->execute();
$resultado = $stmt_hotel->get_result();

if ($resultado->num_rows === 0) {
    header("Location: hoteles.php?error=hotel_no_encontrado");
    exit;
}

$hotel = $resultado->fetch_assoc();

// Obtener ciudades del mismo país que el hotel actual
$sql_ciudades = "SELECT id_ciudad, nombre_ciudad FROM ciudades 
                 WHERE id_pais = ? 
                 ORDER BY nombre_ciudad";
$stmt_ciudades = $conexion->prepare($sql_ciudades);
$stmt_ciudades->bind_param("i", $hotel['id_pais']);
$stmt_ciudades->execute();
$ciudades = $stmt_ciudades->get_result();

$sql_paises = "SELECT id_pais, nombre_pais FROM paises ORDER BY nombre_pais";
$paises = $conexion->query($sql_paises);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Hotel | Turismo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .form-container { max-width: 800px; margin: 2rem auto; background: white; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 2rem; }
        .form-title { color: #1e40af; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .form-label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: #374151; }
        .form-input { width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; margin-bottom: 1rem; }
        .form-input:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .btn-submit { background-color: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600; transition: background-color 0.2s; }
        .btn-submit:hover { background-color: #2563eb; }
        .img-preview { max-width: 200px; max-height: 200px; margin-top: 0.5rem; border-radius: 0.25rem; }
        .error-message { color: #ef4444; font-size: 0.875rem; margin-top: -0.5rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1 class="form-title text-2xl font-bold">Editar Hotel: <?php echo htmlspecialchars($hotel['hotel']); ?></h1>

        <?php if (isset($_GET['error'])): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                <?php 
                $error = $_GET['error'];
                switch($error) {
                    case 'campo_vacio': echo "Todos los campos son requeridos"; break;
                    case 'error_subir_imagen': echo "Error al subir la imagen"; break;
                    default: echo "Ocurrió un error";
                }
                ?>
            </div>
        <?php endif; ?>

        <form action="hotelcod.php" method="post" enctype="multipart/form-data" id="hotelForm">
            <input type="hidden" name="id_hotel" value="<?php echo htmlspecialchars($hotel['id_hotel']); ?>">
            <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($hotel['imagenes']); ?>">

            <div class="mb-4">
                <label for="nombre" class="form-label">Nombre del Hotel</label>
                <input type="text" id="nombre" name="nombre" class="form-input" 
                       value="<?php echo htmlspecialchars($hotel['hotel']); ?>" required>
            </div>

            <div class="mb-4">
                <label for="pais" class="form-label">País</label>
                <select id="pais" name="pais" class="form-input" required onchange="cargarCiudades()">
                    <option value="">Seleccione un país</option>
                    <?php while($pais = $paises->fetch_assoc()): ?>
                        <option value="<?php echo $pais['id_pais']; ?>" 
                            <?php if($pais['id_pais'] == $hotel['id_pais']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($pais['nombre_pais']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="ciudad" class="form-label">Ciudad</label>
                <select id="ciudad" name="ciudad" class="form-input" required>
                    <option value="">Seleccione una ciudad</option>
                    <?php while($ciudad = $ciudades->fetch_assoc()): ?>
                        <option value="<?php echo $ciudad['id_ciudad']; ?>" 
                            <?php if($ciudad['id_ciudad'] == $hotel['id_ciudad']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($ciudad['nombre_ciudad']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <div id="loadingCiudades" class="text-sm text-gray-500 italic" style="display: none;">Cargando ciudades...</div>
            </div>

            <div class="mb-4">
                <label for="precio" class="form-label">Precio por noche</label>
                <input type="number" id="precio" name="precio" class="form-input" min="0" step="0.01" 
                       value="<?php echo htmlspecialchars($hotel['precio']); ?>" required>
            </div>

            <div class="mb-4">
                <label for="imagen" class="form-label">Imagen del Hotel</label>
                <input type="file" id="imagen" name="imagen" class="form-input" accept="image/*">
                <p class="text-sm text-gray-500">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 2MB</p>
                <div id="imageError" class="error-message"></div>
                
                <?php if(!empty($hotel['imagenes'])): ?>
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">Imagen actual:</p>
                        <img src="<?php echo htmlspecialchars($hotel['imagenes']); ?>" 
                             alt="Imagen actual del hotel" class="img-preview" id="currentImage" />
                        <div class="mt-2">
                            <input type="checkbox" id="eliminar_imagen" name="eliminar_imagen" value="1">
                            <label for="eliminar_imagen" class="text-sm text-gray-700">Eliminar esta imagen</label>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div id="imagePreview" class="mt-2 hidden">
                    <p class="text-sm text-gray-600">Nueva imagen seleccionada:</p>
                    <img id="previewImage" class="img-preview" />
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Estado</label>
                <div class="flex items-center">
                    <input type="checkbox" id="estado" name="estado" class="h-5 w-5 text-blue-600 rounded" 
                           value="1" <?php echo ($hotel['estado'] == 1) ? 'checked' : ''; ?> />
                    <label for="estado" class="ml-2 text-gray-700">Activo</label>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <a href="hoteles.php" class="px-4 py-2 text-gray-600 hover:text-gray-800 rounded border border-gray-300 hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="btn-submit">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <script>
        // Vista previa de la imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const preview = document.getElementById('previewImage');
            const error = document.getElementById('imageError');
            const previewContainer = document.getElementById('imagePreview');
            const currentImage = document.getElementById('currentImage');
            const file = e.target.files[0];
            
            error.textContent = '';
            previewContainer.classList.add('hidden');
            
            if (!file) return;
            
            // Validar tipo de archivo
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                error.textContent = 'Solo se permiten imágenes JPG, PNG o GIF';
                this.value = '';
                return;
            }
            
            // Validar tamaño (2MB)
            if (file.size > 2 * 1024 * 1024) {
                error.textContent = 'La imagen no debe exceder los 2MB';
                this.value = '';
                return;
            }
            
            // Mostrar vista previa
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
                if (currentImage) {
                    currentImage.classList.add('opacity-50');
                }
            }
            reader.readAsDataURL(file);
        });

        // Cargar ciudades dinámicamente según país seleccionado
        function cargarCiudades() {
            const paisSelect = document.getElementById('pais');
            const ciudadSelect = document.getElementById('ciudad');
            const loading = document.getElementById('loadingCiudades');
            
            const idPais = paisSelect.value;
            
            if (!idPais) {
                ciudadSelect.innerHTML = '<option value="">Seleccione un país primero</option>';
                ciudadSelect.disabled = true;
                return;
            }
            
            // Mostrar carga y deshabilitar select
            loading.style.display = 'block';
            ciudadSelect.disabled = true;
            ciudadSelect.innerHTML = '<option value="">Cargando ciudades...</option>';
            
            // Hacer petición AJAX
            fetch(`obtener_ciudades.php?id_pais=${idPais}`)
                .then(response => response.json())
                .then(ciudades => {
                    if (ciudades.length > 0) {
                        let options = '<option value="">Seleccione una ciudad</option>';
                        ciudades.forEach(ciudad => {
                            options += `<option value="${ciudad.id_ciudad}">${ciudad.nombre_ciudad}</option>`;
                        });
                        
                        ciudadSelect.innerHTML = options;
                    } else {
                        ciudadSelect.innerHTML = '<option value="">No hay ciudades para este país</option>';
                    }
                    ciudadSelect.disabled = false;
                })
                .catch(error => {
                    ciudadSelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
                })
                .finally(() => {
                    loading.style.display = 'none';
                });
        }
    </script>
</body>
</html>

<?php 
$conexion->close();

?>
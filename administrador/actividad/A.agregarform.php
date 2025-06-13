<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Actividad</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .form-table th {
            text-align: left;
            padding: 12px;
            width: 30%;
            background-color: #f5f5f5;
        }
        .form-table td {
            padding: 12px;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        .form-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 100px;
            resize: vertical;
        }
        .form-submit {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .form-submit:hover {
            background-color: #45a049;
        }
        .form-cancel {
            background-color: #f44336;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .form-cancel:hover {
            background-color: #d32f2f;
        }
        .error-message {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
        #loadingCiudades {
            display: none;
            color: #666;
            font-style: italic;
        }
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .preview-image-item {
            position: relative;
            max-width: 100px;
            max-height: 100px;
        }
        .remove-image {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #f44336;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            cursor: pointer;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <?php
    require_once('../../variable_global.php');
    ?>
    <div class="form-container">
        <h2>Agregar Nueva Actividad</h2>
        <form action="A.agregaractividadcod.php" method="post" enctype="multipart/form-data" id="actividadForm">
            <table class="form-table">
                <tr>
                    <th>Nombre de la Actividad</th>
                    <td>
                        <input type="text" name="actividad" class="form-input" 
                               placeholder="Ingrese el nombre de la actividad" required>
                    </td>
                </tr>
                <tr>
                    <th>País</th>
                    <td>
                        <select name="pais" id="pais" class="form-select" required onchange="cargarCiudades()">
                            <option value="">Seleccione un país</option>
                            <?php
                            require '../conexion.php';
                            $query = "SELECT id_pais, nombre_pais FROM paises ORDER BY nombre_pais";
                            $result = $conexion->query($query);
                            while($row = $result->fetch_assoc()) {
                                echo "<option value='".$row['id_pais']."'>".$row['nombre_pais']."</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Ciudad</th>
                    <td>
                        <select name="id_ciudad" id="ciudad" class="form-select" required disabled>
                            <option value="">Primero seleccione un país</option>
                        </select>
                        <div id="loadingCiudades">Cargando ciudades...</div>
                    </td>
                </tr>
                <tr>
                    <th>Precio</th>
                    <td>
                        <input type="number" name="precio" class="form-input" 
                               placeholder="Ingrese el precio" min="1" required>
                    </td>
                </tr>
                <tr>
                    <th>Descripción</th>
                    <td>
                        <textarea name="descripcion" class="form-textarea" 
                                  placeholder="Ingrese una descripción detallada de la actividad" required></textarea>
                    </td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td>
                        <select name="estado" class="form-select" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Imágenes (Múltiples)</th>
                    <td>
                        <input type="file" name="imagenes[]" id="imagenes" class="form-input" 
                               accept="image/*" multiple required>
                        <div id="imagePreview" class="image-preview-container"></div>
                        <p class="error-message" id="imageError"></p>
                    </td>
                </tr>
            </table>
            
            <button type="submit" class="form-submit">
                AGREGAR ACTIVIDAD
            </button>
            <?php
            echo "<a href='" . BASE_URL . "/administrador/actividad/actividad.php'  class='form-cancel'>CANCELAR</a>";
            ?>

        </form>
    </div>

    <script>
        // Vista previa de múltiples imágenes
        document.getElementById('imagenes').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('imagePreview');
            const error = document.getElementById('imageError');
            const files = e.target.files;
            
            previewContainer.innerHTML = '';
            error.textContent = '';
            
            if (!files || files.length === 0) return;
            
            // Validar cantidad de imágenes (opcional)
            if (files.length > 5) {
                error.textContent = 'Máximo 5 imágenes permitidas';
                return;
            }
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                if (!file.type.match('image.*')) {
                    error.textContent = 'Todos los archivos deben ser imágenes';
                    return;
                }
                
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    error.textContent = 'Las imágenes no deben exceder los 2MB';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-image-item';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '100%';
                    
                    const removeBtn = document.createElement('span');
                    removeBtn.className = 'remove-image';
                    removeBtn.innerHTML = '×';
                    removeBtn.onclick = function() {
                        previewItem.remove();
                        // También deberías eliminar el archivo del input file
                        // Esto requiere un manejo más avanzado con DataTransfer
                    };
                    
                    previewItem.appendChild(img);
                    previewItem.appendChild(removeBtn);
                    previewContainer.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            }
            
            previewContainer.style.display = 'flex';
        });

        // Cargar ciudades según país seleccionado (igual que en el ejemplo de hoteles)
        function cargarCiudades() {
            const paisSelect = document.getElementById('pais');
            const ciudadSelect = document.getElementById('ciudad');
            const loading = document.getElementById('loadingCiudades');
            
            const idPais = paisSelect.value;
            
            if (!idPais) {
                ciudadSelect.innerHTML = '<option value="">Primero seleccione un país</option>';
                ciudadSelect.disabled = true;
                return;
            }
            
            // Mostrar carga y deshabilitar select
            loading.style.display = 'block';
            ciudadSelect.disabled = true;
            ciudadSelect.innerHTML = '<option value="">Cargando ciudades...</option>';
            
            // Hacer petición AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `obtener_ciudades.php?id_pais=${idPais}`, true);
            
            xhr.onload = function() {
                if (this.status === 200) {
                    const ciudades = JSON.parse(this.responseText);
                    
                    if (ciudades.length > 0) {
                        let options = '';
                        ciudades.forEach(ciudad => {
                            options += `<option value="${ciudad.id_ciudad}">${ciudad.nombre_ciudad}</option>`;
                        });
                        
                        ciudadSelect.innerHTML = options;
                        ciudadSelect.disabled = false;
                    } else {
                        ciudadSelect.innerHTML = '<option value="">No hay ciudades para este país</option>';
                    }
                } else {
                    ciudadSelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
                }
                
                loading.style.display = 'none';
            };
            
            xhr.onerror = function() {
                ciudadSelect.innerHTML = '<option value="">Error de conexión</option>';
                loading.style.display = 'none';
            };
            
            xhr.send();
        }
    </script>
</body>
</html>
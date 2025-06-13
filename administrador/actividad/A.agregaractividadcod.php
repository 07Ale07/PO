<?php
require '../conexion.php';

// Configuración de directorios (igual que en hoteles)
$directorio = $_SERVER["HTTP_ORIGIN"] . "/olimpiadas_7timo/administrador/";

$directorio_destino = '../img/hoteles/';
$max_file_size = 2 * 1024 * 1024; // 2MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_images = 5; // Máximo de imágenes permitidas

// Validar y sanitizar datos de entrada
$actividad = trim($_POST['actividad'] ?? '');
$id_ciudad = filter_input(INPUT_POST, 'id_ciudad', FILTER_VALIDATE_INT);
$pais = filter_input(INPUT_POST, 'pais', FILTER_VALIDATE_INT);
$precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);
$descripcion = trim($_POST['descripcion'] ?? '');
$estado = filter_input(INPUT_POST, 'estado', FILTER_VALIDATE_INT);

// Validaciones básicas
if (empty($actividad)) {
    mostrarError("El nombre de la actividad es requerido");
}

if (empty($descripcion)) {
    mostrarError("La descripción de la actividad es requerida");
}

if (!$id_ciudad || $id_ciudad <= 0) {
    mostrarError("Seleccione una ciudad válida");
}

if (!$pais || $pais <= 0) {
    mostrarError("Seleccione un país válido");
}

if (!$precio || $precio <= 0) {
    mostrarError("El precio debe ser un número positivo");
}

if ($estado === false) {
    mostrarError("Seleccione un estado válido");
}

// Verificar que la ciudad pertenezca al país seleccionado
$stmt = $conexion->prepare("SELECT id_ciudad FROM ciudades WHERE id_ciudad = ? AND id_pais = ?");
$stmt->bind_param("ii", $id_ciudad, $pais);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    mostrarError("La ciudad seleccionada no pertenece al país especificado");
}
$stmt->close();

// Validar archivos subidos
if (!isset($_FILES['imagenes']) || count($_FILES['imagenes']['name']) === 0) {
    mostrarError("Debe subir al menos una imagen");
}

// Verificar cantidad de imágenes
if (count($_FILES['imagenes']['name']) > $max_images) {
    mostrarError("Máximo $max_images imágenes permitidas");
}

$rutas_imagenes = [];
$errores_imagenes = [];

// Procesar cada imagen
for ($i = 0; $i < count($_FILES['imagenes']['name']); $i++) {
    if ($_FILES['imagenes']['error'][$i] !== UPLOAD_ERR_OK) {
        $errores_imagenes[] = "Error en imagen " . ($i+1) . ": " . obtenerMensajeError($_FILES['imagenes']['error'][$i]);
        continue;
    }

    $file = [
        'name' => $_FILES['imagenes']['name'][$i],
        'type' => $_FILES['imagenes']['type'][$i],
        'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
        'error' => $_FILES['imagenes']['error'][$i],
        'size' => $_FILES['imagenes']['size'][$i]
    ];

    $file_type = mime_content_type($file['tmp_name']);
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    // Validar tipo y tamaño de archivo
    if (!in_array($file_type, $allowed_types)) {
        $errores_imagenes[] = "Imagen " . ($i+1) . ": Tipo de archivo no permitido. Solo JPEG, PNG, GIF o WEBP";
        continue;
    }

    if ($file['size'] > $max_file_size) {
        $errores_imagenes[] = "Imagen " . ($i+1) . ": El tamaño no debe exceder los 2MB";
        continue;
    }

    // Crear directorio si no existe
    if (!file_exists($directorio_destino)) {
        if (!mkdir($directorio_destino, 0755, true)) {
            mostrarError("No se pudo crear el directorio para guardar las imágenes");
        }
    }

    // Generar nombre único con prefijo 'hotel_'
    $nombre_archivo = 'hotel_' . uniqid() . '.' . $file_extension;
    $ruta_completa = $directorio_destino . $nombre_archivo;

    // Mover el archivo subido
    if (!move_uploaded_file($file['tmp_name'], $ruta_completa)) {
        $errores_imagenes[] = "Imagen " . ($i+1) . ": Error al guardar en el servidor. Verifique permisos.";
        continue;
    }

    // Ruta relativa para la base de datos
    $rutas_imagenes[] = '/img/hoteles/' . $nombre_archivo;
}

// Si hay errores con las imágenes, eliminar las subidas y mostrar errores
if (!empty($errores_imagenes)) {
    foreach ($rutas_imagenes as $ruta) {
        $ruta_fisica = '../' . ltrim($ruta, '/');
        if (file_exists($ruta_fisica)) {
            unlink($ruta_fisica);
        }
    }
    mostrarError(implode("<br>", $errores_imagenes));
}

// 👉 Aquí está el cambio: guardar solo la primera imagen como string
$imagenes_json = $rutas_imagenes[0];

// Insertar en la base de datos
try {
    $stmt = $conexion->prepare('INSERT INTO actividad 
                            (actividad, precio, id_ciudad, estado, imagenes, descripcion) 
                            VALUES (?, ?, ?, ?, ?, ?)');
    
    $stmt->bind_param("sdiiss", $actividad, $precio, $id_ciudad, $estado, $imagenes_json, $descripcion);
    
    if (!$stmt->execute()) {
        foreach ($rutas_imagenes as $ruta) {
            $ruta_fisica = '../' . ltrim($ruta, '/');
            if (file_exists($ruta_fisica)) {
                unlink($ruta_fisica);
            }
        }
        mostrarError("Error al guardar en la base de datos: " . $stmt->error);
    }
    
    $stmt->close();
    
    // Redirección con éxito
    header("Location: ". $directorio . "actividad/actividad.php?success=1");
    exit();
    
} catch (Exception $e) {
    foreach ($rutas_imagenes as $ruta) {
        $ruta_fisica = '../' . ltrim($ruta, '/');
        if (file_exists($ruta_fisica)) {
            unlink($ruta_fisica);
        }
    }
    mostrarError("Error en la base de datos: " . $e->getMessage());
}

// Funciones auxiliares
function mostrarError($mensaje) {
    echo '<script language="javascript">
        alert("Error: ' . addslashes($mensaje) . '")
        window.history.back()
        </script>';
    exit();
}

function obtenerMensajeError($codigo) {
    $errores = [
        UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido',
        UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
        UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente',
        UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
        UPLOAD_ERR_NO_TMP_DIR => 'Falta el directorio temporal',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco',
        UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo'
    ];
    return $errores[$codigo] ?? 'Error desconocido';
}

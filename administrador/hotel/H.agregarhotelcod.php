<?php
require '../conexion.php';

// Configuración de directorios
$directorio_destino = '../img/hoteles/';
$max_file_size = 2 * 1024 * 1024; // 2MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// Validar y sanitizar datos de entrada
$nombre = trim($_POST['nombre'] ?? '');
$ciudad = filter_input(INPUT_POST, 'ciudad', FILTER_VALIDATE_INT);
$pais = filter_input(INPUT_POST, 'pais', FILTER_VALIDATE_INT);
$precio = filter_input(INPUT_POST, 'precio', FILTER_VALIDATE_FLOAT);

// Validaciones básicas
if (empty($nombre)) {
    mostrarError("El nombre del hotel es requerido");
}

if (!$ciudad || $ciudad <= 0) {
    mostrarError("Seleccione una ciudad válida");
}

if (!$pais || $pais <= 0) {
    mostrarError("Seleccione un país válido");
}

if (!$precio || $precio <= 0) {
    mostrarError("El precio debe ser un número positivo");
}

// Verificar que la ciudad pertenezca al país seleccionado
$stmt = $conexion->prepare("SELECT id_ciudad FROM ciudades WHERE id_ciudad = ? AND id_pais = ?");
$stmt->bind_param("ii", $ciudad, $pais);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    mostrarError("La ciudad seleccionada no pertenece al país especificado");
}
$stmt->close();

// Validar archivo subido
if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    mostrarError("Error al subir la imagen: " . obtenerMensajeError($_FILES['imagen']['error']));
}

$file = $_FILES['imagen'];
$file_type = mime_content_type($file['tmp_name']);
$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Validar tipo y tamaño de archivo
if (!in_array($file_type, $allowed_types)) {
    mostrarError("Tipo de archivo no permitido. Solo se aceptan imágenes JPEG, PNG, GIF o WEBP");
}

if ($file['size'] > $max_file_size) {
    mostrarError("El tamaño de la imagen no debe exceder los 2MB");
}

// Crear directorio si no existe
if (!file_exists($directorio_destino)) {
    if (!mkdir($directorio_destino, 0755, true)) {
        mostrarError("No se pudo crear el directorio para guardar las imágenes");
    }
}

// Generar nombre único para el archivo
$nombre_archivo = 'hotel_' . uniqid() . '.' . $file_extension;
$ruta_completa = $directorio_destino . $nombre_archivo;

// Mover el archivo subido
if (!move_uploaded_file($file['tmp_name'], $ruta_completa)) {
    mostrarError("Error al guardar la imagen en el servidor");
}

// Ruta relativa para la base de datos
$ruta_db = '/img/hoteles/' . $nombre_archivo;

// Insertar en la base de datos usando sentencias preparadas
try {
    $stmt = $conexion->prepare("INSERT INTO hoteles 
                               (hotel, id_ciudad, id_pais, imagenes, estado, precio) 
                               VALUES (?, ?, ?, ?, 1, ?)");
    
    $stmt->bind_param("siisd", $nombre, $ciudad, $pais, $ruta_db, $precio);
    
    if (!$stmt->execute()) {
        // Eliminar la imagen si falla la inserción
        unlink($ruta_completa);
        mostrarError("Error al guardar en la base de datos: " . $stmt->error);
    }
    
    $stmt->close();
    
    // Redireccionar con éxito
    header("Location: metodo.php?success=1");
    exit();
    
} catch (Exception $e) {
    unlink($ruta_completa);
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
?>
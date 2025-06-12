<?php
require '../conexion.php';

// Configuración
$directorio_destino = '../img/hoteles/';
$max_file_size = 2 * 1024 * 1024; // 2MB
$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$ruta_base = '/img/hoteles/'; // Ruta para la BD

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mostrarError("Método no permitido", 'hoteles.php');
}

// Sanitizar entradas
$nombre = trim($_POST['nombre'] ?? '');
$ciudad = (int)($_POST['ciudad'] ?? 0);
$pais = (int)($_POST['pais'] ?? 0);
$precio = (float)($_POST['precio'] ?? 0);
$imagen_actual = $_POST['imagen_actual'] ?? '';

// Validaciones
if (empty($nombre)) {
    mostrarError("El nombre del hotel es requerido", 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
}

if ($ciudad <= 0 || $pais <= 0) {
    mostrarError("Seleccione ciudad y país válidos", 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
}

if ($precio <= 0) {
    mostrarError("El precio debe ser mayor a 0", 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
}

// Verificar relación ciudad-país
$stmt = $conexion->prepare("SELECT 1 FROM ciudades WHERE id_ciudad = ? AND id_pais = ?");
$stmt->bind_param("ii", $ciudad, $pais);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    mostrarError("La ciudad no pertenece al país seleccionado", 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
}
$stmt->close();

// Procesar imagen
$imagen_subida = $imagen_actual; // Mantener la actual por defecto

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['imagen'];
    
    // Validar tipo real del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        mostrarError("Solo se permiten imágenes JPG, PNG o WEBP", 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
    }

    // Validar tamaño
    if ($file['size'] > $max_file_size) {
        mostrarError("La imagen no debe exceder 2MB", 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
    }

    // Generar nombre único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nombre_archivo = 'hotel_' . uniqid() . '_' . preg_replace('/[^a-z0-9]/i', '_', $nombre) . '.' . $extension;
    $ruta_completa = $directorio_destino . $nombre_archivo;

    // Crear directorio si no existe
    if (!is_dir($directorio_destino)) {
        mkdir($directorio_destino, 0755, true);
    }

    // Mover archivo
    if (move_uploaded_file($file['tmp_name'], $ruta_completa)) {
        $imagen_subida = $ruta_base . $nombre_archivo;
        
        // Eliminar imagen anterior si existe
        if (!empty($imagen_actual) && file_exists('..' . $imagen_actual)) {
            unlink('..' . $imagen_actual);
        }
    } else {
        mostrarError("Error al guardar la imagen", 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
    }
}

// Actualizar en BD
$sql = "UPDATE hoteles SET 
        hotel = ?, 
        id_ciudad = ?, 
        id_pais = ?, 
        precio = ?, 
        imagenes = ?, 
        estado = ?
        WHERE id_hotel = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    mostrarError("Error al preparar la consulta", 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
}

$estado = isset($_POST['estado']) ? 1 : 0;
$stmt->bind_param("siissii", $nombre, $ciudad, $pais, $precio, $imagen_subida, $estado, $_POST['id_hotel']);

if (!$stmt->execute()) {
    mostrarError("Error al actualizar: " . $stmt->error, 'H.updateform.php?codigo='.($_POST['id_hotel'] ?? ''));
}

// Éxito
header("Location: hoteles.php?success=1");
exit();

// Funciones auxiliares
function mostrarError($mensaje, $redireccion = '') {
    if (!empty($redireccion)) {
        header("Location: $redireccion&error=" . urlencode($mensaje));
    } else {
        die("Error: $mensaje");
    }
    exit();
}
?>
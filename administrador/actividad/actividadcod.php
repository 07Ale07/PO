<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require '../conexion.php';

// Configuración
$directorio = $_SERVER["HTTP_ORIGIN"] . "/olimpiadas_7timo/administrador/";

$directorio_destino = '../img/hoteles/';
$max_file_size = 2 * 1024 * 1024; // 2MB
$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
$ruta_base = '/img/hoteles/'; // Ruta para la BD

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mostrarError("Método no permitido", 'actividades.php');
}

// Sanitizar entradas
$id_actividad = (int)($_POST['id_actividad'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$ciudad = (int)($_POST['ciudad'] ?? 0);
$pais = (int)($_POST['pais'] ?? 0);
$precio = (float)($_POST['precio'] ?? 0);
$estado = isset($_POST['estado']) ? 1 : 0;

$imagenes_actuales = json_decode($_POST['imagenes_actuales'] ?? '[]', true) ?: [];
$imagenes_a_eliminar = $_POST['eliminar_imagenes'] ?? [];

if ($id_actividad <= 0) {
    mostrarError("ID de actividad inválido", 'actividades.php');
}

if (empty($nombre) || empty($descripcion)) {
    mostrarError("Nombre y descripción son requeridos", "A.updateform.php?codigo=$id_actividad");
}

if ($ciudad <= 0 || $pais <= 0) {
    mostrarError("Seleccione ciudad y país válidos", "A.updateform.php?codigo=$id_actividad");
}

if ($precio < 0) {
    mostrarError("El precio no puede ser negativo", "A.updateform.php?codigo=$id_actividad");
}

// Verificar relación ciudad-país
$stmt = $conexion->prepare("SELECT 1 FROM ciudades WHERE id_ciudad = ? AND id_pais = ?");
$stmt->bind_param("ii", $ciudad, $pais);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    mostrarError("La ciudad no pertenece al país seleccionado", "A.updateform.php?codigo=$id_actividad");
}
$stmt->close();

// Filtrar imágenes actuales (eliminar marcadas)
$imagenes_finales = [];
foreach ($imagenes_actuales as $index => $ruta) {
    if (!isset($imagenes_a_eliminar[$index]) || $imagenes_a_eliminar[$index] != "1") {
        $imagenes_finales[] = $ruta;
    } else {
        if (file_exists('..' . $ruta)) {
            unlink('..' . $ruta); // eliminar físicamente
        }
    }
}

// Procesar nuevas imágenes
if (!empty($_FILES['imagenes']['name'][0])) {
    foreach ($_FILES['imagenes']['tmp_name'] as $i => $tmp_name) {
        if ($_FILES['imagenes']['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        $mime_type = mime_content_type($tmp_name);
        if (!in_array($mime_type, $allowed_types)) {
            mostrarError("Formato no válido en imagen #" . ($i + 1), "A.updateform.php?codigo=$id_actividad");
        }

        if ($_FILES['imagenes']['size'][$i] > $max_file_size) {
            mostrarError("La imagen #" . ($i + 1) . " excede el tamaño permitido", "A.updateform.php?codigo=$id_actividad");
        }

        $extension = pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION);
        $nombre_archivo = 'actividad_' . uniqid() . '_' . preg_replace('/[^a-z0-9]/i', '_', $nombre) . ".$extension";
        $ruta_completa = $directorio_destino . $nombre_archivo;

        if (!is_dir($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }

        if (move_uploaded_file($tmp_name, $ruta_completa)) {
            $ruta_relativa = $ruta_base . $nombre_archivo;
            $imagenes_finales[] = $ruta_relativa;
        }
    }
}

if (count($imagenes_finales) === 0) {
    mostrarError("Debe haber al menos una imagen", "A.updateform.php?codigo=$id_actividad");
}

// Guardar solo la primera imagen que haya quedado, sea nueva o existente
$imagenes_json = $imagenes_finales[0];


// Actualizar en BD
$sql = "UPDATE actividad SET 
        actividad = ?, 
        descripcion = ?, 
        id_ciudad = ?, 
        precio = ?, 
        imagenes = ?, 
        estado = ?
        WHERE id_actividad = ?";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    mostrarError("Error al preparar la consulta", "A.updateform.php?codigo=$id_actividad");
}

$stmt->bind_param("ssiisii", $nombre, $descripcion, $ciudad, $precio, $imagenes_json, $estado, $id_actividad);

if (!$stmt->execute()) {
    mostrarError("Error al actualizar: " . $stmt->error, "A.updateform.php?codigo=$id_actividad");
}

// Éxito
header("Location: actividad.php?success=1");
exit();

// Función de errores
function mostrarError($mensaje, $redireccion = '') {
    if (!empty($redireccion)) {
        header("Location:" .  $directorio . "actividad.php?error=" . urlencode($mensaje));
    } else {
        die("Error: $mensaje");
    }
    exit();
}
?>

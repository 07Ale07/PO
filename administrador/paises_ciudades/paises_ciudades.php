<?php
set_time_limit(0);
require './../conexion.php';

define('GEONAMES_USER', 'ariel_ayala125');
header('Content-Type: text/html; charset=utf-8');

function callApi($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MiAplicacion/1.0');
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("Error cURL: " . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 401) {
        throw new Exception("Usuario Geonames no válido o sin créditos.");
    }

    return json_decode($response, true);
}

function asegurarColumnaCodigoIso($conexion) {
    $resultado = $conexion->query("SHOW COLUMNS FROM paises LIKE 'codigo_iso'");
    if ($resultado->num_rows == 0) {
        $conexion->query("ALTER TABLE paises ADD codigo_iso CHAR(2) AFTER nombre_pais");
    }
}

function obtenerPaisesSeleccionados($conexion, $lista_iso) {
    asegurarColumnaCodigoIso($conexion);

    $paisesData = callApi('http://api.geonames.org/countryInfoJSON?username=' . GEONAMES_USER);
    if (empty($paisesData['geonames'])) {
        throw new Exception("No se recibieron datos de países.");
    }

    $stmt = $conexion->prepare("INSERT IGNORE INTO paises (nombre_pais, codigo_iso) VALUES (?, ?)");

    foreach ($paisesData['geonames'] as $pais) {
        if (in_array($pais['countryCode'], $lista_iso)) {
            $stmt->bind_param("ss", $pais['countryName'], $pais['countryCode']);
            $stmt->execute();
        }
    }
    $stmt->close();
}

function obtenerCiudades($conexion, $id_pais, $iso_code) {
    if (strlen($iso_code) != 2) {
        throw new Exception("Código ISO inválido.");
    }

    $maxRows = 500;
    $startRow = 0;
    $totalCount = PHP_INT_MAX;

    $stmt = $conexion->prepare("INSERT IGNORE INTO ciudades (nombre_ciudad, id_pais) VALUES (?, ?)");

    while ($startRow < $totalCount) {
        $url = "http://api.geonames.org/searchJSON?country=$iso_code&featureClass=P&maxRows=$maxRows&startRow=$startRow&username=" . GEONAMES_USER;
        $data = callApi($url);

        if (empty($data['geonames'])) break;

        $totalCount = isset($data['totalResultsCount']) ? (int)$data['totalResultsCount'] : 0;
        if ($totalCount == 0) break;

        foreach ($data['geonames'] as $ciudad) {
            $nombre = $ciudad['name'];
            $stmt->bind_param("si", $nombre, $id_pais);
            $stmt->execute();
        }

        $startRow += $maxRows;
        usleep(500000);
    }

    $stmt->close();
}

echo "<div style='font-family:Arial;max-width:800px;margin:20px auto;line-height:1.6'>";
echo "<h1>🌍 Sincronización Países Top Turismo y sus Ciudades</h1>";

try {
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }

    // Lista actualizada con Argentina y Brasil
    $paisesTurismo = ['FR', 'ES', 'US', 'IT', 'MX', 'CN', 'TH', 'JP', 'DE', 'GB', 'AR', 'BR'];

    echo "<h3>Obteniendo países seleccionados...</h3>";
    obtenerPaisesSeleccionados($conexion, $paisesTurismo);

    $paises = $conexion->query("SELECT id_pais, nombre_pais, codigo_iso FROM paises WHERE codigo_iso IN ('" . implode("','", $paisesTurismo) . "')")->fetch_all(MYSQLI_ASSOC);

    foreach ($paises as $pais) {
        $nombre = htmlspecialchars($pais['nombre_pais']);
        $iso = $pais['codigo_iso'];

        echo "<div style='padding:10px; margin:10px 0; background:#eee; border-radius:5px'>";
        echo "<h4>Procesando $nombre ($iso)...</h4>";

        try {
            obtenerCiudades($conexion, $pais['id_pais'], $iso);
            echo "<p style='color:green;'>Ciudades actualizadas</p>";
        } catch (Exception $e) {
            echo "<p style='color:red;'>Error al obtener ciudades: " . $e->getMessage() . "</p>";
        }

        echo "</div>";
    }

    echo "<h2 style='color:green;'>✅ Sincronización completada</h2>";

} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}

echo "</div>";
$conexion->close();
?>

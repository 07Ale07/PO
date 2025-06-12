<?php
require '../conexion.php';

// Parámetros de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';

// Construir la consulta SQL con filtros
$sql = "SELECT a.*, c.nombre_ciudad 
        FROM actividad a
        LEFT JOIN ciudades c ON a.id_ciudad = c.id_ciudad
        WHERE (a.actividad LIKE ? OR c.nombre_ciudad LIKE ? OR a.precio LIKE ? OR a.descripcion LIKE ?)";

// Añadir filtro de estado si no es "todos"
if ($estado !== 'todos') {
    $sql .= " AND a.estado = ?";
}

$sql .= " ORDER BY a.actividad ASC";

// Preparar la consulta
$stmt = $conexion->prepare($sql);

if ($estado !== 'todos') {
    $searchParam = "%$search%";
    $stmt->bind_param("ssssi", $searchParam, $searchParam, $searchParam, $searchParam, $estado);
} else {
    $searchParam = "%$search%";
    $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin Actividades | Intercity Tour</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --color-primary: #3B82F6;
            --color-secondary: #10B981;
            --color-danger: #EF4444;
            --color-warning: #F59E0B;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }
        
        /* Estilos para el menú Intercity Tour */
        .menu-item {
            transition: all 0.2s ease;
            position: relative;
        }
        .menu-item:hover {
            color: #3B82F6;
        }
        .menu-item:hover::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #3B82F6;
        }
        .logo-text {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(90deg, #3B82F6, #10B981);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .slogan {
            font-size: 0.9rem;
            color: #6B7280;
        }
        
        /* Estilos para la sección de búsqueda */
        .search-section {
            background-image: url('../img/actividades/fond1.jpeg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .search-content {
            position: relative;
            z-index: 1;
        }
        
        .search-box {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        
        /* Estilos mejorados para las cards */
        .activity-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            background: white;
        }
        
        .activity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.15);
        }
        
        .activity-card.inactive {
            position: relative;
            opacity: 0.7;
        }
        
        .activity-image-container {
            height: 220px;
            position: relative;
            overflow: hidden;
        }
        
        .activity-image {
            height: 100%;
            width: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .activity-card:hover .activity-image {
            transform: scale(1.05);
        }
        
        .activity-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, transparent 100%);
            padding: 1.5rem 1.25rem 0.75rem;
            color: white;
            z-index: 1;
        }
        
        .activity-name {
            font-size: 1.375rem;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 0.25rem 0;
            letter-spacing: 0.5px;
        }
        
        .activity-location {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            opacity: 0.9;
        }
        
        .activity-location i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }
        
        .activity-content {
            padding: 1.25rem;
        }
        
        .activity-price-container {
            background-color: #F0F9FF;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .price-label {
            font-size: 0.75rem;
            color: #6B7280;
            margin-bottom: 0.25rem;
        }
        
        .price-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-primary);
            line-height: 1;
        }
        
        .price-person {
            font-size: 0.75rem;
            color: #9CA3AF;
        }
        
        .activity-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #F3F4F6;
        }
        
        .status-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            z-index: 10;
        }
        
        .status-active {
            background-color: var(--color-secondary);
            color: white;
        }
        
        .status-inactive {
            background-color: var(--color-danger);
            color: white;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #E5E7EB;
            transition: .4s;
            border-radius: 24px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: var(--color-secondary);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(20px);
        }
        
        .edit-btn {
            display: inline-flex;
            align-items: center;
            color: #D97706;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .edit-btn:hover {
            color: #B45309;
            transform: translateX(2px);
        }
        
        .edit-btn i {
            margin-right: 0.5rem;
            font-size: 0.875rem;
        }
        
        .no-results {
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
        }
        
        .filter-btn {
            transition: all 0.2s;
        }
        
        .filter-btn:hover {
            transform: translateY(-1px);
        }
        
        .filter-active {
            background-color: var(--color-primary);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
        }
        
        .description-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #4B5563;
            font-size: 0.875rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación Intercity Tour -->
    <nav class="bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <!-- Logo y slogan -->
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="mr-2">
                        <span class="logo-text">INTERCITY TOUR</span>
                    </div>
                    <div class="slogan hidden md:block">
                        Pagá menos por viajar
                    </div>
                </div>
                
                <!-- Menú principal -->
                <div class="flex space-x-1 md:space-x-6">
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Vuelos</a>
                    <a href="../hotel/hoteles.php" class="menu-item px-2 py-1 text-gray-800 font-medium">Hoteles</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Paquetes</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Autos</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Asistencias</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium text-blue-600">Actividades</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Micros</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Blog</a>
                </div>
                
                <!-- Selector de moneda y usuario -->
                <div class="flex items-center space-x-4 mt-4 md:mt-0">
                    <div class="flex items-center border rounded-full px-3 py-1">
                        <span class="text-sm mr-1">AR</span>
                        <span class="text-sm font-medium">(ARS)</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-600">N</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Barra de búsqueda con imagen de fondo -->
    <div class="search-section py-12">
        <div class="container mx-auto px-6 search-content">
            <form method="GET" action="" class="search-box rounded-xl p-6 max-w-4xl mx-auto">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-search mr-2"></i> Encontrá actividades
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Nombre, ciudad, precio o descripción" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   class="w-full p-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg w-full transition flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i> Buscar
                        </button>
                    </div>
                </div>
                
                <!-- Filtros de estado -->
                <div class="flex space-x-3 mt-4">
                    <a href="?search=<?php echo urlencode($search); ?>&estado=todos" 
                       class="filter-btn px-4 py-2 rounded-lg border border-gray-200 <?php echo $estado === 'todos' ? 'filter-active border-transparent' : 'bg-white'; ?>">
                        <i class="fas fa-layer-group mr-2"></i> Todos
                    </a>
                    <a href="?search=<?php echo urlencode($search); ?>&estado=1" 
                       class="filter-btn px-4 py-2 rounded-lg border border-gray-200 <?php echo $estado === '1' ? 'filter-active border-transparent' : 'bg-white'; ?>">
                        <i class="fas fa-check-circle mr-2"></i> Activos
                    </a>
                    <a href="?search=<?php echo urlencode($search); ?>&estado=0" 
                       class="filter-btn px-4 py-2 rounded-lg border border-gray-200 <?php echo $estado === '0' ? 'filter-active border-transparent' : 'bg-white'; ?>">
                        <i class="fas fa-times-circle mr-2"></i> Inactivos
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Contenido principal -->
    <main class="container mx-auto py-10 px-4 sm:px-6">
        <!-- Botón agregar y contador -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div class="text-gray-600 text-sm font-medium bg-white px-4 py-2 rounded-lg shadow-xs border border-gray-100">
                <i class="fas fa-ticket-alt mr-2 text-blue-500"></i>
                Mostrando <span class="font-bold text-blue-600"><?php echo $result->num_rows; ?></span> actividades
            </div>
            <div>
                <a href="A.agregarform.php" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg flex items-center transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i> Agregar Actividad
                </a>
            </div>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <!-- Grid de actividades -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php while ($fila = $result->fetch_assoc()): ?>
                    <div class="activity-card <?php echo ($fila['estado'] == 0) ? 'inactive' : ''; ?>">
                        <!-- Badge de estado -->
                        <div class="status-badge <?php echo ($fila['estado'] == 1) ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo ($fila['estado'] == 1) ? 'Activo' : 'Inactivo'; ?>
                        </div>
                        
                        <!-- Imagen de la actividad con overlay -->
                        <div class="activity-image-container">
                            <?php if (!empty($fila['imagenes'])): ?>
                                <?php 
                                $ruta_imagen = '/cristian/PO/administrador' . htmlspecialchars($fila['imagenes']);
                                ?>
                                <img src="<?php echo $ruta_imagen; ?>" alt="<?php echo htmlspecialchars($fila['actividad']); ?>" class="activity-image">
                            <?php else: ?>
                                <div class="activity-image bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <i class="fas fa-ticket-alt text-gray-300 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="activity-overlay">
                                <h3 class="activity-name"><?php echo htmlspecialchars($fila['actividad']); ?></h3>
                                <div class="activity-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($fila['nombre_ciudad'] ?? 'No especificada'); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contenido de la tarjeta -->
                        <div class="activity-content">
                            <div class="activity-price-container">
                                <div class="price-label">Precio por persona</div>
                                <div class="price-value">$<?php echo number_format($fila['precio'], 0, ',', '.'); ?></div>
                            </div>
                            
                            <div class="description-text mb-3">
                                <?php echo htmlspecialchars($fila['descripcion']); ?>
                            </div>
                            
                            <!-- Acciones - Toggle switch mejorado -->
                            <div class="activity-actions">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <span class="text-sm text-gray-600">Estado:</span>
                                    <div class="toggle-switch">
                                        <input type="checkbox" onchange="toggleEstado(<?php echo $fila['id_actividad']; ?>, this)" <?php echo ($fila['estado'] == 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </div>
                                </label>
                                <a href="A.updateform.php?codigo=<?php echo $fila['id_actividad']; ?>" 
                                   class="edit-btn">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-results text-center py-16 rounded-xl shadow-sm border border-gray-100">
                <div class="bg-white w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                    <i class="fas fa-ticket-alt text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700">No se encontraron actividades</h3>
                <p class="text-gray-500 mt-2 max-w-md mx-auto"><?php echo empty($search) ? 'No hay actividades registradas todavía.' : 'No hay resultados para tu búsqueda.'; ?></p>
                <?php if (!empty($search)): ?>
                    <a href="?" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-redo mr-2"></i> Mostrar todos
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
    function toggleEstado(actividadId, checkbox) {
        const nuevoEstado = checkbox.checked ? 1 : 0;
        const card = checkbox.closest('.activity-card');
        const badge = card.querySelector('.status-badge');

        fetch('update_estado_actividad.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_actividad=' + actividadId + '&estado=' + nuevoEstado
        })
        .then(response => response.text())
        .then(data => {
            if(data === 'OK') {
                // Actualizar la interfaz
                if (nuevoEstado === 1) {
                    // Si se activa
                    badge.classList.remove('status-inactive');
                    badge.classList.add('status-active');
                    badge.textContent = 'Activo';
                    card.classList.remove('inactive');
                } else {
                    // Si se desactiva
                    badge.classList.remove('status-active');
                    badge.classList.add('status-inactive');
                    badge.textContent = 'Inactivo';
                    card.classList.add('inactive');
                }
                
                // Recargar si estamos filtrados
                const urlParams = new URLSearchParams(window.location.search);
                const estadoFiltro = urlParams.get('estado');
                if(estadoFiltro && estadoFiltro !== 'todos') {
                    window.location.reload();
                }
            } else {
                alert('Error al actualizar el estado');
                checkbox.checked = !checkbox.checked; // Revertir el cambio
            }
        })
        .catch(() => {
            alert('Error en la conexión');
            checkbox.checked = !checkbox.checked; // Revertir el cambio
        });
    }
    </script>
</body>
</html>
<?php
$stmt->close();
$conexion->close();
?>
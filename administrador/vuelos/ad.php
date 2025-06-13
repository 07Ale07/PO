<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Vuelos | Intercity Tour</title>
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
            background-image: url('../img/vuelos/fondo-avion.jpg');
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
        
        /* Estilos para las cards de vuelos */
        .flight-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            background: white;
            margin-bottom: 1.5rem;
        }
        
        .flight-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.15);
        }
        
        .flight-card.inactive {
            position: relative;
            opacity: 0.7;
        }
        
        .flight-header {
            background-color: var(--color-primary);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .flight-price {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .flight-body {
            padding: 1.5rem;
        }
        
        .flight-route {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .flight-route::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e5e7eb;
            z-index: 1;
        }
        
        .flight-origin, .flight-destination {
            background-color: white;
            padding: 0 0.5rem;
            position: relative;
            z-index: 2;
        }
        
        .flight-origin {
            text-align: left;
        }
        
        .flight-destination {
            text-align: right;
        }
        
        .flight-city {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .flight-date {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .flight-icon {
            background-color: white;
            padding: 0 0.5rem;
            position: relative;
            z-index: 2;
        }
        
        .flight-details {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #f3f4f6;
        }
        
        .flight-class {
            background-color: #f0f9ff;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            color: var(--color-primary);
        }
        
        .flight-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--color-primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2563eb;
        }
        
        .btn-secondary {
            background-color: #f0f9ff;
            color: var(--color-primary);
        }
        
        .btn-secondary:hover {
            background-color: #e0f2fe;
        }
        
        .btn-danger {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .btn-danger:hover {
            background-color: #fecaca;
        }
        
        .btn-success {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .btn-success:hover {
            background-color: #bbf7d0;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        .form-control {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: border-color 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background-color: white;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6b7280;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 1.5rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background-color: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        
        .table tr:last-child td {
            border-bottom: none;
        }
        
        .table tr:hover td {
            background-color: #f9fafb;
        }
        
        .no-results {
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
            padding: 3rem 1rem;
            text-align: center;
            border-radius: 0.5rem;
        }
        
        .no-results-icon {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 1rem;
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
                    <a href="<?= BASE_URL ?>/administrador/vuelos/ad.php" class="menu-item px-2 py-1 text-gray-800 font-medium">Vuelos</a>
                    <a href="<?= BASE_URL ?>/administrador/hotel/hoteles.php" class="menu-item px-2 py-1 text-gray-800 font-medium">Hoteles</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Paquetes</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Autos</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Asistencias</a>
                    <a href="<?= BASE_URL ?>/administrador/actividad/actividad.php" class="menu-item px-2 py-1 text-gray-800 font-medium">Actividades</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Micros</a>
                    <a href="#" class="menu-item px-2 py-1 text-gray-800 font-medium">Blog</a>
                </div>
                
                <!-- Cerrar sesión -->
                <div class="mt-4 md:mt-0">
                    <form action="<?= BASE_URL ?>/administrador/logouts/cerrar_sesion.php" method="POST">
                        <input type="hidden" name="cerrar_sesion" value="1">
                        <button type="submit" class="flex items-center text-red-600 hover:text-red-800 transition">
                            <i class="fas fa-sign-out-alt mr-2"></i> Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Barra de búsqueda con imagen de fondo -->
    <div class="search-section py-12">
        <div class="container mx-auto px-6 search-content">
            <div class="search-box rounded-xl p-6 max-w-4xl mx-auto">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-plane mr-2"></i> Gestión de Vuelos
                </h2>
                
                <div class="flex justify-between items-center">
                    <div class="text-gray-600 text-sm font-medium bg-white px-4 py-2 rounded-lg shadow-xs border border-gray-100">
                        <i class="fas fa-plane mr-2 text-blue-500"></i>
                        Total de vuelos: <span class="font-bold text-blue-600" id="total-vuelos">0</span>
                    </div>
                    
                    <button id="btn-agregar-vuelo" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg flex items-center transition-all shadow-md hover:shadow-lg">
                        <i class="fas fa-plus mr-2"></i> Agregar Vuelo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <main class="container mx-auto py-10 px-4 sm:px-6">
        <!-- Lista de vuelos -->
        <div class="table-container">
            <table class="table" id="tabla-vuelos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha y Hora</th>
                        <th>Precio</th>
                        <th>Clase</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los vuelos se cargarán aquí con JavaScript -->
                </tbody>
            </table>
            
            <div id="no-results" class="no-results" style="display: none;">
                <div class="no-results-icon">
                    <i class="fas fa-plane-slash"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700">No se encontraron vuelos</h3>
                <p class="text-gray-500 mt-2">No hay vuelos registrados todavía.</p>
            </div>
        </div>
    </main>

    <!-- Modal para agregar/editar vuelo -->
    <div class="modal" id="modal-vuelo">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal-titulo">Agregar Nuevo Vuelo</h3>
                <button class="modal-close" id="modal-cerrar">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-vuelo">
                    <input type="hidden" name="id_vuelo" id="id_vuelo">
                    
                    <div class="form-group">
                        <label class="form-label">Origen</label>
                        <div class="flex items-center gap-2">
                            <select name="lugar_partida" id="lugar_partida" class="form-control select-pais" required></select>
                            <button type="button" id="btn-agregar-pais" class="btn btn-secondary">
                                <i class="fas fa-plus mr-1"></i> País
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Destino</label>
                        <select name="destino" id="destino" class="form-control select-pais" required></select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Fecha y Hora</label>
                        <input type="datetime-local" name="fecha_hora" id="fecha_hora" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Precio</label>
                        <input type="number" name="precio" id="precio" class="form-control" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Clase</label>
                        <input type="text" name="clase" id="clase" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Imagen URL</label>
                        <input type="text" name="img" id="img" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="modal-cancelar">Cancelar</button>
                <button type="button" class="btn btn-primary" id="modal-guardar">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para agregar país -->
    <div class="modal" id="modal-pais">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Agregar Nuevo País</h3>
                <button class="modal-close" id="modal-pais-cerrar">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-pais">
                    <div class="form-group">
                        <label class="form-label">Nombre del País</label>
                        <input type="text" name="nombre_pais" id="nombre_pais" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="modal-pais-cancelar">Cancelar</button>
                <button type="button" class="btn btn-primary" id="modal-pais-guardar">Guardar</button>
            </div>
        </div>
    </div>

    <script>
    // Variables globales
    let vuelos = [];
    let paises = [];
    
    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar datos iniciales
        cargarPaises();
        cargarVuelos();
        
        // Event listeners
        document.getElementById('btn-agregar-vuelo').addEventListener('click', mostrarModalAgregar);
        document.getElementById('modal-cerrar').addEventListener('click', cerrarModal);
        document.getElementById('modal-cancelar').addEventListener('click', cerrarModal);
        document.getElementById('modal-guardar').addEventListener('click', guardarVuelo);
        
        document.getElementById('btn-agregar-pais').addEventListener('click', mostrarModalPais);
        document.getElementById('modal-pais-cerrar').addEventListener('click', cerrarModalPais);
        document.getElementById('modal-pais-cancelar').addEventListener('click', cerrarModalPais);
        document.getElementById('modal-pais-guardar').addEventListener('click', guardarPais);
    });
    
    // Funciones para cargar datos
    function cargarPaises() {
        fetch('obtener_paises.php')
            .then(response => response.json())
            .then(data => {
                paises = data;
                actualizarSelectsPaises();
            })
            .catch(error => console.error('Error al cargar países:', error));
    }
    
    function cargarVuelos() {
        fetch('obtener_vuelos.php')
            .then(response => response.json())
            .then(data => {
                vuelos = data;
                actualizarTablaVuelos();
                document.getElementById('total-vuelos').textContent = vuelos.length;
                
                if (vuelos.length === 0) {
                    document.getElementById('no-results').style.display = 'block';
                    document.getElementById('tabla-vuelos').style.display = 'none';
                } else {
                    document.getElementById('no-results').style.display = 'none';
                    document.getElementById('tabla-vuelos').style.display = 'table';
                }
            })
            .catch(error => console.error('Error al cargar vuelos:', error));
    }
    
    // Funciones para actualizar la UI
    function actualizarSelectsPaises() {
        const selects = document.querySelectorAll('.select-pais');
        selects.forEach(select => {
            // Guardar el valor seleccionado actual
            const selectedValue = select.value;
            
            // Limpiar el select
            select.innerHTML = '';
            
            // Agregar la opción por defecto
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Seleccione un país';
            defaultOption.disabled = true;
            defaultOption.selected = true;
            select.appendChild(defaultOption);
            
            // Agregar las opciones de países
            paises.forEach(pais => {
                const option = document.createElement('option');
                option.value = pais.id_pais;
                option.textContent = pais.nombre_pais;
                select.appendChild(option);
            });
            
            // Restaurar el valor seleccionado si existe
            if (selectedValue && paises.some(p => p.id_pais == selectedValue)) {
                select.value = selectedValue;
            }
        });
    }
    
    function actualizarTablaVuelos() {
        const tbody = document.querySelector('#tabla-vuelos tbody');
        tbody.innerHTML = '';
        
        vuelos.forEach(vuelo => {
            const tr = document.createElement('tr');
            
            // Encontrar los nombres de los países
            const paisOrigen = paises.find(p => p.id_pais == vuelo.lugar_partida)?.nombre_pais || 'Desconocido';
            const paisDestino = paises.find(p => p.id_pais == vuelo.destino)?.nombre_pais || 'Desconocido';
            
            // Formatear fecha
            const fecha = new Date(vuelo.fecha_hora);
            const fechaFormateada = fecha.toLocaleString('es-AR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Formatear precio
            const precioFormateado = new Intl.NumberFormat('es-AR', {
                style: 'currency',
                currency: 'ARS'
            }).format(vuelo.precio);
            
            tr.innerHTML = `
                <td>${vuelo.id_vuelo}</td>
                <td>${paisOrigen}</td>
                <td>${paisDestino}</td>
                <td>${fechaFormateada}</td>
                <td>${precioFormateado}</td>
                <td><span class="flight-class">${vuelo.clase}</span></td>
                <td>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <div class="toggle-switch">
                            <input type="checkbox" onchange="toggleEstado(${vuelo.id_vuelo}, this)" ${vuelo.estado == 1 ? 'checked' : ''}>
                            <span class="toggle-slider"></span>
                        </div>
                        <span class="status-badge ${vuelo.estado == 1 ? 'status-active' : 'status-inactive'}">
                            ${vuelo.estado == 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </label>
                </td>
                <td class="flex gap-2">
                    <button onclick="editarVuelo(${vuelo.id_vuelo})" class="btn btn-secondary">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="confirmarEliminar(${vuelo.id_vuelo})" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            if (vuelo.estado == 0) {
                tr.classList.add('inactive');
            }
            
            tbody.appendChild(tr);
        });
    }
    
    // Funciones para los modales
    function mostrarModalAgregar() {
        document.getElementById('modal-titulo').textContent = 'Agregar Nuevo Vuelo';
        document.getElementById('form-vuelo').reset();
        document.getElementById('id_vuelo').value = '';
        document.getElementById('modal-vuelo').style.display = 'flex';
    }
    
    function mostrarModalEditar(idVuelo) {
        const vuelo = vuelos.find(v => v.id_vuelo == idVuelo);
        if (!vuelo) return;
        
        document.getElementById('modal-titulo').textContent = 'Editar Vuelo';
        document.getElementById('id_vuelo').value = vuelo.id_vuelo;
        document.getElementById('lugar_partida').value = vuelo.lugar_partida;
        document.getElementById('destino').value = vuelo.destino;
        
        // Formatear fecha para el input datetime-local
        const fecha = new Date(vuelo.fecha_hora);
        const fechaLocal = new Date(fecha.getTime() - (fecha.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
        document.getElementById('fecha_hora').value = fechaLocal;
        
        document.getElementById('precio').value = vuelo.precio;
        document.getElementById('clase').value = vuelo.clase;
        document.getElementById('img').value = vuelo.img || '';
        
        document.getElementById('modal-vuelo').style.display = 'flex';
    }
    
    function cerrarModal() {
        document.getElementById('modal-vuelo').style.display = 'none';
    }
    
    function mostrarModalPais() {
        document.getElementById('form-pais').reset();
        document.getElementById('modal-pais').style.display = 'flex';
    }
    
    function cerrarModalPais() {
        document.getElementById('modal-pais').style.display = 'none';
    }
    
    // Funciones CRUD
    function guardarVuelo() {
        const form = document.getElementById('form-vuelo');
        const formData = new FormData(form);
        const idVuelo = formData.get('id_vuelo');
        
        const url = idVuelo ? 'actualizar_vuelo.php' : 'agregar_vuelo.php';
        const method = 'POST';
        
        fetch(url, {
            method: method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cerrarModal();
                cargarVuelos();
            } else {
                alert('Error: ' + (data.message || 'No se pudo guardar el vuelo'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar el vuelo');
        });
    }
    
    function guardarPais() {
        const nombrePais = document.getElementById('nombre_pais').value.trim();
        if (!nombrePais) return;
        
        const formData = new FormData();
        formData.append('nombre_pais', nombrePais);
        
        fetch('agregar_pais.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cerrarModalPais();
                cargarPaises();
            } else {
                alert('Error: ' + (data.message || 'No se pudo agregar el país'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar el país');
        });
    }
    
    function toggleEstado(idVuelo, checkbox) {
        const nuevoEstado = checkbox.checked ? 1 : 0;
        
        const formData = new FormData();
        formData.append('id_vuelo', idVuelo);
        formData.append('estado', nuevoEstado);
        
        fetch('actualizar_estado_vuelo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Error al actualizar el estado');
                checkbox.checked = !checkbox.checked;
            } else {
                cargarVuelos();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
            checkbox.checked = !checkbox.checked;
        });
    }
    
    function editarVuelo(idVuelo) {
        mostrarModalEditar(idVuelo);
    }
    
    function confirmarEliminar(idVuelo) {
        if (confirm('¿Estás seguro de que deseas eliminar este vuelo?')) {
            eliminarVuelo(idVuelo);
        }
    }
    
    function eliminarVuelo(idVuelo) {
        const formData = new FormData();
        formData.append('id_vuelo', idVuelo);
        
        fetch('eliminar_vuelo.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargarVuelos();
            } else {
                alert('Error: ' + (data.message || 'No se pudo eliminar el vuelo'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el vuelo');
        });
    }
    
    // Hacer funciones accesibles globalmente
    window.toggleEstado = toggleEstado;
    window.editarVuelo = editarVuelo;
    window.confirmarEliminar = confirmarEliminar;
    </script>
</body>
</html>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Vuelos Disponibles</title>
  <style>
   <style>
  body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    margin: 20px;
  }
  h1 {
    text-align: center;
    color: #333;
  }

  #contenedor-vuelos {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
  }

  .vuelo {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
    width: calc(25% - 20px); /* 4 por fila */
    box-sizing: border-box;
  }

  .vuelo img {
    max-width: 100%;
    height: auto;
    border-radius: 5px;
  }

  .info {
    margin-top: 10px;
  }

  .info p {
    margin: 5px 0;
  }

  /* Adaptativo para móviles */
  @media (max-width: 992px) {
    .vuelo {
      width: calc(50% - 20px); /* 2 por fila */
    }
  }

  @media (max-width: 600px) {
    .vuelo {
      width: 100%; /* 1 por fila */
    }
  }
</style>

  </style>
</head>
<body>
  <h1>Vuelos Disponibles</h1>
  <div id="contenedor-vuelos"></div>

  <script>
    fetch('vuelos.php')
      .then(res => res.json())
      .then(data => {
        const contenedor = document.getElementById('contenedor-vuelos');
        const disponibles = data.filter(vuelo => vuelo.estado == "1");

        if (disponibles.length === 0) {
          contenedor.innerHTML = "<p>No hay vuelos disponibles.</p>";
          return;
        }

        disponibles.forEach(vuelo => {
          const div = document.createElement('div');
          div.className = 'vuelo';
          div.innerHTML = `
           <img src="${vuelo.img}" alt="Imagen del vuelo">

            <div class="info">
              <p><strong>Desde:</strong> ${vuelo.lugar_partida}</p>
              <p><strong>Hacia:</strong> ${vuelo.destino}</p>
              <p><strong>Fecha y Hora:</strong> ${vuelo.fecha_hora}</p>
              <p><strong>Precio:</strong> $${vuelo.precio}</p>
              <p><strong>Clase:</strong> ${vuelo.clase}</p>
            </div>
          `;
          contenedor.appendChild(div);
        });
      })
      .catch(err => {
        console.error("Error al obtener los vuelos:", err);
        document.getElementById('contenedor-vuelos').innerHTML = "<p>Error al cargar los vuelos.</p>";
      });
  </script>
</body>
</html>

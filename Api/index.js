const express = require('express');
const dotenv = require('dotenv');
const db = require('./db'); // archivo de conexión MySQL

dotenv.config();

const app = express();
const port = process.env.PORT || 3000;

app.use(express.json());

// Ruta para obtener todos los paquetes de ventas
app.get('/paquete_ventas', (req, res) => {
  const sql = 'SELECT * FROM paquete_ventas';

  db.query(sql, (err, results) => {
    if (err) {
      console.error('Error al consultar la base de datos:', err);
      return res.status(500).json({ error: 'Error en la consulta' });
    }
    res.json(results);
  });
});

//para los usuarios
app.get('/usuarios', (req, res) => {
    const sql = 'SELECT id_usuario, usuario, clave, correo FROM usuarios';
  
    db.query(sql, (err, results) => {
      if (err) {
        console.error('Error al obtener usuarios:', err);
        return res.status(500).json({ error: 'Error en la consulta' });
      }
      res.json(results);
    });
  });
  
  // Ruta para login
  app.post('/login', (req, res) => {
    const { usuario, clave } = req.body;
  
    const sql = 'SELECT * FROM usuarios WHERE usuario = ? AND clave = ? LIMIT 1';
    db.query(sql, [usuario, clave], (err, results) => {
      if (err) {
        console.error('Error en login:', err);
        return res.status(500).json({ error: 'Error interno' });
      }
  
      if (results.length === 0) {
        return res.status(401).json({ error: 'Credenciales incorrectas' });
      }
  
      res.json({ nombre: results[0].usuario });
    });
  });


// Ruta para obtener el top de hoteles, vuelos y vehículos más usados
app.get('/top-uso', (req, res) => {
  const topHoteles = 'SELECT id_hotel_estadia FROM paquete_ventas GROUP BY id_hotel_estadia ORDER BY COUNT(*) DESC LIMIT 5';
  const topVuelos = 'SELECT id_vuelo FROM paquete_ventas GROUP BY id_vuelo ORDER BY COUNT(*) DESC LIMIT 5';
  const topVehiculos = 'SELECT id_vehiculo FROM paquete_ventas GROUP BY id_vehiculo ORDER BY COUNT(*) DESC LIMIT 5';

  const resultados = { hoteles: [], vuelos: [], vehiculos: [] };

  db.query(topHoteles, (err, hoteles) => {
    if (err) {
      console.error('Error al consultar hoteles:', err);
      return res.status(500).json({ error: 'Error hoteles' });
    }

    resultados.hoteles = hoteles.map(h => h.id_hotel_estadia);

    db.query(topVuelos, (err, vuelos) => {
      if (err) {
        console.error('Error al consultar vuelos:', err);
        return res.status(500).json({ error: 'Error vuelos' });
      }

      resultados.vuelos = vuelos.map(v => v.id_vuelo);

      db.query(topVehiculos, (err, vehiculos) => {
        if (err) {
          console.error('Error al consultar vehículos:', err);
          return res.status(500).json({ error: 'Error vehiculos' });
        }

        resultados.vehiculos = vehiculos.map(v => v.id_vehiculo);

        res.json(resultados);
      });
    });
  });
});

app.listen(port, () => {
  console.log(`Servidor corriendo en http://localhost:${port}`);
});

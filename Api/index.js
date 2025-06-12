const express = require('express');
const dotenv = require('dotenv');
const cors = require('cors');
const db = require('./db');

dotenv.config();

const app = express();
const port = process.env.PORT || 3000;

app.use(cors()); // Habilita CORS para todas las rutas
app.use(express.json());

// Ruta para login
app.post('/login', (req, res) => {
  const { usuario, clave } = req.body;

  if (!usuario || !clave) {
    return res.status(400).json({ error: 'Faltan datos' });
  }

  const sql = 'SELECT * FROM usuarios WHERE usuario = ? AND clave = ? LIMIT 1';
  db.query(sql, [usuario, clave], (err, results) => {
    if (err) {
      console.log('Error en login:', err);
      return res.status(500).json({ error: 'Error interno' });
    }

    if (results.length === 0) {
      return res.status(401).json({ error: 'Credenciales incorrectas' });
    }

    res.json({ success: true, nombre: results[0].usuario });
  });
});

// Ver todos los usuarios
app.get('/usuarios', (req, res) => {
  const sql = 'SELECT id_usuario, usuario, clave, correo FROM usuarios';
  db.query(sql, (err, results) => {
    if (err) return res.status(500).json({ error: 'Error en la consulta' });
    res.json(results);
  });
});

app.listen(port, () => {
  console.log(`Servidor corriendo en http://localhost:${port}`);
});

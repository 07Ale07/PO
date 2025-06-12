function cargarVuelos() {
  fetch('vuelos.php')
    .then(r => r.json())
    .then(data => {
      let tbody = document.querySelector('#tabla-vuelos tbody');
      tbody.innerHTML = '';
      data.forEach(v => {
        let tr = document.createElement('tr');
        let accionBtn = v.estado == 1
          ? `<button onclick='cambiarEstado(${v.id_vuelo})'>Deshabilitar</button>`
          : `<button onclick='cambiarEstado(${v.id_vuelo})'>Habilitar</button>`;
        tr.innerHTML = `
          <td>${v.id_vuelo}</td>
          <td>${v.lugar_partida}</td>
          <td>${v.destino}</td>
          <td>${v.fecha_hora}</td>
          <td>${v.precio}</td>
          <td>${v.clase}</td>
          <td>${v.estado}</td>
          <td><img src="${v.img}" width="50"></td>
          <td>
            <button onclick='editar(${JSON.stringify(v)})'>Editar</button>
            ${accionBtn}
          </td>
        `;
        tbody.appendChild(tr);
      });
    });
}

function cargarPaises() {
  fetch('vuelos.php?tipo=paises')
    .then(r => r.json())
    .then(paises => {
      let selects = document.querySelectorAll('.select-pais');
      selects.forEach(select => {
        select.innerHTML = paises.map(p => `<option value="${p.nombre_pais}">${p.nombre_pais}</option>`).join('');
      });
    });
}

document.getElementById('form-agregar').addEventListener('submit', e => {
  e.preventDefault();
  let datos = Object.fromEntries(new FormData(e.target));
  fetch('vuelos.php', {
    method: 'POST',
    body: JSON.stringify(datos)
  }).then(() => {
    cargarVuelos();
    e.target.reset();
  });
});

function editar(vuelo) {
  let f = document.getElementById('form-modificar');
  f.id_vuelo.value = vuelo.id_vuelo;
  f.lugar_partida.value = vuelo.lugar_partida;
  f.destino.value = vuelo.destino;
  f.fecha_hora.value = vuelo.fecha_hora;
  f.precio.value = vuelo.precio;
  f.clase.value = vuelo.clase;
  f.img.value = vuelo.img;
  document.getElementById('form-modificar-contenedor').style.display = 'block';
}

document.getElementById('form-modificar').addEventListener('submit', e => {
  e.preventDefault();
  let datos = Object.fromEntries(new FormData(e.target));
  fetch('vuelos.php', {
    method: 'PUT',
    body: JSON.stringify(datos)
  }).then(() => {
    cargarVuelos();
    e.target.reset();
    document.getElementById('form-modificar-contenedor').style.display = 'none';
  });
});

function cambiarEstado(id) {
  fetch('vuelos.php', {
    method: 'DELETE',
    body: JSON.stringify({ id_vuelo: id })
  }).then(cargarVuelos);
}

function agregarPais(nombre) {
  fetch('vuelos.php', {
    method: 'POST',
    body: JSON.stringify({ nombre_pais: nombre })
  }).then(cargarPaises);
}

document.getElementById('btn-agregar-pais').addEventListener('click', () => {
  let nombre = prompt('Nombre del nuevo país:');
  if (nombre) agregarPais(nombre);
});

document.getElementById('btn-agregar-pais-mod').addEventListener('click', () => {
  let nombre = prompt('Nombre del nuevo país:');
  if (nombre) agregarPais(nombre);
});

cargarPaises();
cargarVuelos();

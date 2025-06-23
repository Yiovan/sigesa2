const API_URL = "http://localhost:8000/Backend/api.php";

window.onload = () => {
  fetchTareas();
};

function fetchTareas() {
  fetch(API_URL)
    .then(res => res.json())
    .then(data => {
      const contenedor = document.getElementById("listaTareas");
      contenedor.innerHTML = "";

      if (data.length === 0) {
        contenedor.innerHTML = "<p style='text-align:center;'>No hay tareas registradas.</p>";
        return;
      }

      data.forEach(t => {
  const div = document.createElement("div");
  div.classList.add("tarea");

  const fecha = new Date(t.fecha_creacion).toLocaleDateString('es-ES', {
    year: 'numeric', month: 'long', day: 'numeric'
  });

  div.innerHTML = `
    <span><strong>${t.titulo}</strong> (${t.estado})</span>
    <p>${t.descripcion}</p>
    <em>${t.nombre} - ${t.correo}</em><br>
    <small>📅 Creado el: ${fecha}</small><br>
    <button onclick="marcarCompletada(${t.id})" style="background-color: gold;">✔ Completar</button>
    <button onclick="eliminarTarea(${t.id})" style="background-color: crimson; color: white;">✖ Eliminar</button>
  `;

  contenedor.appendChild(div);
});

    })
    .catch(err => console.error("❌ Error al cargar tareas:", err));
}

function agregarTarea() {
  const nombre = document.getElementById("nombre").value;
  const correo = document.getElementById("correo").value;
  const titulo = document.getElementById("titulo").value;
  const descripcion = document.getElementById("descripcion").value;
  const estado = document.getElementById("estado").value;

  fetch(API_URL, { // <-- Aquí está la corrección
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ nombre, correo, titulo, descripcion, estado })
  })
  .then(res => res.json())
  .then(res => {
    console.log(res);
    fetchTareas();  // Actualiza la lista tras insertar
  })
  .catch(err => console.error("❌ Error al agregar tarea:", err));
}


function marcarCompletada(id) {
  // Buscar la tarea por ID para traer su título y descripción actuales
  fetch(`${API_URL}?id=${id}`)
    .then(res => res.json())
    .then(tarea => {
      fetch(`${API_URL}?id=${id}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          titulo: tarea.titulo,
          descripcion: tarea.descripcion,
          estado: "completada"
        })
      })
      .then(res => res.json())
      .then(() => fetchTareas())
      .catch(err => console.error("❌ Error al actualizar:", err));
      console.log(`Tarea con ID ${id} marcada como completada.`);
    });
}


function eliminarTarea(id) {
  fetch(`${API_URL}?id=${id}`, {
  method: "DELETE"
})
.then(res => res.json())
.then(() => fetchTareas())
.catch(err => console.error("❌ Error al eliminar:", err));

  console.log(`Tarea con ID ${id} eliminada.`);
}

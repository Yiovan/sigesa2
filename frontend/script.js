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

        div.innerHTML = `
          <span><strong>${t.titulo}</strong> (${t.estado})</span>
          <p>${t.descripcion}</p>
          <em>${t.nombre} - ${t.correo}</em><br>
          <button onclick="marcarCompletada(${t.id})">✔ Completar</button>
          <button onclick="eliminarTarea(${t.id})">✖ Eliminar</button>
        `;

        contenedor.appendChild(div);
      });
    })
    .catch(err => console.error("❌ Error al cargar tareas:", err));
}

function agregarTarea() {
  const nombre = document.getElementById("nombre").value.trim();
  const correo = document.getElementById("correo").value.trim();
  const titulo = document.getElementById("titulo").value.trim();
  const descripcion = document.getElementById("descripcion").value.trim();
  const estado = document.getElementById("estado").value;

  if (!nombre || !correo || !titulo) {
    alert("Nombre, correo y título son obligatorios.");
    return;
  }

  const tarea = { nombre, correo, titulo, descripcion, estado };
  console.log("📤 Enviando tarea:", tarea);

  fetch(API_URL, {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify(tarea)
})

    .then(res => res.json())
    .then(response => {
      console.log("✅ Respuesta del servidor:", response);
      document.getElementById("titulo").value = "";
      document.getElementById("descripcion").value = "";
      fetchTareas();
    })
    .catch(err => console.error("❌ Error al agregar tarea:", err));
}

function marcarCompletada(id) {
  fetch(API_URL, {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(() => fetchTareas());
}

function eliminarTarea(id) {
  fetch(API_URL, {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(() => fetchTareas());
}

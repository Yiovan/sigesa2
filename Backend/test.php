<?php
require 'db.php';

// Agregar tarea y usuario si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';

    if ($titulo !== '' && $nombre !== '' && $correo !== '') {
        // Insertar nuevo usuario
        $stmtUsuario = $pdo->prepare("INSERT INTO usuarios (nombre, correo) VALUES (?, ?)");
        $stmtUsuario->execute([$nombre, $correo]);
        $usuario_id = $pdo->lastInsertId();

        // Insertar tarea asociada al nuevo usuario
        $stmtTarea = $pdo->prepare("INSERT INTO tareas (titulo, descripcion, estado, usuario_id) VALUES (?, ?, 'pendiente', ?)");
        $stmtTarea->execute([$titulo, $descripcion, $usuario_id]);

        header("Location: test.php");
        exit;
    }
}

// Obtener todas las tareas con nombre de usuario
$tareas = $pdo->query("
    SELECT t.*, u.nombre AS nombre_usuario 
    FROM tareas t 
    JOIN usuarios u ON t.usuario_id = u.id 
    ORDER BY t.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tareas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eee; padding: 20px; }
        h1 { text-align: center; }
        form { text-align: center; margin-bottom: 20px; }
        input, textarea { width: 300px; padding: 8px; margin: 5px; }
        button { padding: 8px 16px; }
        .tarea { background: white; padding: 15px; margin: 10px auto; max-width: 600px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        small { color: #555; }
    </style>
</head>
<body>
    <h1>📝 Lista de Tareas</h1>

    <form method="POST">
        <input type="text" name="titulo" placeholder="Título de la tarea" required><br>
        <textarea name="descripcion" placeholder="Descripción (opcional)"></textarea><br>
        <input type="text" name="nombre" placeholder="Nombre del usuario" required><br>
        <input type="email" name="correo" placeholder="Correo del usuario" required><br>
        <button type="submit">Agregar Tarea</button>
    </form>

    <?php if (count($tareas) === 0): ?>
        <p style="text-align:center;">No hay tareas registradas.</p>
    <?php else: ?>
        <?php foreach ($tareas as $t): ?>
            <div class="tarea">
                <strong><?= htmlspecialchars($t['titulo']) ?></strong>
                <p><?= nl2br(htmlspecialchars($t['descripcion'])) ?></p>
                <small>
                    Usuario: <?= htmlspecialchars($t['nombre_usuario']) ?> |
                    Estado: <?= htmlspecialchars($t['estado']) ?> |
                    Creado: <?= $t['fecha_creacion'] ?>
                </small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

<?php
require 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

try {
    switch ($method) {
        case "GET":
            $stmt = $pdo->query("
                SELECT tareas.*, usuarios.nombre, usuarios.correo 
                FROM tareas 
                JOIN usuarios ON tareas.usuario_id = usuarios.id 
                ORDER BY tareas.id DESC
            ");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case "POST":
  $nombre = $data["nombre"] ?? '';
  $correo = $data["correo"] ?? '';
  $titulo = $data["titulo"] ?? '';
  $descripcion = $data["descripcion"] ?? '';
  $estado = $data["estado"] ?? 'pendiente';

  if (!$nombre || !$correo || !$titulo) {
    echo json_encode(["error" => "Faltan campos obligatorios"]);
    exit;
  }

  try {
    // Insertar nuevo usuario
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo) VALUES (?, ?)");
    $stmt->execute([$nombre, $correo]);
    $usuario_id = $pdo->lastInsertId();

    // Insertar tarea asociada
    $stmt = $pdo->prepare("INSERT INTO tareas (titulo, descripcion, estado, usuario_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$titulo, $descripcion, $estado, $usuario_id]);

    echo json_encode(["success" => true]);
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error interno: " . $e->getMessage()]);
  }
  break;


        case "PUT":
            $id = $data["id"] ?? 0;
            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "ID de tarea no proporcionado"]);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE tareas SET estado = 'completada' WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["mensaje" => "Tarea completada"]);
            break;

        case "DELETE":
            $id = $data["id"] ?? 0;
            if (!$id) {
                http_response_code(400);
                echo json_encode(["error" => "ID de tarea no proporcionado"]);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM tareas WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["mensaje" => "Tarea eliminada"]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error del servidor: " . $e->getMessage()]);
}

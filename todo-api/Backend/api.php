<?php
require 'db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

// Detectar parámetros por GET
parse_str($_SERVER["QUERY_STRING"] ?? "", $params);

switch ($method) {
    case "GET":
        if (isset($params["id"])) {
            $id = (int)$params["id"];
            $stmt = $pdo->prepare("
                SELECT tareas.*, usuarios.nombre, usuarios.correo
                FROM tareas 
                JOIN usuarios ON tareas.usuario_id = usuarios.id 
                WHERE tareas.id = ?
            ");
            $stmt->execute([$id]);
            $tarea = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tarea) {
                echo json_encode($tarea);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Tarea no encontrada"]);
            }
        } else {
            $stmt = $pdo->query("
                SELECT tareas.id, tareas.titulo, tareas.descripcion, tareas.estado, tareas.fecha_creacion,
                       usuarios.nombre, usuarios.correo
                FROM tareas 
                JOIN usuarios ON tareas.usuario_id = usuarios.id 
                ORDER BY tareas.id DESC
            ");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case "POST":
        if (!isset($data["nombre"], $data["correo"], $data["titulo"], $data["estado"])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan campos requeridos"]);
            break;
        }

        $nombre = $data["nombre"];
        $correo = $data["correo"];
        $titulo = $data["titulo"];
        $descripcion = $data["descripcion"] ?? "";
        $estado = $data["estado"];

        $stmtUsuario = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmtUsuario->execute([$correo]);
        $usuario = $stmtUsuario->fetch();

        if (!$usuario) {
            $stmtInsertUsuario = $pdo->prepare("INSERT INTO usuarios (nombre, correo) VALUES (?, ?)");
            $stmtInsertUsuario->execute([$nombre, $correo]);
            $usuario_id = $pdo->lastInsertId();
        } else {
            $usuario_id = $usuario['id'];
        }

        $stmt = $pdo->prepare("
            INSERT INTO tareas (titulo, descripcion, estado, usuario_id) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$titulo, $descripcion, $estado, $usuario_id]);

        echo json_encode(["mensaje" => "Tarea agregada exitosamente"]);
        break;

   case "PUT":
    $id = $params["id"] ?? null;
    if (!$id || !isset($data["estado"])) {
        http_response_code(400);
        echo json_encode(["error" => "Falta estado o id"]);
        break;
    }

    $stmt = $pdo->prepare("UPDATE tareas SET estado = ? WHERE id = ?");
    $stmt->execute([$data["estado"], $id]);

    echo json_encode(["mensaje" => "Estado actualizado"]);
    break;

    case "DELETE":
        $id = $params["id"] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM tareas WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["mensaje" => "Tarea eliminada"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID no especificado"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}

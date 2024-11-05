<?php
require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = $data['id_usuario'];

$query = "DELETE FROM usuario WHERE id_usuario = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_usuario);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al eliminar usuario."]);
}

$stmt->close();
$mysqli->close();
?>
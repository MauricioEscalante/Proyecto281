<?php
require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$id_usuario = $data['id_usuario'];
$nombre = $data['nombre'];
$email = $data['email'];
$telefono = $data['telefono'];
$tipo_idtipo = $data['tipo_idtipo'];

$query = "UPDATE usuario SET nombre = ?, email = ?, telefono = ?, tipo_idtipo = ? WHERE id_usuario = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ssisi", $nombre, $email, $telefono, $tipo_idtipo, $id_usuario);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar usuario."]);
}

$stmt->close();
$mysqli->close();
?>
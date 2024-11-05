<?php
require "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'];
$email = $data['email'];
$telefono = $data['telefono'];
$tipo_idtipo = $data['tipo_idtipo'];
$contraseña = password_hash($data['contraseña'], PASSWORD_DEFAULT);

$query = "INSERT INTO usuario (nombre, email, telefono, tipo_idtipo, contraseña) VALUES (?, ?, ?, ?, ?)";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ssiss", $nombre, $email, $telefono, $tipo_idtipo, $contraseña);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error al agregar usuario."]);
}

$stmt->close();
$mysqli->close();
?>
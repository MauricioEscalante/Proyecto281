<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = $_POST['producto'];
    $id_usuario = $_SESSION['idUSUARIO']; // Suponiendo que el ID del usuario está en la sesión
    $comentario = $_POST['comentario'];
    $calificacion = $_POST['calificacion'];
    $fecha_actual = date('Y-m-d H:i:s'); // Obtener la fecha actual

    // Consulta para insertar la reseña
    $insertQuery = "INSERT INTO reseña (id_producto, id_usuario, comentario, calificacion,fecha) VALUES ('$id_producto', '$id_usuario', '$comentario', '$calificacion','$fecha_actual')";
    $insertResult = mysqli_query($mysqli, $insertQuery);

    if ($insertResult) {
        echo json_encode(['success' => true, 'message' => 'Reseña agregada exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar la reseña: ' . mysqli_error($mysqli)]);
    }
    exit;
}
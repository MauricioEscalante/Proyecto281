<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autorizado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = (int)json_decode(file_get_contents("php://input"))->id_producto;

    $query = "DELETE FROM producto WHERE id_producto = $id_producto";
    
    if (mysqli_query($mysqli, $query)) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto: ' . mysqli_error($mysqli)]);
    }
}
?>
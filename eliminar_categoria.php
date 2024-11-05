<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_categoria'];

    // Verificar si la categoría está en uso (opcional)
    $checkQuery = "SELECT COUNT(*) as count FROM productos WHERE id_categoria = $id"; // Ajusta según tu lógica
    $checkResult = mysqli_query($mysqli, $checkQuery);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar la categoría, está en uso.']);
        exit;
    }

    $deleteQuery = "DELETE FROM categoria WHERE id_categoria = $id";
    $deleteResult = mysqli_query($mysqli, $deleteQuery);

    if ($deleteResult) {
        echo json_encode(['success' => true, 'message' => 'Categoría eliminada exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la categoría: ' . mysqli_error($mysqli)]);
    }
    exit;
}
<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_categoria'];
    $descripcion = $_POST['descripcion'];

    $updateQuery = "UPDATE categoria SET descripcion = '$descripcion' WHERE id_categoria = $id";
    $updateResult = mysqli_query($mysqli, $updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => true, 'message' => 'Categoría actualizada exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la categoría: ' . mysqli_error($mysqli)]);
    }
    exit;
}
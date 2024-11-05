<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = $_POST['descripcion'];

    $insertQuery = "INSERT INTO categoria (descripcion) VALUES ('$descripcion')";
    $insertResult = mysqli_query($mysqli, $insertQuery);

    if ($insertResult) {
        echo json_encode(['success' => true, 'message' => 'Categoría agregada exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar la categoría: ' . mysqli_error($mysqli)]);
    }
    exit;
}
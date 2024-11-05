<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autorizado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = mysqli_real_escape_string($mysqli, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($mysqli, $_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $id_artesano = (int)$_POST['id_artesano'];
    $id_categoria = (int)$_POST['id_categoria'];

    $query = "INSERT INTO producto (nombre, descripcion, precio, stock, id_artesano, id_categoria) 
              VALUES ('$nombre', '$descripcion', $precio, $stock, $id_artesano, $id_categoria)";
    
    if (mysqli_query($mysqli, $query)) {
        echo json_encode(['success' => true, 'message' => 'Producto agregado exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar el producto: ' . mysqli_error($mysqli)]);
    }
}
?>
<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    echo json_encode(['success' => false, 'message' => 'No estás autorizado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = (int)$_POST['id_producto'];
    $nombre = mysqli_real_escape_string($mysqli, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($mysqli, $_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $stock = (int)$_POST['stock'];
    $id_artesano = (int)$_POST['id_artesano'];
    $id_categoria = (int)$_POST['id_categoria'];

    $query = "UPDATE producto SET 
              nombre = '$nombre', 
              descripcion = '$descripcion', 
              precio = $precio, 
              stock = $stock, 
              id_artesano = $id_artesano, 
              id_categoria = $id_categoria 
              WHERE id_producto = $id_producto";
    
    if (mysqli_query($mysqli, $query)) {
        echo json_encode(['success' => true, 'message' => 'Producto actualizado exitosamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el producto: ' . mysqli_error($mysqli)]);
    }
}
?>
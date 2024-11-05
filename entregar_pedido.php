<?php
session_start();
require "conexion.php";

// Verifica si el usuario está logueado
if (!isset($_SESSION['idUSUARIO'])) {
    echo "Usuario no autenticado.";
    exit();
}

// Verifica si se ha recibido el ID del pedido
if (isset($_POST['id_pedido'])) {
    $id_pedido = $_POST['id_pedido'];

    // Actualiza el estado de entrega del pedido a "Entregado"
    $query = "UPDATE pedidos SET estado_entrega = 'Entregado' WHERE id_pedido = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_pedido);

    if ($stmt->execute()) {
        echo "Pedido marcado como entregado.";
    } else {
        echo "Error al marcar el pedido como entregado.";
    }

    $stmt->close();
} else {
    echo "ID de pedido no proporcionado.";
}

$mysqli->close();
?>

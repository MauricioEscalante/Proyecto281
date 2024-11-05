<?php
session_start();
require "conexion.php";


if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
}

$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];
$id_usuario = $_SESSION['idUSUARIO'];

// Cambiar la consulta para obtener solo carritos abiertos
$query = "SELECT p.nombre, p.precio, c.cantidad, (p.precio * c.cantidad) AS total
          FROM carrito c
          JOIN producto p ON c.id_producto = p.id_producto
          WHERE c.id_usuario = $id_usuario AND c.cerrado = 0"; // Solo carritos abiertos

$result = mysqli_query($mysqli, $query);
$total_acumulado = 0;

?>

<?php include("template/cabecera.php"); ?>
<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Tu Carrito de Compras</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php while ($row = mysqli_fetch_array($result)) {
            // Sumar el total de cada producto al total acumulado
            $total_acumulado += $row['total'];
             ?>
            <div class="product-card bg-white rounded-lg shadow-md overflow-hidden">
                <h3 class="font-semibold text-xl mb-2"><?php echo $row['nombre']; ?></h3>
                <p class="text-gray-600 mb-4">Cantidad: <?php echo $row['cantidad']; ?></p>
                <p class="text-xl font-bold text-indigo-600">Total: $<?php echo number_format($row['total'], 2); ?></p>
            </div>
        <?php } ?>
    </div>
    <!-- Mostrar el total acumulado -->
    <div class="mt-6 text-right">
        <h2 class="text-2xl font-bold text-gray-800">Total del Carrito: $<?php echo number_format($total_acumulado, 2); ?></h2>
    </div>



    <div class="mt-6">
        <a href="seleccion_pago.php" class="btn btn-primary">Proceder al Pago</a>
    </div>
</main>

<?php include("template/pie.php"); ?>
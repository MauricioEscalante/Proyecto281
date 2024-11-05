<?php
// seleccion_pago.php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
}

$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];
$id_usuario = $_SESSION['idUSUARIO'];

$query = "SELECT p.nombre, p.precio, c.cantidad, (p.precio * c.cantidad) AS total 
          FROM carrito c 
          JOIN producto p ON c.id_producto = p.id_producto 
          WHERE c.id_usuario = $id_usuario AND c.cerrado = 0";

$result = mysqli_query($mysqli, $query);
$total_acumulado = 0;
?>

<?php include("template/cabecera.php"); ?>
<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Selecciona tu método de pago</h1>
    
    <!-- Resumen del carrito -->
    <div class="mb-8 text-right">
        <?php 
        while ($row = mysqli_fetch_array($result)) {
            $total_acumulado += $row['total'];
        }
        ?>
        <h2 class="text-2xl font-bold text-gray-800">Total a Pagar: $<?php echo number_format($total_acumulado, 2); ?></h2>
    </div>

    <!-- Opciones de pago -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
        <!-- Tarjeta -->
        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
            <img src="assets/tarjeta.png" alt="Tarjeta" class="w-24 h-24 mx-auto mb-4">
            <h3 class="text-xl font-bold mb-4">Tarjeta de Crédito/Débito</h3>
            <p class="text-gray-600 mb-6">Paga de manera segura con tu tarjeta</p>
            <a href="pago_tarjeta.php?total=<?php echo $total_acumulado; ?>" 
               class="bg-indigo-600 text-success py-2 px-6 rounded-lg hover:bg-indigo-700 transition-colors">
                Pagar con Tarjeta
            </a>
        </div>

        <!-- QR -->
        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
            <img src="assets/QR.png" alt="QR" class="w-24 h-24 mx-auto mb-4">
            <h3 class="text-xl font-bold mb-4">Pago con QR</h3>
            <p class="text-gray-600 mb-6">Escanea y paga desde tu celular</p>
            <a href="pago_qr.php?total=<?php echo $total_acumulado; ?>" 
               class="bg-indigo-600 text-success py-2 px-6 rounded-lg hover:bg-indigo-700 transition-colors">
                Pagar con QR
            </a>
        </div>

        <!-- Efectivo -->
        <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
            <img src="assets/efectivo.png" alt="Efectivo" class="w-24 h-24 mx-auto mb-4">
            <h3 class="text-xl font-bold mb-4">Pago en Efectivo</h3>
            <p class="text-gray-600 mb-6">Genera un código y paga en tiendas</p>
            <a href="pago_efectivo.php?total=<?php echo $total_acumulado; ?>" 
               class="bg-indigo-600 text-success py-2 px-6 rounded-lg hover:bg-indigo-700 transition-colors">
                Pagar en Efectivo
            </a>
        </div>
    </div>
</main>
<script>
function showPaymentDetails(method) {
    // Ocultar todos los detalles
    document.getElementById('cardDetails').classList.add('hidden');
    document.getElementById('qrDetails').classList.add('hidden');
    document.getElementById('cashDetails').classList.add('hidden');

    // Mostrar el detalle seleccionado
    switch(method) {
        case 'tarjeta':
            document.getElementById('cardDetails').classList.remove('hidden');
            break;
        case 'qr':
            document.getElementById('qrDetails').classList.remove('hidden');
            break;
        case 'efectivo':
            document.getElementById('cashDetails').classList.remove('hidden');
            break;
    }
}
</script>
<?php include("template/pie.php"); ?>
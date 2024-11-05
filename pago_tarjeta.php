<?php
// pago_tarjeta.php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
}
$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];
$id_usuario = $_SESSION['idUSUARIO'];

$total = $_GET['total'] ?? 0;
?>

<?php include("template/cabecera.php"); ?>
<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Pago con Tarjeta</h1>

    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-3">
        <form action="pago_exitoso.php" method="POST" class="space-y-6">
            <input type="hidden" name="payment_method" value="tarjeta">
            <input type="hidden" name="total" value="<?php echo $total; ?>">

            <div>
                <label class="block text-gray-700 mb-2 mt-4">Número de Tarjeta</label>
                <input type="text" name="card_number" 
                       class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" 
                       placeholder="Ingrese No. de Tarjeta" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-2 mt-4">Fecha de Vencimiento</label>
                    <input type="date" name="expiry_date" 
                           class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" 
                           placeholder="MM/YY" required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2 mt-4">CVV</label>
                    <input type="text" name="cvv" 
                           class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" 
                           placeholder="Ingrese el codigo de la tarjeta" required>
                </div>
            </div>

            <div>
                <label class="block text-gray-700 mb-2 mt-4">Nombre en la Tarjeta</label>
                <input type="text" name="card_name" 
                       class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" 
                       placeholder="Ingrese su nombre" required>
            </div>

            <div class="text-right text-xl font-bold text-gray-800 mb-6">
                Total a Pagar: $<?php echo number_format($total, 2); ?>
            </div>

            <button type="submit" 
                    class="w-full bg-success text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                Realizar Pago
            </button>

            <a href="seleccion_pago.php" 
               class="block text-center mt-4 text-indigo-600 hover:text-indigo-800">
                Volver a métodos de pago
            </a>
        </form>
    </div>
</main>
<?php include("template/pie.php"); ?>
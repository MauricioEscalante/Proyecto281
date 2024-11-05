<?php
// pago_qr.php
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
$codigo_qr = uniqid('QR_');
?>

<?php include("template/cabecera.php"); ?>
<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Pago con QR</h1>
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8 text-center">
        <div class="mb-8">
            <img src="generate_qr.php?data=<?php echo urlencode($codigo_qr . '|' . $total); ?>" 
                 alt="Código QR" class="mx-auto">
        </div>

        <div class="space-y-4 mb-8">
            <p class="text-xl font-bold text-gray-800">
                Total a Pagar: $<?php echo number_format($total, 2); ?>
            </p>
            <p class="text-gray-600">
                Escanea este código QR con la aplicación de tu banco o billetera virtual
            </p>
        </div>

        <form action="pago_exitoso.php" method="POST">
            <input type="hidden" name="payment_method" value="qr">
            <input type="hidden" name="total" value="<?php echo $total; ?>">
            <input type="hidden" name="codigo_qr" value="<?php echo $codigo_qr; ?>">

            <button type="submit" 
                    class="w-full bg-success text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors mb-4">
                Confirmar Pago
            </button>
        </form>

        <a href="seleccion_pago.php" 
           class="block text-center text-indigo-600 hover:text-indigo-800">
            Volver a métodos de pago
        </a>
    </div>
</main>
<?php include("template/pie.php"); ?>
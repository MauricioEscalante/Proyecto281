<?php
// pago_efectivo.php
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
$codigo_pago = strtoupper(substr(md5(uniqid()), 0, 8));
?>

<?php include("template/cabecera.php"); ?>
<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Pago en Efectivo</h1>

    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-8 text-center">
        <div class="mb-8 space-y-4">
            <h2 class="text-2xl font-bold text-gray-800">Tu código de pago:</h2>
            <div class="bg-gray-100 p-6 rounded-lg">
                <p class="text-3xl font-mono font-bold tracking-wider mt-4"><?php echo $codigo_pago; ?></p>
            </div>
            <p class="text-xl font-bold text-gray-800">
                Total a Pagar: $<?php echo number_format($total, 2); ?>
            </p>
        </div>

        <div class="space-y-4 text-left mb-8">
            <h3 class="font-bold text-lg">Instrucciones:</h3>
            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                <li>Guarda este código de pago</li>
                <li>Visita cualquier tienda afiliada</li>
                <li>Proporciona el código al cajero</li>
                <li>Realiza el pago en efectivo</li>
            </ol>
        </div>

        <form action="pago_exitoso.php" method="POST">
            <input type="hidden" name="payment_method" value="efectivo">
            <input type="hidden" name="total" value="<?php echo $total; ?>">
            <input type="hidden" name="codigo_pago" value="<?php echo $codigo_pago; ?>">

            <button type="submit" 
                    class="w-full bg-success text-white py-3 px-4 rounded-lg hover:bg-indigo-700 transition-colors mb-4">
                Confirmar Código Generado
            </button>
        </form>

        <a href="seleccion_pago.php" 
           class="block text-center text-indigo-600 hover:text-indigo-800">
            Volver a métodos de pago
        </a>
    </div>
</main>
<?php include("template/pie.php"); ?>
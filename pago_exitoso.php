<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
    exit;
}

$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];
$id_usuario = $_SESSION['idUSUARIO'];

// Obtener todos los carritos abiertos del usuario
$query = "SELECT c.id_carrito, p.nombre, p.precio, c.cantidad, (p.precio * c.cantidad) AS total
          FROM carrito c
          JOIN producto p ON c.id_producto = p.id_producto
          WHERE c.id_usuario = $id_usuario AND c.cerrado = 0"; // Solo carritos abiertos

$result = mysqli_query($mysqli, $query);

// Verificar si hay resultados
if (!$result || mysqli_num_rows($result) == 0) {
    echo "No hay carritos para mostrar.";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = $_SESSION['idUSUARIO'];
    $monto_total = $_POST['total']; // Este valor debería ser calculado a partir de los productos seleccionados
    $fecha = date('Y-m-d H:i:s');

    // Insertar la compra en la tabla reporte
    $insertQuery = "INSERT INTO reporte (id_usuario, fecha, monto_total) VALUES ('$id_usuario', '$fecha', '$monto_total')";
    $insertResult = mysqli_query($mysqli, $insertQuery);

   
}

// Cerrar todos los carritos abiertos del usuario
?>

<?php include("template/cabecera.php"); ?>
<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Pago Exitoso</h1>

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Detalles de tu Compra</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php
        $totalGeneral = 0; // Variable para acumular el total general
        $id_carrito = null; // Variable para almacenar el ID del carrito
        while ($row = mysqli_fetch_array($result)) {
            $totalGeneral += $row['total']; // Acumular el total
            $id_carrito = $row['id_carrito']; // Guardar el ID del carrito
        ?>
            <div class="product-card bg-white rounded-lg shadow-md overflow-hidden">
                <h3 class="font-semibold text-xl mb-2"><?php echo $row['nombre']; ?></h3>
                <p class="text-gray-600 mb-4">Cantidad: <?php echo $row['cantidad']; ?></p>
                <p class="text-xl font-bold text-indigo-600">Total: $<?php echo number_format($row['total'], 2); ?></p>
            </div>
        <?php } ?>
    </div>

    <div class="mt-6">
        <h3 class="text-xl font-bold text-gray-800">Total General: $<?php echo number_format($totalGeneral, 2); ?></h3>
    </div>

    <form id="deliveryForm" method="POST" action="pedidoPag.php" class="mt-6">
        <input type="hidden" name="id_carrito" value="<?php echo $id_carrito; ?>"> <!-- Campo oculto para el ID del carrito -->
        <h3 class="text-xl font-bold text-gray-800">Selecciona una opción:</h3>

        <!-- Delivery or Store Pickup Options -->
        <label>
            <input type="radio" name="opcion" value="delivery" onclick="toggleForm(true)" required> Delivery
        </label>
        <label class="ml-6">
            <input type="radio" name="opcion" value="tienda" onclick="toggleForm(false)" required> Retiro en Tienda
        </label>

        <!-- Delivery Form (hidden initially) -->
        <div id="deliveryDetails" style="display:none; margin-top:20px;">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Detalles de Entrega:</h3>
            <label class="block mb-2">Dirección:</label>
            <input type="text" name="direccion" class="border p-2 w-full mb-4" id="direccionInput">

            <label class="block mb-2">Ciudad:</label>
            <input type="text" name="ciudad" class="border p-2 w-full mb-4" id="ciudadInput">

            <label class="block mb-2">Teléfono:</label>
            <input type="text" name="telefono" class="border p-2 w-full mb-4" id="telefonoInput">
        </div>

        <div id="pickupDetails" style="display:none; margin-top:20px;">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Detalles para Retiro:</h3>
            <p>Por favor, presenta tu identificación al recoger tu pedido.</p>
        </div>

        <div class="mt-6">
            <input type="submit" value="Confirmar Pedido" class="btn btn-primary">
        </div>
    </form>
</main>

<script>
    function toggleForm(isDelivery) {
        var deliveryDetails = document.getElementById('deliveryDetails');
        var pickupDetails = document.getElementById('pickupDetails');
        var direccionInput = document.getElementById('direccionInput');
        var ciudadInput = document.getElementById('ciudadInput');
        var telefonoInput = document.getElementById('telefonoInput');

        if (isDelivery) {
            // Mostrar los detalles del delivery
            deliveryDetails.style.display = 'block';
            pickupDetails.style.display = 'none';

            // Hacer que los campos sean obligatorios
            direccionInput.required = true;
            ciudadInput.required = true;
            telefonoInput.required = true;
        } else {
            // Ocultar los detalles del delivery
            deliveryDetails.style.display = 'none';
            pickupDetails.style.display = 'block';

            // Quitar la obligatoriedad de los campos
            direccionInput.required = false;
            ciudadInput.required = false;
            telefonoInput.required = false;
        }
    }
</script>
<?php include("template/pie.php"); ?>
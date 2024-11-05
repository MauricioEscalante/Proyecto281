<?php
session_start();
require "conexion.php";

// Verificar que el usuario sea un repartidor
if (!isset($_SESSION['idUSUARIO']) || $_SESSION['nivel'] != 'Delivery') {
    header("location: index.php");
    exit;
}
$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];
$id_delivery = $_SESSION['idUSUARIO'];

// Obtener pedidos pendientes de entrega
$query = "SELECT id_pedido, nombre_cliente, direccion, estado 
          FROM pedidos
          WHERE estado = 'Pendiente'";

$result = mysqli_query($mysqli, $query);

if (!$result) {
    die('Error en la consulta: ' . mysqli_error($mysqli));
}

?>

<?php include("template/cabecera.php"); ?>
<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Pedidos Pendientes</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php while ($row = mysqli_fetch_array($result)) { ?>
            <div class="delivery-card bg-white rounded-lg shadow-md overflow-hidden">
                <h3 class="font-semibold text-xl mb-2">Pedido #<?php echo $row['id_pedido']; ?></h3>
                <p class="text-gray-600 mb-4">Cliente: <?php echo htmlspecialchars($row['nombre_cliente']); ?></p>
                <p class="text-gray-600 mb-4">Dirección: <?php echo htmlspecialchars($row['direccion']); ?></p>
                <p class="text-gray-600 mb-4">Estado: <?php echo htmlspecialchars($row['estado']); ?></p>
                <div class="flex justify-between mt-4">
                    <button onclick="aceptarPedido(<?php echo $row['id_pedido']; ?>)" class="btn btn-primary">Aceptar</button>
                    <button onclick="rechazarPedido(<?php echo $row['id_pedido']; ?>)" class="btn btn-danger">Rechazar</button>
                </div>
            </div>
        <?php } ?>
    </div>
</main>

<script>
    function aceptarPedido(pedidoId) {
        fetch('procesar_pedido1.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'aceptar',
                    id_pedido: pedidoId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pedido aceptado. Estado: En camino.');
                    location.reload();
                } else {
                    alert('Error al aceptar el pedido: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function rechazarPedido(pedidoId) {
        fetch('procesar_pedido1.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'rechazar',
                    id_pedido: pedidoId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pedido rechazado.');
                    location.reload();
                } else {
                    alert('Error al rechazar el pedido: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>

<?php include("template/pie.php"); ?>

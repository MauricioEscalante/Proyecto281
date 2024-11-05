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

// Cambiar la consulta para obtener todos los pedidos pendientes
$query = "SELECT p.id_producto, p.nombre, p.precio, c.cantidad, (p.precio * c.cantidad) AS total, c.id_carrito
          FROM carrito c
          JOIN producto p ON c.id_producto = p.id_producto
          WHERE c.estado = 'pendiente' AND c.cerrado = 1 AND p.id_artesano = $id_usuario "; // Filtrar solo carritos pendientes

$result = mysqli_query($mysqli, $query);

// Verificar si la consulta fue exitosa y hay resultados
if (!$result) {
    die('Error en la consulta: ' . mysqli_error($mysqli));
}

?>

<?php include("template/cabecera.php"); ?>
<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Listado de Pedidos</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php while ($row = mysqli_fetch_array($result)) { ?>
            <div class="product-card bg-white rounded-lg shadow-md overflow-hidden">
                <h3 class="font-semibold text-xl mb-2"><?php echo htmlspecialchars($row['nombre']); ?></h3>
                <p class="text-gray-600 mb-4">Cantidad: <?php echo htmlspecialchars($row['cantidad']); ?></p>
                <p class="text-xl font-bold text-indigo-600">Total: $<?php echo number_format($row['total'], 2); ?></p>
                <div class="flex justify-between mt-4">
                    <button onclick="aceptarPedido(<?php echo $row['id_carrito']; ?>, <?php echo $row['id_producto']; ?>, <?php echo $row['cantidad']; ?>)" class="btn btn-primary">Aceptar</button>
                    <button onclick="rechazarPedido(<?php echo $row['id_carrito']; ?>)" class="btn btn-danger">Rechazar</button>
                </div>
            </div>
        <?php } ?>
    </div>
</main>

<script>
    /*function aceptarPedido(carritoId, productoId, cantidad) {
        fetch('procesar_pedido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'aceptar',
                    id_carrito: carritoId,
                    id_producto: productoId,
                    cantidad: cantidad
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Imprime la respuesta en la consola para depurar
                if (data.success) {
                    alert('Pedido aceptado y stock actualizado. El carrito se cerrará.');
                    location.reload(); // Recargar la página para ver cambios
                } else {
                    alert('Error al aceptar el pedido: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }
*/
async function aceptarPedido(carritoId, productoId, cantidad) {
        try {
            const response = await fetch('procesar_pedido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'aceptar',
                    id_carrito: carritoId,
                    id_producto: productoId,
                    cantidad: cantidad
                })
            });

            const data = await response.json();
            if (data.success) {
                alert('Pedido aceptado y stock actualizado. El carrito se cerrará.');
                location.reload();
            } else {
                alert('Error al aceptar el pedido: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }


    function rechazarPedido(carritoId) {
        fetch('procesar_pedido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'rechazar',
                    id_carrito: carritoId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pedido rechazado. El carrito se cerrará.');
                    location.reload(); // Recargar la página para ver cambios
                } else {
                    alert('Error al rechazar el pedido: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>

<?php include("template/pie.php"); ?>
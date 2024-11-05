<?php
session_start();
require "conexion.php"; // Asegúrate de que la conexión a la base de datos esté configurada correctamente

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php"); // Redirigir si el usuario no está autenticado
}

// Datos del usuario
$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];

// Consultar productos para el formulario
$productosQuery = "SELECT id_producto, nombre FROM producto";
$productosResult = mysqli_query($mysqli, $productosQuery);

// Consultar reseñas
$reseñasQuery = "SELECT r.id_reseña, r.comentario, r.calificacion, r.fecha, p.nombre AS producto, u.nombre AS usuario FROM reseña r JOIN producto p ON r.id_producto = p.id_producto JOIN usuario u ON r.id_usuario = u.id_usuario  ORDER BY r.fecha DESC";

$reseñasResult = mysqli_query($mysqli, $reseñasQuery);
?>

<?php include("template/cabecera.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas y Comentarios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        async function agregarReseña(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('reseñaForm'));

            const response = await fetch('agregar_reseña.php', { // Asegúrate de que la ruta sea correcta
                method: 'POST',
                body: formData,
            });

            const result = await response.json();
            alert(result.message);

            if (result.success) {
                 // Opcional: limpiar el formulario o actualizar la lista de reseñas
                document.getElementById('reseñaForm').reset();
                location.reload();
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Reseñas y Comentarios</h1>

        <!-- Formulario de reseña -->
        <h3 class="text-2xl font-semibold mb-4">Deja tu reseña:</h3>
        <form id="reseñaForm" onsubmit="return agregarReseña(event)">
            <div class="mb-4">
                <label for="producto" class="block text-gray-700">Producto:</label>
                <select id="producto" name="producto" class="border rounded py-2 px-3 w-full" required>
                <?php while ($producto = mysqli_fetch_assoc($productosResult)) { ?>
                        <option value="<?php echo $producto['id_producto']; ?>">
                            <?php echo htmlspecialchars($producto['nombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="comentario" class="block text-gray-700">Comentario:</label>
                <textarea id="comentario" name="comentario" class="border rounded py-2 px-3 w-full" required></textarea>
            </div>
            <div class="mb-4">
                <label for="calificacion" class="block text-gray-700">Calificación:</label>
                <select id="calificacion" name="calificacion" class="border rounded py-2 px-3 w-full" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white rounded py-2 px-4">Enviar Reseña</button>
        </form>

        <!-- Tabla de reseñas -->
        <table class="w-full bg-white rounded-lg shadow-md mt-12">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-4 text-left">ID Reseña</th>
                    <th class="py-3 px-4 text-left">Producto</th>
                    <th class="py-3 px-4 text-left">Usuario</th>
                    <th class="py-3 px-4 text-left">Comentario</th>
                    <th class="py-3 px-4 text-left">Calificación</th>
                    <th class="py-3 px-4 text-left">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($reseña = mysqli_fetch_assoc($reseñasResult)) { ?>
                    <tr>
                        <td class="py-3 px-4"><?php echo $reseña['id_reseña']; ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($reseña['producto']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($reseña['usuario']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($reseña['comentario']); ?></td>
                        <td class="py-3 px-4"><?php echo $reseña['calificacion']; ?></td>
                        <td class="py-3 px-4"><?php echo $reseña['fecha']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>
    
    <?php include("template/pie.php"); ?>
</body>
</html>
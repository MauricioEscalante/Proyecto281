<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
}
$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];
// Obtener categorías
$categoriaQuery = "SELECT id_categoria, descripcion FROM categoria";
$categoriaResult = mysqli_query($mysqli, $categoriaQuery);

// Obtener artesanos
$artesanoQuery = "SELECT id_usuario, nombre FROM usuario WHERE tipo_idtipo = (SELECT id_rol FROM rol WHERE id_rol = '2')"; // Asegúrate de que la tabla y columna sean correctas
$artesanoResult = mysqli_query($mysqli, $artesanoQuery);

$query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.Stock, p.id_artesano, p.id_categoria 
          FROM producto p";
$result = mysqli_query($mysqli, $query);
if (!$result) {
    die("Error en la consulta: " . mysqli_error($mysqli));
}?>
<?php include("template/cabecera.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .hidden { display: none; }
    </style>
</head>
<body class="bg-gray-100">

<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Gestión de Productos</h1>

    <div class="mb-5">
        <button onclick="openModal('add')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
            Agregar Producto
        </button>
    </div>

    <div id="productList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) { ?>
                <div class="product-card bg-white rounded-lg shadow-md p-4">
                    <h3 class="font-semibold text-xl mb-2"><?php echo htmlspecialchars($row['nombre']); ?></h3>
                    <p>Descripción: <?php echo htmlspecialchars($row['descripcion']); ?></p>
                    <p>Precio: <?php echo htmlspecialchars($row['precio']); ?></p>
                    <p>Stock: <?php echo htmlspecialchars($row['Stock']); ?></p>
                    <p>Artesano: <?php echo htmlspecialchars($row['id_artesano']); ?></p>
                    <p>Categoría: <?php echo htmlspecialchars($row['id_categoria']); ?></p>
                    <div class="mt-4 space-x-2">
                        <button onclick='openModal("edit", <?php echo json_encode($row); ?>)' class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md">Editar</button>
                        <button onclick="deleteProduct(<?php echo $row['id_producto']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">Eliminar</button>
                    </div>
                </div>
            <?php }
        } else { ?>
            <p>No hay productos registrados.</p>
        <?php } ?>
    </div>
</main>

<div id="productModal" class="modal hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="modal-content bg-white rounded-lg p-6 w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-4" id="modalTitle">Agregar Producto</h2>

        <form id="productForm" onsubmit="return saveProduct(event)" class="space-y-4">
            <input type="hidden" id="productId" name="id_producto" value="">

            <div>
                <label for="artesanoId" class="block text-sm font-medium text-gray-700">Artesano</label>
                <select id="artesanoId" name="id_artesano" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                    <?php while ($artesano = mysqli_fetch_array($artesanoResult)) { ?>
                        <option value="<?php echo $artesano['id_artesano']; ?>">
                            <?php echo htmlspecialchars($artesano['nombre']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label for="categoriaId" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select id="categoriaId" name="id_categoria" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                    <?php while ($categoria = mysqli_fetch_array($categoriaResult)) { ?>
                        <option value="<?php echo $categoria['id_categoria']; ?>">
                            <?php echo htmlspecialchars($categoria['descripcion']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="mt-1 block w-full border rounded-md px-3 py-2" required>
            </div>
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="mt-1 block w-full border rounded-md px-3 py-2" required></textarea>
            </div>
            <div>
                <label for="precio" class="block text-sm font-medium text-gray-700">Precio</label>
                <input type="number" id="precio" name="precio" class="mt-1 block w-full border rounded-md px-3 py-2" step="0.01" required>
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
                <input type="number" id="stock" name="stock" class="mt-1 block w-full border rounded-md px-3 py-2" required>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">Cancelar</button>
                <button type="submit" id="saveButton" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(action, product = {}) {
        const modal = document.getElementById('productModal');
        const modalTitle = document.getElementById('modalTitle');
        const productId = document.getElementById('productId');
        const artesanoId = document.getElementById('artesanoId');
        const categoriaId = document.getElementById('categoriaId');
        const nombre = document.getElementById('nombre');
        const descripcion = document.getElementById('descripcion');
        const precio = document.getElementById('precio');
        const stock = document.getElementById('stock');

        if (action === 'add') {
            modalTitle.innerText = 'Agregar Producto';
            productId.value = '';
            artesanoId.value = '<?php echo $_SESSION['idUSUARIO']; ?>'; // ID del artesano por defecto
            categoriaId.value = '1'; // Cambia esto según sea necesario
            nombre.value = '';
            descripcion.value = '';
            precio.value = '';
            stock.value = '';
        } else if (action === 'edit') {
            modalTitle.innerText = 'Editar Producto';
            productId.value = product.id_producto;
            artesanoId.value = product.id_artesano; // ID del artesano
            categoriaId.value = product.id_categoria; // ID de la categoría
            nombre.value = product.nombre;
            descripcion.value = product.descripcion;
            precio.value = product.precio;
            stock.value = product.stock;
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        const modal = document.getElementById('productModal');
        modal.classList.add('hidden');
    }

    async function saveProduct(event) {
        event.preventDefault();
        const productId = document.getElementById('productId').value;
        const formData = new FormData(document.getElementById('productForm'));

        const url = productId ? 'actualizar_producto.php' : 'agregar_producto.php';

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
        });

        const result = await response.json();
        
        alert(result.message);

        if (result.success) {
            location.reload(); // Recargar la página para ver los cambios
        }
    }

    async function deleteProduct(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
            const response = await fetch('eliminar_producto.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_producto: id }),
            });

            const result = await response.json();
            
            alert(result.message);

            if (result.success) {
                location.reload(); // Recargar la página para ver los cambios
            }
        }
    }
</script>
<?php include("template/pie.php"); ?>
</body>
</html>
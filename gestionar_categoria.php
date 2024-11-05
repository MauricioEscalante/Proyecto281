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

// Gestión de categorías
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $categoriaId = $_POST['id_categoria'] ?? null;
    $categoriaDescripcion = $_POST['descripcion'] ?? '';

    if ($action === 'add') {
        $insertQuery = "INSERT INTO categoria (descripcion) VALUES ('$categoriaDescripcion')";
        $insertResult = mysqli_query($mysqli, $insertQuery);
        if ($insertResult) {
            echo json_encode(['success' => true, 'message' => 'Categoría agregada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al agregar la categoría: ' . mysqli_error($mysqli)]);
        }
    } elseif ($action === 'edit') {
        $updateQuery = "UPDATE categoria SET descripcion = '$categoriaDescripcion' WHERE id_categoria = $categoriaId";
        $updateResult = mysqli_query($mysqli, $updateQuery);
        if ($updateResult) {
            echo json_encode(['success' => true, 'message' => 'Categoría actualizada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la categoría: ' . mysqli_error($mysqli)]);
        }
    } elseif ($action === 'delete') {
        $deleteQuery = "DELETE FROM categoria WHERE id_categoria = $categoriaId";
        $deleteResult = mysqli_query($mysqli, $deleteQuery);
        if ($deleteResult) {
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la categoría: ' . mysqli_error($mysqli)]);
        }
    }
    exit;
}
?>
<?php include("template/cabecera.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .hidden { display: none; }
    </style>
</head>
<body class="bg-gray-100">
    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Gestión de Categorías</h1>

        <div class="mb-5">
            <button onclick="openModal('add')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
                Agregar Categoría
            </button>
        </div>

        <table class="w-full bg-white rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-4 text-left">ID</th>
                    <th class="py-3 px-4 text-left">Descripción</th>
                    <th class="py-3 px-4 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($categoria = mysqli_fetch_assoc($categoriaResult)) { ?>
                    <tr>
                        <td class="py-3 px-4"><?php echo $categoria['id_categoria']; ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($categoria['descripcion']); ?></td>
                        <td class="py-3 px-4 space-x-2">
                            <button onclick='openModal("edit", <?php echo json_encode($categoria); ?>)' class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md">Editar</button>
                            <button onclick="deleteCategoria(<?php echo $categoria['id_categoria']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">Eliminar</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </main>

    <div id="categoriaModal" class="modal hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white rounded-lg p-6 w-full max-w-2xl">
            <h2 class="text-2xl font-bold mb-4" id="modalTitle">Agregar Categoría</h2>

            <form id="categoriaForm" onsubmit="return saveCategoria(event)" class="space-y-4">
                <input type="hidden" id="categoriaId" name="id_categoria" value="">

                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <input type="text" id="descripcion" name="descripcion" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">Cancelar</button>
                    <button type="submit" id="saveButton" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(action, categoria = {}) {
            const modal = document.getElementById('categoriaModal');
            const modalTitle = document.getElementById('modalTitle');
            const categoriaId = document.getElementById('categoriaId');
            const descripcion = document.getElementById('descripcion');

            if (action === 'add') {
                modalTitle.innerText = 'Agregar Categoría';
                categoriaId.value = '';
                descripcion.value = '';
            } else if (action === 'edit') {
                modalTitle.innerText = 'Editar Categoría';
                categoriaId.value = categoria.id_categoria;
                descripcion.value = categoria.descripcion;
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('categoriaModal');
            modal.classList.add('hidden');
        }

        async function saveCategoria(event) {
            event.preventDefault();
            const categoriaId = document.getElementById('categoriaId').value;
            const formData = new FormData(document.getElementById('categoriaForm'));
            formData.append('action', categoriaId ? 'edit' : 'add');

            const response = await fetch('gestionar_categoria.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();
            alert(result.message);

            if (result.success) {
                location.reload();
            }
        }

        async function deleteCategoria(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        const response = await fetch('eliminar_categoria.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_categoria: id }),
        });

        const result = await response.json();
        alert(result.message);

        if (result.success) {
            location.reload(); // Recargar la página para reflejar los cambios
        }
    }
}
    </script>
    <?php include("template/pie.php"); ?>
</body>
</html>
<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
}

$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];

$query = "SELECT id_usuario, nombre, email, telefono, tipo_idtipo FROM usuario";  
$result = mysqli_query($mysqli, $query);
if (!$result) {
    die("Error en la consulta: " . mysqli_error($mysqli));
}
?>
<?php include("template/cabecera.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .hidden { display: none; }
    </style>
</head>
<body class="bg-gray-100">

<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Gestión de Usuarios</h1>

    <div class="mb-5">
        <button onclick="openModal('add')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">
            Agregar Usuario
        </button>
    </div>

    <!-- Lista de usuarios -->
    <div id="userList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result)) { ?>
                <div class="user-card bg-white rounded-lg shadow-md p-4">
                    <h3 class="font-semibold text-xl mb-2"><?php echo htmlspecialchars($row['nombre']); ?></h3>
                    <p>Email: <?php echo htmlspecialchars($row['email']); ?></p>
                    <p>Teléfono: <?php echo htmlspecialchars($row['telefono']); ?></p>
                    <p>Tipo: <?php echo htmlspecialchars($row['tipo_idtipo']); ?></p>
                    <div class="mt-4 space-x-2">
                        <button onclick='openModal("edit", <?php echo json_encode($row); ?>)' class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md">Editar</button>
                        <button onclick="deleteUser(<?php echo $row['id_usuario']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md">Eliminar</button>
                    </div>
                </div>
            <?php }
        } else { ?>
            <p>No hay usuarios registrados.</p>
        <?php } ?>
    </div>
</main>

<!-- Modal para agregar/editar usuario -->
<div id="userModal" class="modal hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="modal-content bg-white rounded-lg p-6 w-full max-w-2xl">
        <h2 class="text-2xl font-bold mb-4" id="modalTitle">Agregar Usuario</h2>
        
        <form id="userForm" onsubmit="return saveUser(event)" class="space-y-4">
            <input type="hidden" id="userId" name="id_usuario" value="">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="mt-1 block w-full border rounded-md px-3 py-2" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="mt-1 block w-full border rounded-md px-3 py-2" required>
            </div>
            <div>
                <label for="contraseña" class="block text-sm font-medium text-gray-700">Contraseña</label>
                <div class="relative">
                <input type="password" id="contraseña" name="contraseña" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                <button type="button" onclick="togglePasswordVisibility()" class="absolute right-2 top-2 text-gray-500">
                <span id="togglePasswordText">Mostrar</span>
            </button>
            </div>
            </div>
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="mt-1 block w-full border rounded-md px-3 py-2" required>
            </div>
            <div>
                <label for="tipo_idtipo" class="block text-sm font-medium text-gray-700">Tipo de Usuario</label>
                <select id="tipo_idtipo" name="tipo_idtipo" class="mt-1 block w-full border rounded-md px-3 py-2" required>
                    <option value="1">Administrador</option>
                    <option value="2">Artesano</option>
                    <option value="3">Comprador</option>
                    <option value="4">Delivery</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">Cancelar</button>
                <button type="submit" id="saveButton" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(action, user = {}) {
        const modal = document.getElementById('userModal');
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = user.id_usuario || '';
        document.getElementById('nombre').value = user.nombre || '';
        document.getElementById('email').value = user.email || '';
        document.getElementById('telefono').value = user.telefono || '';
        document.getElementById('tipo_idtipo').value = user.tipo_idtipo || '1';

        document.getElementById('modalTitle').innerText = action === 'edit' ? 'Editar Usuario' : 'Agregar Usuario';
        document.getElementById('saveButton').innerText = action === 'edit' ? 'Actualizar Usuario' : 'Guardar Usuario';
        
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function saveUser(event) {
        event.preventDefault();
        const userId = document.getElementById('userId').value;
        const userName = document.getElementById('nombre').value;
        const userEmail = document.getElementById('email').value;
        const userTelefono = document.getElementById('telefono').value;
        const userTipo = document.getElementById('tipo_idtipo').value;
        const userContraseña = document.getElementById('contraseña').value;

        const url = userId ? 'actualizar_usuario.php' : 'agregar_usuario.php';

        const data = {
            id_usuario: userId,
            nombre: userName,
            email: userEmail,
            telefono: userTelefono,
            tipo_idtipo: userTipo,
            contraseña: userContraseña
        };

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Usuario ${userId ? 'actualizado' : 'agregado'} correctamente.`);
                closeModal();
                location.reload(); // Recargar la página para mostrar los cambios
            } else {
                alert('Error: ' + (data.message || 'Ocurrió un error.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud: ' + error.message);
        });
    }

    function deleteUser(userId) {
        fetch('eliminar_usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_usuario: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Usuario eliminado correctamente.');
                location.reload(); // Recargar la página para mostrar los cambios
            } else {
                alert('Error: ' + (data.message || 'Ocurrió un error.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud: ' + error.message);
        });
    }
    function togglePasswordVisibility() {
    const passwordInput = document.getElementById('contraseña');
    const toggleText = document.getElementById('togglePasswordText');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleText.textContent = 'Ocultar';
    } else {
        passwordInput.type = 'password';
        toggleText.textContent = 'Mostrar';
    }
}
</script>
<?php include("template/pie.php"); ?>
</body>
</html>
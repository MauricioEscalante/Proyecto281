<?php 
session_start();
require "conexion.php";

// Verifica si el usuario está logueado
if (!isset($_SESSION['idUSUARIO'])) {
    header("location: index.php");
    exit();
}

$nombre = $_SESSION['nombre'];
$nivel = $_SESSION['nivel'];
$tipo_idtipo = $_SESSION['tipo_idtipo'];
$id_usuario = $_SESSION['idUSUARIO'];
$id_pedido = $_POST['id_pedido'];
include("template/cabecera.php"); 

// Consulta para verificar si el pedido ya está entregado
$query = "SELECT estado_entrega FROM pedidos WHERE id_pedido = $id_pedido";
$result = mysqli_query($mysqli, $query);
$row = mysqli_fetch_assoc($result);

if ($row && $row['estado_entrega'] === 'Entregado') {
    echo "<script>alert('El pedido ya fue entregado.'); window.location.href='pedidos.php';</script>";
    exit();
}
?>

<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Empecemos la entrega</h1>
    
    <div id="controls" class="text-center mt-6">
        <button onclick="startDelivery()" class="btn btn-primary">Iniciar Entrega</button>
        <button onclick="endDelivery()" class="btn btn-secondary" style="display:none;">Terminar Entrega</button>
    </div>
    
    <div id="map" style="height: 400px; width: 100%; display: none;" class="mt-6"></div>
</main>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    let map;
    let marker;

    function initMap() {
        // Inicializa el mapa en una ubicación predeterminada
        map = L.map('map').setView([-34.397, 150.644], 15); // Coordenadas predeterminadas

        // Capa de mapa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);
    }

    function startDelivery() {
        // Muestra el mapa y obtiene la ubicación del usuario
        document.getElementById("map").style.display = "block";
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };

                // Centra el mapa en la ubicación del usuario
                map.setView(pos, 15);

                // Coloca un marcador en la ubicación
                if (marker) {
                    map.removeLayer(marker); // Eliminar marcador anterior si existe
                }
                marker = L.marker(pos).addTo(map).bindPopup("Tu ubicación").openPopup();

                // Mostrar el botón de terminar entrega
                document.querySelectorAll("#controls button")[1].style.display = "inline-block"; // Mostrar botón "Terminar Entrega"
            }, () => {
                handleLocationError(true);
            });
        } else {
            // El navegador no soporta Geolocalización
            handleLocationError(false);
        }
    }

    function endDelivery() {
        // Oculta el mapa y elimina el marcador
        document.getElementById("map").style.display = "none";
        if (marker) {
            map.removeLayer(marker);
        }

        // Ocultar el botón de "Terminar Entrega"
        document.querySelectorAll("#controls button")[1].style.display = "none";

        // Llama al script PHP para marcar el pedido como entregado
        fetch("entregar_pedido.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `id_pedido=<?php echo $id_pedido; ?>`
        }).then(response => {
            if (response.ok) {
                alert("El pedido ha sido marcado como entregado.");
                window.location.href = "pedidosf.php";
            } else {
                alert("Hubo un problema al marcar el pedido como entregado.");
            }
        });
    }

    function handleLocationError(browserHasGeolocation) {
        alert(browserHasGeolocation
            ? "Error: El servicio de geolocalización falló."
            : "Error: Tu navegador no soporta geolocalización.");
    }

    // Inicializa el mapa al cargar
    document.addEventListener("DOMContentLoaded", initMap);
</script>

<?php include("template/pie.php"); ?>

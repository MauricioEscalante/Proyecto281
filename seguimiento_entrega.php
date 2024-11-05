<?php
session_start();
require "conexion.php";

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
?>

<main class="container mx-auto px-6 py-24">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Seguimiento de Entrega</h1>

    <div id="map" style="height: 400px; width: 100%;" class="mt-6"></div>

    <!-- Botón de Aceptar Entrega -->
    <form action="pedidosf1.php" method="post" class="text-center mt-6">
        <input type="hidden" name="id_pedido" value="<?php echo $id_pedido; ?>">
        <button type="submit" class="btn btn-primary">Aceptar </button>
    </form>
</main>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    function initMap() {
        // Inicializa el mapa en una ubicación predeterminada
        const map = L.map('map').setView([-34.397, 150.644], 15); // Coordenadas predeterminadas

        // Capa de mapa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Geolocalización
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };

                // Centra el mapa en la ubicación del usuario
                map.setView(pos, 15);

                // Coloca un marcador en la ubicación
                L.marker(pos).addTo(map).bindPopup("Tu ubicación actual").openPopup();
            }, () => {
                alert("No se pudo obtener la ubicación.");
            });
        } else {
            alert("Tu navegador no soporta geolocalización.");
        }
    }

    // Inicializa el mapa al cargar
    document.addEventListener("DOMContentLoaded", initMap);
</script>

<?php include("template/pie.php"); ?>

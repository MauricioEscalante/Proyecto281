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

// Obtener los parámetros de filtro por fecha
$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : null;
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : null;

// Consultar ventas uniendo las tablas
$ventasQuery = "
    SELECT r.id_reporte, r.fecha, r.monto_total, u.nombre AS nombre_usuario
    FROM reporte r
    JOIN usuario u ON r.id_usuario = u.id_usuario";

if ($fechaInicio && $fechaFin) {
    $ventasQuery .= " WHERE r.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
}
$ventasQuery .= " ORDER BY r.fecha DESC";

$ventasResult = mysqli_query($mysqli, $ventasQuery);
if (!$ventasResult) {
    die("Error en la consulta: " . mysqli_error($mysqli)); // Manejo de errores
}

// Calcular totales
$totalVentas = 0;
$totalMonto = 0;
$fechas = [];
$montos = [];

while ($venta = mysqli_fetch_assoc($ventasResult)) {
    $totalVentas++;
    $totalMonto += $venta['monto_total'];

    // Preparar datos para el gráfico
    $fechas[] = $venta['fecha'];
    $montos[] = $venta['monto_total'];
}

// Resetear el puntero del resultado de la consulta
mysqli_data_seek($ventasResult, 0);
?>

<?php include("template/cabecera.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <main class="container mx-auto px-6 py-24">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-12">Reporte de Ventas</h1>

        <!-- Formulario de filtro por fecha -->
        <div class="mb-8">
            <form id="filtroFecha" onsubmit="return filtrarVentas(event)">
                <div class="mb-4 flex items-center">
                    <label for="fechaInicio" class="mr-4 text-gray-700">Fecha Inicio:</label>
                    <input type="date" id="fechaInicio" name="fechaInicio" class="border rounded py-2 px-3 w-full" value="<?php echo $fechaInicio; ?>">
                </div>
                <div class="mb-4 flex items-center">
                    <label for="fechaFin" class="mr-4 text-gray-700">Fecha Fin:</label>
                    <input type="date" id="fechaFin" name="fechaFin" class="border rounded py-2 px-3 w-full" value="<?php echo $fechaFin; ?>">
                </div>
                <button type="submit" class="bg-blue-500 text-white rounded py-2 px-4">Filtrar</button>
            </form>
        </div>

        <table class="w-full bg-white rounded-lg shadow-md mb-8">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-4 text-left">ID Reporte</th>
                    <th class="py-3 px-4 text-left">Fecha</th>
                    <th class="py-3 px-4 text-left">Usuario</th>
                    <th class="py-3 px-4 text-left">Monto Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($venta = mysqli_fetch_assoc($ventasResult)) { ?>
                    <tr>
                        <td class="py-3 px-4"><?php echo $venta['id_reporte']; ?></td>
                        <td class="py-3 px-4"><?php echo $venta['fecha']; ?></td>
                        <td class="py-3 px-4"><?php echo $venta['nombre_usuario']; ?></td>
                        <td class="py-3 px-4"><?php echo $venta['monto_total']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="bg-gray-200 font-bold">
                    <td class="py-3 px-4">Total</td>
                    <td class="py-3 px-4"><?php echo $totalVentas; ?></td>
                    <td class="py-3 px-4"></td>
                    <td class="py-3 px-4"><?php echo $totalMonto; ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Canvas para el gráfico -->
        <canvas id="ventasChart" class="mb-8" width="400" height="200"></canvas>
    </main>

    <?php include("template/pie.php"); ?>

    <script>
        async function filtrarVentas(event) {
            event.preventDefault();

            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaFin = document.getElementById('fechaFin').value;

            // Realizar la consulta con los filtros de fecha
            window.location.href = 'reporte.php?fechaInicio=' + fechaInicio + '&fechaFin=' + fechaFin;
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (<?php echo count($fechas); ?> > 0 && <?php echo count($montos); ?> > 0) {
                const ctx = document.getElementById('ventasChart').getContext('2d');
                const ventasChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($fechas); ?>,
                        datasets: [{
                            label: 'Monto Total por Fecha',
                            data: <?php echo json_encode($montos); ?>,
                            backgroundColor: 'rgba(5, 249, 24,0.8)',
                            borderColor: 'rgba(0, 0, 0, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                console.log("No se encontraron datos para mostrar el gráfico.");
            }
        });
    </script>
</body>
</html>
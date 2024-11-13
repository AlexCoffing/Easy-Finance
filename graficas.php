<?php
// Incluir el archivo de conexión y comenzar la sesión
require "conecta_chatbot.php";
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo "Error: Usuario no autenticado.";
    exit;
}

// Obtener el ID del usuario de la sesión
$user_id = $_SESSION['user_id'];

// Conectar a la base de datos usando la función conecta()
$conn = conecta();

// Consulta para obtener los balances del usuario
$sql = "SELECT balance_ingreso, balance_egreso, balance_total FROM datos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Asignar los datos obtenidos a variables
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $balance_ingreso = $row['balance_ingreso'];
    $balance_egreso = $row['balance_egreso'];
    $balance_total = $row['balance_total'];
} else {
    // Si no hay registro, asignar valores cero
    $balance_ingreso = 0;
    $balance_egreso = 0;
    $balance_total = 0;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficas de Balances</title>
    <link rel="stylesheet" href="styles.css"> <!-- Incluyendo el archivo CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Cargar Chart.js desde CDN -->
    <style>
        /* Ajustar el diseño para mostrar los gráficos en fila horizontal */
        .graficas-container {
            display: flex;
            justify-content: space-between; /* Espacio entre las gráficas */
            gap: 20px; /* Espacio entre los elementos */
            flex-wrap: wrap; /* Permite que los gráficos se acomoden en varias líneas si el espacio no es suficiente */
            width: 100%;
            box-sizing: border-box;

            
        }

        .grafica {
            flex: 1; /* Cada gráfico ocupa un espacio igual */
            min-width: 200px; /* Ancho mínimo para cada gráfico */
            max-width: 300px; /* Ancho máximo para cada gráfico */
            height: 200px; /* Altura fija para los gráficos */
            

        }
    </style>
</head>
<body>
    <div class="cuadro">
        <h1>Gráficas de Balances</h1>

        <!-- Contenedor para las gráficas en fila horizontal -->
        <div class="graficas-container">
            <!-- Gráfico de barras -->
            <div class="grafica">
                <canvas id="barChart"></canvas>
            </div>
            <!-- Gráfico de pastel -->
            <div class="grafica">
                <canvas id="pieChart"></canvas>
            </div>
            <!-- Gráfico de línea -->
            <div class="grafica">
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <br><a href="principal_chatbot.php">Volver a la página principal</a>
    </div>

    <script>
        // Obtener los datos de PHP para usar en las gráficas de JavaScript
        const balanceIngreso = <?php echo $balance_ingreso; ?>;
        const balanceEgreso = <?php echo $balance_egreso; ?>;
        const balanceTotal = <?php echo $balance_total; ?>;

        // Gráfico de barras
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Ingresos', 'Egresos', 'Saldo Actual'],
                datasets: [{
                    label: 'Balance en $',
                    data: [balanceIngreso, balanceEgreso, balanceTotal],
                    backgroundColor: ['#4caf50', '#f44336', '#2196f3']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Balance de Ingresos, Egresos y Saldo Actual'
                    }
                }
            }
        });

        // Gráfico de pastel
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Ingresos', 'Egresos', 'Saldo Actual'],
                datasets: [{
                    data: [balanceIngreso, balanceEgreso, balanceTotal],
                    backgroundColor: ['#4caf50', '#f44336', '#2196f3']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribución de Balance'
                    }
                }
            }
        });

        // Gráfico de línea
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Ingresos', 'Egresos', 'Saldo Actual'],
                datasets: [{
                    label: 'Balance en $',
                    data: [balanceIngreso, balanceEgreso, balanceTotal],
                    fill: false,
                    borderColor: '#4caf50',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Evolución de Balance'
                    }
                }
            }
        });
    </script>
</body>
</html>

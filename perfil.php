<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    echo "Por favor, inicia sesión primero.";
    echo '<br><br>';
    echo '<a href="login_chatbot.php">Iniciar sesión</a>';
    exit;
}

$correo = $_SESSION['usuario'];

require "conecta_chatbot.php";
$conn = conecta();

$sql = "SELECT nombre, apellidos, correo, balance_ingreso, balance_egreso, balance_total FROM datos WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombre = $row['nombre'];
    $apellidos = $row['apellidos'];
    $correo = $row['correo'];
    $balance_ingreso = $row['balance_ingreso'];
    $balance_egreso = $row['balance_egreso'];
    $balance_total = $row['balance_total'];
} else {
    echo "No se encontró el perfil del usuario.";
    echo '<br>';
    echo '<a href="principal_chatbot.php">Regresar</a>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .cuadro {
            text-align: center;
            margin: 20px;
            background-color: rgba(255, 213, 135, 0.986);

        }
    </style>
</head>
<body>
    <div class="cuadro">
        <h1>Perfil de Usuario</h1>
        
        <!-- Tabla de datos del perfil -->
        <table>
            <tr>
                <th>Nombre</th>
                <td><?php echo $nombre; ?></td>
            </tr>
            <tr>
                <th>Apellidos</th>
                <td><?php echo $apellidos; ?></td>
            </tr>
            <tr>
                <th>Correo</th>
                <td><?php echo $correo; ?></td>
            </tr>
            <tr>
                <th>Balance de ingresos totales</th>
                <td>$<?php echo number_format($balance_ingreso, 2); ?></td>
            </tr>
            <tr>
                <th>Balance de egresos totales</th>
                <td>$<?php echo number_format($balance_egreso, 2); ?></td>
            </tr>
            <tr>
                <th>Saldo actual</th>
                <td>$<?php echo number_format($balance_total, 2); ?></td>
            </tr>
        </table>

        <br>
        <a href="principal_chatbot.php">Volver al Panel Principal</a>
    </div>
</body>
</html>

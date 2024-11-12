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

// Consulta para obtener el balance actual del usuario
$sql = "SELECT balance_total FROM datos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Mostrar el balance si existe un registro
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $balance_total = $row['balance_total'];
} else {
    // Si no hay registro, mostrar balance cero
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
    <title>Balance Actual</title>
    <link rel="stylesheet" href="styles.css"> <!-- Incluyendo el archivo CSS -->
</head>
<body>
    <div class="cuadro"> <!-- Contenedor para mostrar el balance -->
        <h1>Su saldo actual es:</h1>
        <h2><?php echo number_format($balance_total, 2); ?> </h2> <!-- Mostrar balance en formato de moneda -->
        <br>
        <a href="principal_chatbot.php">Volver a la página principal</a>
    </div>
</body>
</html>

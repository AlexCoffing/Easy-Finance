<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['nombre_usuario'])) {
    echo "Por favor, inicia sesión primero.";
    echo '<br><br>';
    echo '<a href="login_chatbot.php">Iniciar sesión</a>';
    exit;
}

$usuario = $_SESSION['nombre_usuario']; // Recuperar el nombre del usuario desde la sesión

// Conexión a la base de datos
require "conecta_chatbot.php";

$conn = conecta();

// Consultar los mensajes del usuario logueado
$sql = "SELECT * FROM mensajes WHERE usuario_id = ? ORDER BY fecha_mensaje DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);  // Usar el correo o identificador del usuario logueado
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EASYFINANCE</title>
    <link rel="stylesheet" href="styles.css"> <!-- Incluyendo el archivo CSS -->
</head>

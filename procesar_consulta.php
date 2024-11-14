<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    echo "Por favor, inicia sesión primero.";
    echo '<br><br>';
    echo '<a href="login_chatbot.php">Iniciar sesión</a>';
    exit;
}

$usuario = $_SESSION['nombre_usuario']; // Recuperar el nombre del usuario desde la sesión
$user_id = $_SESSION['user_id']; // Recuperar el ID del usuario desde la sesión
$consulta = $_POST['consulta']; // Consulta del usuario

// Conexión a la base de datos
require "conecta_chatbot.php";
$conn = conecta(); // Llamada a la función para obtener la conexión

// Insertar el mensaje del usuario en la base de datos
$sql = "INSERT INTO mensajes (usuario_id, mensaje, sender) VALUES (?, ?, 'user')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_id, $consulta);
$stmt->execute();
$stmt->close();

// Realizar una solicitud HTTP POST al servidor Flask
$url = 'http://localhost:5000/get_response';
$data = array('message' => $consulta, 'user_id' => $user_id); // Añadir el user_id al array de datos

// Usar cURL para hacer la solicitud
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$response = curl_exec($ch);
curl_close($ch);

$response_data = json_decode($response, true);
$respuestaBot = $response_data['response'];

// Insertar la respuesta del bot en la base de datos
$sql = "INSERT INTO mensajes (usuario_id, mensaje, sender) VALUES (?, ?, 'bot')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_id, $respuestaBot);
$stmt->execute();
$stmt->close();

$conn->close();

// Devolver la respuesta del bot
echo $respuestaBot;
?>
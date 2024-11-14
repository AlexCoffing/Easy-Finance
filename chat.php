<?php
session_start(); // Inicia la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    echo "Por favor, inicia sesión primero.";
    echo '<br><br>';
    echo '<a href="login_chatbot.php">Iniciar sesión</a>';
    exit;
}

$usuario = $_SESSION['nombre_usuario']; // Recuperar el nombre del usuario desde la sesión
$user_id = $_SESSION['user_id']; // Recuperar el ID del usuario desde la sesión

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
    <title>Chatbot - Panel Principal</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos adicionales */
        .form-section {
            margin: 20px 0;
        }
        .cuadro {
            text-align: center;
        }
        #chat-box {
            height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-top: 20px;
            text-align: left;
            font-size: 14px;
        }
        .message {
            margin: 5px 0;
        }
        .user {
            color: blue;
            font-weight: bold;
        }
        .bot {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Cuadro central -->
    <div class="cuadro">
    <h1>Bienvenido al Chatbot EASYFINANCE, <?php echo $usuario; ?>!</h1>
    <h3>¿En qué puedo ayudarte hoy?</h3>

        <!-- Cuadro de chat -->
        <div id="chat-box">
            <?php
            // Mostrar los mensajes del usuario y del bot
            while ($row = $result->fetch_assoc()) {
                $mensaje = htmlspecialchars($row['mensaje']);
                $sender = ($row['sender'] == 'user') ? 'user' : 'bot'; // Asegúrate de que tienes un campo 'sender' en la base de datos
                echo "<div class='message'>
                        <span class='$sender'>" . ($sender == 'user' ? 'Usuario' : 'Bot') . ":</span> $mensaje
                      </div>";
            }
            ?>
        </div>

        <!-- Formulario de consulta -->
        <form id="consultaForm">
            <label for="consulta">Escribe tu consulta:</label>
            <input type="text" id="consulta" name="consulta"><br><br>
            <button type="submit">Enviar</button>
        </form>

        <br><br>
        <a href="principal_chatbot.php">Volver a la página principal</a>
    </div>

    <script src="jquery-3.3.1.min.js"></script>
    <script>
        const chatBox = document.getElementById('chat-box');

        // Manejo de envío de consulta
        $('#consultaForm').on('submit', function(e) {
            e.preventDefault();
            const consulta = $('#consulta').val().trim();

            if (consulta !== "") {
                appendMessage(consulta, 'user');

                // AJAX para enviar la consulta y recibir respuesta
                $.ajax({
                    url: 'procesar_consulta.php',
                    method: 'POST',
                    data: { consulta: consulta, user_id: '<?php echo $user_id; ?>' },
                    success: function(response) {
                        appendMessage(response, 'bot');
                    },
                    error: function() {
                        const errorMsg = "Lo siento, no pude entender tu consulta.";
                        appendMessage(errorMsg, 'bot');
                    }
                });
            }
        });

        // Añadir mensaje al cuadro de chat
        const appendMessage = (text, sender) => {
            const messageElem = document.createElement('div');
            messageElem.classList.add('message');
            messageElem.innerHTML = `<span class="${sender}">${sender === 'user' ? 'Usuario' : 'Bot'}:</span> ${text}`;
            chatBox.appendChild(messageElem);
            chatBox.scrollTop = chatBox.scrollHeight;
        };
    </script>
</body>
</html>
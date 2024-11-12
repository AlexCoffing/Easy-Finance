<?php
// Incluye el archivo de conexión y comienza la sesión
require "conecta_chatbot.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];
    $pass = md5($_POST['pass']); // Encriptar la contraseña

    // Verificar si el correo ya existe
    $conn = conecta();
    $sql_check = "SELECT * FROM datos WHERE correo = ?";
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $mensajeCorreo = "El correo ya está registrado.";
    } else {
        // Si el correo no existe, insertamos el nuevo usuario
        $sql_insert = "INSERT INTO datos (nombre, apellidos, correo, pass) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssss", $nombre, $apellidos, $correo, $pass);

        if ($stmt_insert->execute()) {
            $mensaje = "Usuario registrado exitosamente. Puedes iniciar sesión.";
        } else {
            $mensaje = "Error al registrar el usuario: " . $stmt_insert->error;
        }
    }

    // Cerrar conexión
    $stmt->close();
    $stmt_insert->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alta de usuario</title>
    <link rel="stylesheet" href="styles.css">
    <script src="jquery-3.3.1.min.js"></script>
</head>
<body>
    <div class="cuadro">
        <h2>Alta de usuarios</h2>
        
        <?php
        // Mostrar mensaje de error o éxito
        if (isset($mensajeCorreo)) {
            echo "<div style='color:red;'>$mensajeCorreo</div>";
        }
        if (isset($mensaje)) {
            echo "<div style='color:green;'>$mensaje</div>";
        }
        ?>

        <form id="empleadoForm" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required><br>
            
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required><br>
            
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required><br>
            
            <label for="pass">Contraseña:</label>
            <input type="password" id="pass" name="pass" required><br><br>
            
            <button type="submit">Agregar usuario</button>
        </form>
        <br>
        <a href="login_chatbot.php">Regresar a login</a>
    </div>

    <script>
        // Verificar si el correo ya existe al perder el foco en el campo correo
        $('#correo').on('blur', function() {
            let correo = $(this).val().trim();
            if (correo != "") {
                $.ajax({
                    url: 'verificar_correo_chatbot.php',
                    type: 'POST',
                    data: { correo: correo },
                    success: function(response) {
                        if (response == '1') {
                            $('#mensajeCorreo').html('El correo ' + correo + ' ya existe.').show();
                            $('#correo').val('');
                            setTimeout(function() {
                                $('#mensajeCorreo').hide();
                            }, 5000);
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

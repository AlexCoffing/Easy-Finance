<!-- login_chatbot.php -->
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles.css"> <!-- Vincula el archivo CSS -->
    <script src="jquery-3.3.1.min.js"></script>
</head>
<body>
    <div class="cuadro">
        <h2>Login</h2>
        <form id="loginForm">
            <label>Correo:  </label>
            <input type="email" name="correo" id="correo" required><br>
            <label>Contraseña:</label>
            <input type="password" name="pass" id="pass" required><br>
            <div id="mensaje" style="color:red;"></div><br>
            <button type="button" onclick="validarFormulario()">Iniciar Sesión</button>
        </form>
        <p><a href="chatbot_registro.php">Crear un nuevo usuario</a></p>
    </div>

    <script>
        function validarFormulario() {
            var correo = $("#correo").val();
            var pass = $("#pass").val();

            if (correo == "" || pass == "") {
                $("#mensaje").html("Faltan campos por llenar").css("color", "red");
            } else {
                $.ajax({
                    url: "verificar_usuario_chatbot.php",
                    method: "POST",
                    data: {
                        correo: correo,
                        pass: pass
                    },
                    success: function(response) {
                        if (response.trim() == "existe") {
                            window.location.href = "principal_chatbot.php";
                        } else {
                            $("#mensaje").html(response).css("color", "red");
                        }
                    },
                    error: function() {
                        $("#mensaje").html("Error al procesar la solicitud.").css("color", "red");
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php
session_start();
require "conecta_chatbot.php";
$con = conecta();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $pass = md5($_POST['pass']); // Encriptar la contraseña

    // Consulta para buscar al usuario por correo
    $sql = "SELECT * FROM datos WHERE correo = ?";
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $empleado = $res->fetch_assoc();

            if ($pass == $empleado['pass']) {
                $_SESSION['user_id'] = $empleado['id'];
                $_SESSION['usuario'] = $empleado['correo'];
                $_SESSION['nombre_usuario'] = $empleado['nombre']; // Asegurarse de asignar 'nombre_usuario'
                echo "existe";
            } else {
                echo "Contraseña incorrecta";
            }            
        } else {
            echo "Usuario no encontrado";
        }
        $stmt->close();
    } else {
        echo "Error en la consulta";
    }
} else {
    echo "Método de solicitud no válido.";
}

$con->close();
?>

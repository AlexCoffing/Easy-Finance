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
    <title>Chatbot - Panel Principal</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .header {
            background-color: #a0522d;
            color: white;
            padding: 15px;
            text-align: center;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .header p {
            margin: 0;
            font-weight: bold;
        }

        .menu-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 15px;
        }

        .menu-links a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .menu-links a:hover {
            text-decoration: underline;
        }

        .btn a button {
            background-color: #a0522d;
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn a button:hover {
            background-color: #8b4513;
        }

        /* Estilos de bienvenida */
        .bienvenida {
            text-align: center;
            padding: 50px;
            background-color: rgba(255, 213, 135, 0.986);
            width: 400px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 70px; /* Espacio debajo de la barra */
        }

        .bienvenida h1 {
            font-size: 28px;
            color: #333;
        }

        .bienvenida p {
            font-size: 16px;
            color: #666;
        }
    </style>
</head>
<body>
    <header class="header">
        <p>Usuario: <?php echo $usuario; ?></p>
        <nav>
            <ul class="menu-links">
                <li><a href="chat.php">Iniciar chat</a></li>
                <li><a href="graficas.php">Gráficas</a></li>
                <li><a href="registrar_ingreso.php">Registrar ingreso</a></li>
                <li><a href="registrar_gasto.php">Registrar gasto</a></li>
                <li><a href="mostrar_balancetotal.php">Mostrar Saldo Actual</a></li>
                <li><a href="perfil.php">Perfil</a></li>
            </ul>
        </nav>
        <div class="btn">
            <a href="index.html"><button>Cerrar Sesión</button></a>
        </div>
    </header>

    <div class="bienvenida">
        <h1>Hola, <?php echo $usuario; ?>!</h1>
        <h4>Bienvenido al sistema de administración financiera.</h4>
        <h5>Aquí puedes tener un bot financiero, gestionar tus ingresos, gastos, y consultar el saldo actual.</h5>
    </div>
</body>
</html>

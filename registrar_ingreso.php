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

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el monto del formulario
    $monto_ingreso = $_POST['monto_ingreso'];

    // Consulta para obtener los valores actuales de balance del usuario
    $sql_select = "SELECT balance_ingreso, balance_egreso FROM datos WHERE id = ?";
    $stmt = $conn->prepare($sql_select);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Actualizar los valores de balance
        $balance_ingreso = $row['balance_ingreso'] + $monto_ingreso;
        $balance_egreso = $row['balance_egreso'];
        $balance_total = $balance_ingreso - $balance_egreso;
        
        // Consulta de actualización para el registro del usuario
        $sql_update = "UPDATE datos SET balance_ingreso = ?, balance_total = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ddi", $balance_ingreso, $balance_total, $user_id);
        
        if ($stmt_update->execute()) {
            echo "Ingreso registrado y balance actualizado correctamente.";
        } else {
            echo "Error: " . $stmt_update->error;
        }
    } else {
        // Si el usuario no tiene registro previo, creamos uno nuevo
        $balance_ingreso = $monto_ingreso;
        $balance_egreso = 0;
        $balance_total = $balance_ingreso;

        $sql_insert = "INSERT INTO datos (id, balance_ingreso, balance_egreso, balance_total) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iddd", $user_id, $balance_ingreso, $balance_egreso, $balance_total);

        if ($stmt_insert->execute()) {
            echo "Ingreso registrado correctamente.";
        } else {
            echo "Error: " . $stmt_insert->error;
        }
    }

    // Mensaje para volver a la página principal
    echo "<br><a href='principal_chatbot.php'>Volver a la página principal</a>";
} else {
    // Mostrar el formulario si no se ha enviado
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" href="styles.css"> <!-- Incluyendo el archivo CSS -->
    </head>
    <body>
        
        <div class="cuadro"> <!-- Contenedor para el formulario -->
            <h1>Registrar Ingreso</h1>    
            <title>Registrar Ingreso</title>
            <form action="registrar_ingreso.php" method="POST">
                <label for="monto_ingreso">Monto del Ingreso:</label>
                <input type="number" id="monto_ingreso" name="monto_ingreso" required><br><br>
                <button type="submit">Registrar Ingreso</button>
            </form>
            <br>
            <a href="principal_chatbot.php">Volver a la página principal</a>
        </div>
        <br>
        
    </body>
    </html>
    <?php
}

// Cerrar conexión
$conn->close();
?>

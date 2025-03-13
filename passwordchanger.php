<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <title>Cambia tu contraseña</title>
    <link rel="stylesheet" href="css/passwordchanger.css">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <h1>Cambia tu clave</h1>

        <?php
        require 'db.php';
        
        if (isset($_GET['code']) && isset($_GET['mail'])) {
            $code = $_GET['code'];
            $mail = $_GET['mail'];

            // Verificamos que el código es correcto y que no ha expirado
            $checkEmail = $db->prepare("SELECT resetPassCode FROM usuari WHERE mail = :mail AND resetPassExpiry > NOW()");
            $checkEmail->bindParam(':mail', $mail);
            $checkEmail->execute();
            $result = $checkEmail->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['resetPassCode'] == $code) {
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $newPassword = $_POST['new_password'];
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    
                    $updateQuery = $db->prepare("UPDATE usuari SET passHash = :password WHERE mail = :mail");
                    $updateQuery->bindParam(':password', $hashedPassword);
                    $updateQuery->bindParam(':mail', $mail);
                    if ($updateQuery->execute()) {
                        echo "<p class='success'>Contraseña cambiada con éxito.</p>";
                    } else {
                        echo "<p class='error'>Error al cambiar la contraseña.</p>";
                    }
                }
        ?>
                <form method="POST">
                    <input type="password" name="new_password" placeholder="Nueva contraseña" required>
                    <button type="submit">Actualizar</button>
                </form>
        <?php
            } else {
                echo "<p class='error'>Código inválido o expirado.</p>";
            }
        } else {
            echo "<p class='error'>Solicitud no válida.</p>";
        }
        ?>
    </div>
</body>
</html>

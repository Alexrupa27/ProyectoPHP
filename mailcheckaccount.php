<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/mailcheckaccount.css">
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <title>Activación de Cuenta</title>
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <h1>Activación de Cuenta</h1>

        <?php
        require 'db.php';

        if (isset($_GET['code']) && isset($_GET['mail'])) {
            $code = $_GET['code'];
            $mail = $_GET['mail'];

            $checkEmail = $db->prepare("SELECT activationCode FROM usuari WHERE mail = :mail");
            $checkEmail->bindParam(':mail', $mail);
            $checkEmail->execute();
            $result = $checkEmail->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                if ($result['activationCode'] == $code) {
                    echo "<p class='success'>Cuenta activada correctamente.</p>";
                    $updateQuery = $db->prepare("UPDATE usuari SET activationDate = NOW(), active = 1 WHERE mail = :mail");
                    $updateQuery->bindParam(':mail', $mail);
                    $updateQuery->execute();
                } else {
                    echo "<p class='error'>Código incorrecto.</p>";
                }
            } else {
                echo "<p class='error'>Correo no encontrado.</p>";
            }
        } else {
            echo "<p class='error'>Solicitud inválida.</p>";
        }
        ?>
    </div>
</body>
</html>

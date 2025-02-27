<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #F44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Activación de Cuenta</h1>
        <?php 
        // http://localhost/ProyectoPHP/mailCheckAccount.php?code=cbd5d2dbfb8e86c36f2b9b704051c62731ff44b1378f97e1ef7b1f822d86565f&mail=alex.ruizp@educem.net
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación de Cuenta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #F44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Activación de Cuenta</h1>
        <?php 
        // http://localhost/ProyectoPHP/mailCheckAccount.php?code=cbd5d2dbfb8e86c36f2b9b704051c62731ff44b1378f97e1ef7b1f822d86565f&mail=alex.ruizp@educem.net

        $code = $_GET['code'];
        $mail = $_GET['mail'];
        $code = $_GET['code'];
        $mail = $_GET['mail'];

        require 'db.php';
        require 'db.php';

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
        ?>
    </div>
</body>
</html>
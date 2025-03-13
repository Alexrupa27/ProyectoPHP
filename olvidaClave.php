<?php

require './db.php';

use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    $mail = new PHPMailer();
    $mail->IsSMTP();
    
    $mail->CharSet = "UTF-8"; 
    $mail->Encoding = 'base64';
    //Configuració del servidor de Correu
    //Modificar a 0 per eliminar msg error
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host       = 'smtp.gmail.com';
    $mail->Port       = 587;

    //Credencials del compte GMAIL
    $mail->Username = 'alex.ruizp@educem.net';
    $mail->Password = 'memv kjmn xjxe afbq';
    //Dades del correu electrònic
    $mail->SetFrom('alex.ruizp@educem.net', 'Pistachad');
    $mail->Subject = '¡Cambia tu contraseña!';

    // Insertar usuario en la base de datos
    $activeCode = hash('sha256', rand(10000, 90000));
    

    //Destinatari
    $address = $email;
    $mail->AddAddress($address, 'Test');
    //Enviament
    $urlActivation = "http://localhost/PROYECTOPHP/passwordchanger.php?code=$activeCode&mail=$email";
    $message       = "
                        <html>
                            <head>
                                <title>Cambio de constraseña</title>
                                <style>
                                    body {
                                        font-family: Arial, sans-serif;
                                        background-color: #f4f4f4;
                                        padding: 20px;
                                    }
                                    .container {
                                        background-color: #fff;
                                        padding: 20px;
                                        max-width: 600px;
                                        margin: 0 auto;
                                        border-radius: 8px;
                                        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                                    }
                                    .header {
                                        text-align: center;
                                        font-size: 24px;
                                        margin-bottom: 20px;
                                    }
                                    .button {
                                        display: inline-block;
                                        background-color: #4CAF50;
                                        color: white;
                                        padding: 15px 32px;
                                        text-align: center;
                                        text-decoration: none;
                                        font-size: 18px;
                                        border-radius: 5px;
                                        margin-top: 20px;
                                    }
                                    .button:hover {
                                        background-color: #45a049;
                                    }
                                    .footer {
                                        text-align: center;
                                        font-size: 14px;
                                        color: #888;
                                        margin-top: 30px;
                                    }
                                </style>
                            </head>
                            <body>
                                <div class='container'>
                                    <div class='header'>
                                        <h2>!Cambia tu contraseña!</h2>
                                    </div>
                                        <p>Haz click en este botón para camiar tu contraseña</p>
                                        <a href='" . $urlActivation . "' class='button'>Cambiar contraseña</a>
                                    </div>
                            </body>
                            </html>
                            ";
    $mail->MsgHTML($message);

    try {
        $checkEmail = $db->prepare("SELECT * FROM usuari WHERE mail = :email");
        $checkEmail->execute([':email' => $email]);
        echo "Ha ejecutado";
        if (!$checkEmail->rowCount() > 0) {
            echo "El correo no está registrado.";
        } else {

            $stmt = $db->prepare("UPDATE usuari SET resetPassCode = :resetPassCode, resetPassExpiry = NOW() + INTERVAL 1 MINUTE WHERE mail = :email");
            $stmt->execute([

                ':resetPassCode' => $activeCode,
                ':email' => $email
            ]);

            try {
                $mail->send();
                header("Location: ./php/mailChangePassword.php");
                exit();

            } catch (PDOException $e) {
                echo "Hubo un error al enviar el correo. Mailer Error: {$mail->ErrorInfo}";
            }
            exit();
        }
    } catch (PDOException $e) {
        echo "Error en el registro: " . $e->getMessage();
    }
}

<?php
                
                require './db.php';
                
                use PHPMailer\PHPMailer\PHPMailer;
                require 'vendor/autoload.php';

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $username = $_POST["username"];
                    $name = $_POST["name"];
                    $surname = $_POST["surname"];
                    $email = $_POST["email"];
                    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    
                    $mail = new PHPMailer();
                    $mail->IsSMTP();
                    //Configuració del servidor de Correu
                    //Modificar a 0 per eliminar msg error
                    $mail->SMTPDebug = 2;
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'tls';
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 587;
                    
                    //Credencials del compte GMAIL
                    $mail->Username = 'alex.ruizp@educem.net';
                    $mail->Password = 'memv kjmn xjxe afbq';
                    //Dades del correu electrònic
                    $mail->SetFrom('alex.ruizp@educem.net','Pistachad');
                    $mail->Subject ="¡Activa tu cuenta ahora!";
                    

                    
                    //Destinatari
                    $address = $email;
                    $mail->AddAddress($address, 'Test');
                    //Enviament
                    $activeCode = hash('sha256', rand(10000, 90000));
                    $urlActivation = "http://localhost/Pistachad2/ProyectoPHP/mailcheckaccount.php?code=$activeCode&mail=$email";
                    $message= "
                        <html>
                            <head>
                                <title>Activación de cuenta</title>
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
                                        <h2>¡Bienvenido a Pisachad!</h2>
                                    </div>
                                        <p>Estamos emocionados de que te hayas registrado con nosotros. Para completar tu registro y activar tu cuenta, haz clic en el siguiente botón:</p>
                                        <a href='" . $urlActivation . "' class='button'>Activar Cuenta</a>
                                        <p class='footer'>Si no has realizado este registro, puedes ignorar este mensaje.</p>
                                    </div>
                            </body>
                            </html>
                            ";
                            $mail->MsgHTML($message);
                    
                    try {
                        // Verificar que el correo no esté registrado
                        $checkEmail = $db->prepare("SELECT * FROM usuari WHERE mail = :email");
                        $checkEmail->execute([':email' => $email]);
                        echo "Ha ejecutado";
                        if ($checkEmail->rowCount() > 0) {
                            echo "Ha encontrado correo";
                            echo "El correo ya está registrado.";
                        } else {
                            // Insertar usuario en la base de datos
                            $activeCode = hash('sha256', rand(10000, 90000));


                            $stmt = $db->prepare("INSERT INTO usuari (mail, username, passHash, userFirstName, userLastName, creationDate, removeDate, lastSignIn, active, activationDate, activationCode, resetPassExpiry, resetPassCode) 
                            VALUES (:email, :username, :password, :name, :surname, now(), null, null, 0, null, :activeCode, null, null)");
                            $stmt->execute([
                                ':email' => $email,
                                ':username' => $username,
                                ':password' => $password,
                                ':name' => $name,
                                ':surname' => $surname,
                                ':activeCode' => $activeCode
                            ]);
                            try{
                                $mail->send();
                                echo 'El correo ha sido enviado exitosamente.';     
                            }
                            catch (PDOException $e) {
                            echo "Hubo un error al enviar el correo. Mailer Error: {$mail->ErrorInfo}";
                            }
                            header("Location: ./Login.php"); // Redirigir al usuario autenticado
                            exit();
                        }
                    } catch (PDOException $e) {
                        echo "Error en el registro: " . $e->getMessage();
                    }
                }
        ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Login y Registro con HTML5 y CSS3</title>
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
        <!-- Estilos CSS -->
        <link rel="stylesheet" href="./css/register.css">
        <!-- Favicon -->
        <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    </head>
    <body>
        <main>
            <article>
                <section>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="register">
                        <!-- <img src="./img/pistaBackground.png" alt="Imagen pistacho" class="pistaRegister">    -->
                        <h1 class="titleRegister">Regístrate</h1>
                        
                        <input type="text" name="username" placeholder="Nombre de usuario       "><br/>
                        <input  type="text" name="name" placeholder="Nombre"><br/>
                        <input type="text" name="surname" placeholder="Apellidos"><br/>
                        <input type="email" name="email" placeholder="Correo electr&oacute;nico"><br/>
                        <input type="password" name="password" placeholder="Contrase&ntilde;a"><br/>
                        <input type="password" name="password2" placeholder="Repite la Contrase&ntilde;a"><br/>
                        <button type="submit">Entrar</button>
                        
                        
                        <p class="alreadyAccount">Ya tienes cuenta ? <a href="./Login.php" class="alreadyAccount">Inicia Sesión</a></p>
                    
                    </form>
                </section>
            </article>
        </main>
    </body>
    </html>
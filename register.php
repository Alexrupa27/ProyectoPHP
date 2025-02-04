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
                <form action="/" method="POST" class="register">
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
        <?php
            require 'db.php';

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST["username"];
                $name = $_POST["name"];
                $surname = $_POST["surname"];
                $email = $_POST["email"];
                $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Encriptar la contraseña

                try {
                    // Verificar que el correo no esté registrado
                    $checkEmail = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
                    $checkEmail->execute([$email]);

                    if ($checkEmail->rowCount() > 0) {
                        echo "El correo ya está registrado.";
                    } else {
                        // Insertar usuario en la base de datos
                        $stmt = $db->prepare("INSERT INTO usuarios (username, name, surname, email, password) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$username, $name, $surname, $email, $password]);

                        echo "Registro exitoso. <a href='login.php'>Inicia sesión</a>";
                    }
                } catch (PDOException $e) {
                    echo "Error en el registro: " . $e->getMessage();
                }
            }
    ?>
    
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login pistachad</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="css/login.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <img src="./img/pistaBackground.png" alt="Background de la web" class="pistaBackground">
        <p class="titulo">PISTACHAD</p>
    </header>
    <main>
        <article>
            <section>
                <form action="/" method="POST" class="Login">
                    <h1>Inicia Sesión</h1>
                    <input type="email" name="email" placeholder="Correo electr&oacute;nico"><br/>
                    <input type="password" name="password" placeholder="Contrase&ntilde;a"><br/>
                    
                    <button type="submit">Entrar</button>
                    <p class="noAccount">Aun no tienes cuenta?</p>
                    <p>
                        <a href="./register.php">Registrate</a>
                    </p>

                </form>
            </section>
        </article>
        <img src="./img/logo.png" alt="Logo de Pistachad" class="pistachad">
    </main>
    <footer>
        <p class="copy">&copy; 2025 PistaChad. Todos los derechos reservados.</p>
    </footer>
    <?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        $stmt = $db->prepare("SELECT id, username, password FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            header("Location: dashboard.php"); // Redirigir al usuario autenticado
            exit();
        } else {
            echo "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        echo "Error en el inicio de sesión: " . $e->getMessage();
    }
}
?>
    
</body>
</html>
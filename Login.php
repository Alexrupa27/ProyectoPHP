<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Pistachad</title>
    <link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 30%;
            box-shadow: 0px 0px 10px black;
            border-radius: 10px;
        }
        .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <img src="./img/logo.png" alt="Background de la web" class="pistaBackground">
        <p class="titulo">PISTACHAD</p>
    </header>
<?php
require './db.php';
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        $stmt = $db->prepare("SELECT idUser, username, passHash, active, mail FROM usuari WHERE mail = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['passHash'])) {
            if ($user["active"]==1){
                $_SESSION["idUser"] = $user["idUser"];
                $_SESSION["username"] = $user["username"];
                header("Location: ./php/home.php"); // Redirigir al usuario autenticado
                $stmt = $db->prepare("UPDATE usuari SET lastSignIn = now() WHERE mail = ?");
                $stmt->execute([$email]);
                exit();
            }
            else{ echo "Usuario no activo.";}
        } else {
            echo "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        echo "Error en el inicio de sesión: " . $e->getMessage();
    }
}
?>
    <main>
        <article>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="Login">
                <h1>Inicia Sesión</h1>
                <input type="email" name="email" placeholder="Correo electrónico"><br/>
                <input type="password" name="password" placeholder="Contraseña"><br/>
                <button type="submit">Entrar</button>
                <p class="noAccount">¿Aún no tienes cuenta?</p>
                <a href="register.php">Regístrate</a>
                <br>
                <br>
                <a href="#" id="openModal">Olvidé mi clave</a>
            </form>
        </article>
    </main>
    <footer>
        <p class="copy">&copy; 2025 PistaChad. Todos los derechos reservados.</p>
    </footer>
    
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Recuperar Clave</h2>
            <form action="olvidaClave.php" method="POST">
                <input type="email" id="PassCorreo" name="email" placeholder="Correo electrónico" required><br/>
                <button type="submit">Enviar mail</button>
            </form>
        </div>
    </div>
    
    <script>
        var modal = document.getElementById("forgotPasswordModal");
        var btn = document.getElementById("openModal");
        var span = document.getElementsByClassName("close")[0];
        
        btn.onclick = function() {
            modal.style.display = "block";
        }
        
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>
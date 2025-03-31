<?php
//iniciar SESSION
  session_start();

  //comprobar si el usuario no tiene SESSION activa
  if (!isset($_SESSION['username'])) {
    //Redirigir al index si no hay SESSION
    header('Location: ../Login.php');
    exit();
  }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PistaChad</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <img src="../assets/icon.png" alt="Logo PistaChad">
        <nav id="myLinks">
          <ul>
            <a href="#">Inicio</a>
            <a href="./perfil.php">Perfil</a>
            <a href="../Disconnect.php">Desconectar</a>
          </ul>
        </nav>
        <a href="javascript:void(0);" class="icon" onclick="hamburgesa()">
            <i class="fa fa-bars"></i>
          </a>
    </header>
    <br>
    <main>
    <div class="post-container">
      <form action="upload_post.php" method="POST" enctype="multipart/form-data">
        <textarea name="post_content" placeholder="¿Qué estás pensando?" rows="4" required></textarea>
        <input type="file" name="post_image" accept="image/*">
        <button type="submit">Publicar</button>
      </form>
    </div>
    <div>
      <p>Una manita de pistachos illo</p>
      <img src="../assets/pistachosIMG/manopistacho.jpg" alt="mano con pistachos">
    </div>
    <div>
      <p>ai crese eso</p>
      <img src="../assets/pistachosIMG/frutopistacho.jpg" alt="mano con pistachos">
    </div>
    <div>
      <p>muixo pistaixo</p>
      <img src="../assets/pistachosIMG/bolpistachos.png" alt="mano con pistachos">
    </div>
    </main>
    <footer>
    <img src="../assets/pistachad.png" alt="Pistachad Logo" />
    <nav>
        <a href="./about">Acerca de</a> |
        <a href="./privacy">Privacidad</a> |
        <a href="./contact">Contacto</a>
    </nav>
    <p>&copy; 2025 PistaChad. Todos los derechos reservados.</p>
    <p>Todas las marcas registradas mencionadas aquí son propiedad de sus respectivos dueños.</p>
    <br>
  </footer>
    <script src="../js/home.js"></script>
</body>
</html>

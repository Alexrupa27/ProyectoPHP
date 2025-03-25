<?php
// Iniciar sesión
session_start();

// Comprobar si el usuario tiene sesión activa
if (!isset($_SESSION['username'])) {
    header('Location: ../Login.php');
    exit();
}

// Conectar a la base de datos
require '../db.php';

// Obtener el nombre de usuario de la sesión
$username = $_SESSION["username"];

// Preparar la consulta para obtener los datos del usuario
$stmt = $db->prepare('SELECT ubi, descripcio, edat, fotoPerfil FROM usuari WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

// Cerrar la conexión a la base de datos
$db = null;

// Procesar la imagen de perfil correctamente
$fotoPerfil = '../assets/default-profile.png'; // Imagen por defecto
if (!empty($user['fotoPerfil'])) {
    // Asegurar que la imagen está en formato base64
    $fotoPerfil = 'data:image/jpeg;base64,' . base64_encode($user['fotoPerfil']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PistaChad</title>
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>
<body>
    <header>
        <img src="../assets/icon.png" alt="Logo PistaChad">
        <nav id="myLinks">
          <ul>
            <a href="./home.php">Inicio</a>
            <a href="./editarPerfil.php">Editar Perfil</a>
            <a href="../Disconnect.php">Desconectar</a>
          </ul>
        </nav>
        <a href="javascript:void(0);" class="icon" onclick="hamburgesa()">
            <i class="fa fa-bars"></i>
        </a>
    </header>
    <br>
    <main>
      <div class="profileContainer">
        <!-- Mostrar foto de perfil correctamente -->
        <img src="<?php echo $fotoPerfil; ?>" alt="Foto de perfil">

        <div class="profileInfo">
            <h1><?php echo htmlspecialchars($username); ?></h1>
            <p><?php echo !empty($user['descripcio']) ? htmlspecialchars($user['descripcio']) : 'Sin descripción'; ?></p>
            <p><strong>Ubicación:</strong> <?php echo !empty($user['ubi']) ? htmlspecialchars($user['ubi']) : 'No especificado'; ?></p>
            <p><strong>Edad:</strong> <?php echo !empty($user['edat']) ? htmlspecialchars($user['edat']) : 'No especificado'; ?></p>
        </div>
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
    </footer>
    <script src="../js/home.js"></script>
</body>
</html>

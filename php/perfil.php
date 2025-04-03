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
$stmt = $db->prepare('SELECT ubi, descripcio, edat, fotoPerfil, mail FROM usuari WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró el usuario
if (!$user) {
    // Manejar el caso donde no se encontró el usuario
    $user = ['ubi' => '', 'descripcio' => '', 'edat' => '', 'fotoPerfil' => null, 'mail' => ''];
}

// Procesar la imagen de perfil correctamente
$fotoPerfil = '../img/default.png'; // Imagen por defecto

if (!empty($user['fotoPerfil'])) {
    // Comprobar si la imagen ya es una ruta
    if (is_string($user['fotoPerfil']) && file_exists($user['fotoPerfil'])) {
        // Es una ruta de archivo válida
        $fotoPerfil = $user['fotoPerfil'];
    } else {
        // Podría ser datos binarios, intentar convertir a base64
        try {
            $fotoPerfil = 'data:image/jpeg;base64,' . base64_encode($user['fotoPerfil']);
        } catch (Exception $e) {
            // Si falla, usar la imagen por defecto
            $fotoPerfil = '../img/default.png';
        }
    }
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
    <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
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
        <img src="<?php echo htmlspecialchars($fotoPerfil); ?>" alt="Foto de perfil">
         
        <div class="profileInfo">
            <h1><?php echo htmlspecialchars($username); ?></h1>
            <p><strong>Edad:</strong> <?php echo !empty($user['edat']) ? htmlspecialchars($user['edat']) : 'No especificado'; ?></p>
            <p><strong>Ubicación:</strong> <?php echo !empty($user['ubi']) ? htmlspecialchars($user['ubi']) : 'No especificado'; ?></p>
            <p><?php echo !empty($user['descripcio']) ? htmlspecialchars($user['descripcio']) : 'Sin descripción'; ?></p>
        </div>
      </div>
      
      <?php
      // Obtener las publicaciones del usuario actual
      try {
          // Usamos el email del usuario para buscar sus publicaciones
          $userEmail = $user['mail'];
          
          // Solo mostramos publicaciones del usuario cuyo perfil estamos viendo
          $sql = "SELECT id, dataPublicacio, fotoPublicacio, contingut, likesPubli, dislikesPubli, mail 
                 FROM publicacio 
                 WHERE mail = ?
                 ORDER BY dataPublicacio DESC
                 LIMIT 3";
          
          $postStmt = $db->prepare($sql);
          $postStmt->execute([$userEmail]);
          
          if ($postStmt->rowCount() > 0) {
              while($row = $postStmt->fetch(PDO::FETCH_ASSOC)) {
                  // Formatear fecha
                  $fecha = date('d/m/Y H:i', strtotime($row["dataPublicacio"]));
                  
                  // Inicializar variable para la imagen de la publicación
                  $fotoPublicacion = "";
                  
                  // Verificar si hay una imagen en la publicación y procesarla correctamente
                  if (!empty($row["fotoPublicacio"])) {
                      try {
                          $fotoPublicacion = '<img src="data:image/jpeg;base64,' . base64_encode($row["fotoPublicacio"]) . '" alt="Imagen de publicación" class="postImage">';
                      } catch (Exception $e) {
                          // Si hay un error al procesar la imagen, no mostramos nada
                          $fotoPublicacion = '<p class="error-message">Error al cargar la imagen</p>';
                      }
                  }
                  
                  // Mostrar publicación con la foto del autor
                  echo '<div class="socialPost">
                          <div class="postHeader">
                              <img src="' . htmlspecialchars($fotoPerfil) . '" alt="Foto de perfil" class="profilePic">
                              <div>
                                  <p class="userName">' . htmlspecialchars($username) . '</p>
                                  <p class="postDate">' . $fecha . '</p>
                              </div>
                          </div>
                          <p class="postContent">' . htmlspecialchars($row["contingut"]) . '</p>
                          ' . $fotoPublicacion . '
                          <div class="postActions">
                              <button class="likeButton" disabled><i class="fa fa-heart"></i> ' . $row["likesPubli"] . '</button>
                          </div>
                      </div>';
              }
          } else {
              echo '<p class="no-posts-message">Este usuario no ha realizado publicaciones.</p>';
          }
      } catch (PDOException $e) {
          echo '<p class="error-message">Error al cargar las publicaciones: ' . $e->getMessage() . '</p>';
      }
      
      // Cerrar la conexión a la base de datos al final del archivo
      $db = null;
      ?>
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
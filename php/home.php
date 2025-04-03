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
        <li><a href="#">Inicio</a></li>
        <li><a href="./perfil.php">Perfil</a></li>
        <li><a href="../Disconnect.php">Desconectar</a></li>
      </ul>
    </nav>
    <a href="javascript:void(0);" class="icon" onclick="hamburgesa()">
        <i class="fa fa-bars"></i>
    </a>
</header>

    <br>
    <main>
    <div class="postContainer">
        <form action="../php/SubirPost.php" method="POST" enctype="multipart/form-data">
          <textarea name="post_content" placeholder="¿Qué estás pensando?" rows="4" required></textarea>
          <input id="botonsubir" type="file" name="post_image" accept="image/*">
          <label for="tipo">Selecciona una opción:</label>
            <select name="tipo" id="tipo" class="styled-combobox">
            <option value="sin_filtro">Sin filtro</option>
            <option value="fruto">Fruto</option>
            <option value="arbol">Árbol</option>
            </select>
          <button type="submit">Publicar</button>
        </form>
      </div>
      <div class="filtros">
        <a href="?filtro=todos" class="filtro-btn <?php echo $filtro == 'todos' ? 'active' : ''; ?>">Todos</a>
        <a href="?filtro=fruto" class="filtro-btn <?php echo $filtro == 'fruto' ? 'active' : ''; ?>">Fruto</a>
        <a href="?filtro=arbol" class="filtro-btn <?php echo $filtro == 'arbol' ? 'active' : ''; ?>">Árbol</a>
      </div>
      
      <?php
//iniciar SESSION
  session_start();

  //comprobar si el usuario no tiene SESSION activa
  if (!isset($_SESSION['username'])) {
    //Redirigir al index si no hay SESSION
    header('Location: ../Login.php');
    exit();
  }

  require '../db.php';

  $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todos';
  
  // Base de la consulta para publicaciones
$sql = "SELECT id, dataPublicacio, fotoPublicacio, contingut, likesPubli, dislikesPubli, mail 
FROM publicacio";

// Aplicar filtro si es necesario
if ($filtro != 'todos' && ($filtro == 'fruto' || $filtro == 'arbol')) {
$sql .= " WHERE categoria = :filtro"; // Asegúrate de agregar el WHERE de forma correcta
}
$sql .= " ORDER BY dataPublicacio DESC";  // Esto va fuera del bloque if para que siempre se ordenen los resultados

// Preparar y ejecutar la consulta
$postStmt = $db->prepare($sql);

if ($filtro != 'todos' && ($filtro == 'fruto' || $filtro == 'arbol')) {
$postStmt->bindParam(':filtro', $filtro);
}
$postStmt->execute();
?>

<!-- Resto del HTML header y estructura -->

<!-- Sección donde muestras las publicaciones -->
<?php
// Este código reemplaza la parte donde muestras las publicaciones en home.php

// Mostrar publicaciones
if ($postStmt->rowCount() > 0) {
    while($row = $postStmt->fetch(PDO::FETCH_ASSOC)) {
        // Convertir la imagen BLOB a base64 para mostrarla
        $fotoPublicacion = "";
        if ($row["fotoPublicacio"]) {
            $fotoPublicacion = '<img src="data:image/jpeg;base64,' . base64_encode($row["fotoPublicacio"]) . '" alt="Imagen de publicación">';
        }
        
        // Formatear fecha
        $fecha = date('d/m/Y H:i', strtotime($row["dataPublicacio"]));
        
        // Obtener la foto de perfil del autor usando su mail
        $autorMail = $row["mail"];
        $fotoPerfilAutor = '../img/default.png'; // Foto por defecto
        
        // Consultar la foto de perfil del autor en la tabla de usuarios
        $userStmt = $db->prepare('SELECT fotoPerfil, username FROM usuari WHERE mail = ?');
        $userStmt->execute([$autorMail]);
        $autorData = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($autorData && !empty($autorData['fotoPerfil'])) {
            // Comprobar si la imagen ya es una ruta
            if (file_exists($autorData['fotoPerfil'])) {
                // Es una ruta de archivo válida
                $fotoPerfilAutor = $autorData['fotoPerfil'];
            } else {
                // Podría ser datos binarios, intentar convertir a base64
                try {
                    $fotoPerfilAutor = 'data:image/jpeg;base64,' . base64_encode($autorData['fotoPerfil']);
                } catch (Exception $e) {
                    // Si falla, usar la imagen por defecto
                    $fotoPerfilAutor = '../img/default.png';
                }
            }
        }
        if(!empty($autorData['username'])){
          $autorMail = $autorData['username'];
        }
        
        // Verificar si el usuario actual ya dio like a esta publicación
        $username = $_SESSION['username'];
        $userMailStmt = $db->prepare('SELECT mail FROM usuari WHERE username = ?');
        $userMailStmt->execute([$username]);
        $currentUserMail = $userMailStmt->fetch(PDO::FETCH_ASSOC)['mail'];
        
        $hasLiked = false;
        if ($currentUserMail) {
            $likeCheckStmt = $db->prepare('SELECT idReaccio FROM reaccio WHERE mail = ? AND idPubli = ?');
            $likeCheckStmt->execute([$currentUserMail, $row["id"]]);
            $hasLiked = ($likeCheckStmt->rowCount() > 0);
        }
        
        // Clase para el botón de like dependiendo de si el usuario ya dio like
        $likeButtonClass = $hasLiked ? 'likeButton active' : 'likeButton';
        
        // Mostrar publicación con la foto del autor
        echo '<div class="socialPost">
                <div class="postHeader">
                    <img src="' . htmlspecialchars($fotoPerfilAutor) . '" alt="Foto de perfil" class="profilePic">
                    <div>
                        <p class="userName">' . htmlspecialchars($autorMail) . '</p>
                        <p class="postDate">' . $fecha . '</p>
                    </div>
                </div>
                <p>' . htmlspecialchars($row["contingut"]) . '</p>
                ' . $fotoPublicacion . '
                <div class="postActions">
                    <button class="' . $likeButtonClass . '" data-post-id="' . $row["id"] . '">
                        <i class="fa fa-heart"></i> <span class="likeCount">' . $row["likesPubli"] . '</span>
                    </button>
                    <button class="commentToggle" data-post-id="' . $row["id"] . '">
                        <i class="fa fa-comment"></i> Comentarios
                    </button>
                </div>
            </div>';
            echo '<div class="commentSection" id="comments-' . $row["id"] . '" style="display: none;">
            <div class="existingComments" id="existing-comments-' . $row["id"] . '">';
    
    // Consultar comentarios existentes para esta publicación
    $commentStmt = $db->prepare('SELECT c.idComentari, c.mail, c.contingut, c.dataComentari, u.username, u.fotoPerfil 
                                FROM comentari c 
                                LEFT JOIN usuari u ON c.mail = u.mail 
                                WHERE c.idPublicacio = ? 
                                ORDER BY c.dataComentari ASC');
    $commentStmt->execute([$row["id"]]);
    
    if ($commentStmt->rowCount() > 0) {
        while ($comment = $commentStmt->fetch(PDO::FETCH_ASSOC)) {
            // Preparar foto de perfil del comentarista
            $commentUserPic = '../img/default.png'; // Foto por defecto
            
            if (!empty($comment['fotoPerfil'])) {
                // Misma lógica que para la foto del autor de la publicación
                if (file_exists($comment['fotoPerfil'])) {
                    $commentUserPic = $comment['fotoPerfil'];
                } else {
                    try {
                        $commentUserPic = 'data:image/jpeg;base64,' . base64_encode($comment['fotoPerfil']);
                    } catch (Exception $e) {
                        $commentUserPic = '../img/default.png';
                    }
                }
            }
            
            // Nombre a mostrar (username o mail)
            $commentUserName = !empty($comment['username']) ? $comment['username'] : $comment['mail'];
            
            // Formatear fecha del comentario
            $commentDate = date('d/m/Y H:i', strtotime($comment['dataComentari']));
            
            // Mostrar comentario
            echo '<div class="comment">
                    <div class="commentHeader">
                        <img src="' . htmlspecialchars($commentUserPic) . '" alt="Foto de perfil" class="commentProfilePic">
                        <div>
                            <p class="commentUserName">' . htmlspecialchars($commentUserName) . '</p>
                        </div>
                    </div>
                    <p class="commentContent">' . htmlspecialchars($comment['contingut']) . '</p>
                </div>';
        }
    } else {
        echo '<p class="noComments">No hay comentarios aún.</p>';
    }
    
    echo '</div>'; // Cierre de existingComments
    
    // Formulario para añadir un nuevo comentario
    echo '<form class="commentForm" data-post-id="' . $row["id"] . '">
            <textarea name="comment_content" placeholder="Escribe un comentario..." rows="2" required></textarea>
            <button type="submit">Comentar</button>
          </form>
        </div>'; // Cierre de commentSection
    
    echo '</div>'; // Cierre de socialPost
    }
} else {
    echo '<p class="no-posts-message">No hay publicaciones disponibles.</p>';
}
?>
    <br>
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
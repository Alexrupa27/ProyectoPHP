<?php
// Iniciar sesión para obtener datos del usuario
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header('Location: ../Login.php');
    exit();
}

// Conectar a la base de datos
require '../db.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger el contenido del post
    $post_content = filter_input(INPUT_POST, 'post_content', FILTER_SANITIZE_STRING);
    
    // Recoger la categoría seleccionada
    $categoria = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);
    
    // Validar que la categoría sea válida
    $categorias_validas = ['sin_filtro', 'fruto', 'arbol'];
    if (!in_array($categoria, $categorias_validas)) {
        $categoria = 'sin_filtro'; // Valor por defecto si no es válida
    }
    
    // Obtener email del usuario desde la sesión
    // Primero necesitamos obtener el email asociado al username
    $username = $_SESSION["username"];
    $stmt = $db->prepare("SELECT mail FROM usuari WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // Si no se encuentra el usuario, redirigir con error
        header('Location: ../error.php?msg=Usuario no encontrado');
        exit();
    }
    
    $email = $user['mail'];
    
    // Inicializar variables
    $foto_publicacion = null;
    $tiene_imagen = false;
    
    // Procesar la imagen si se ha subido una
    if (!empty($_FILES['post_image']['name'])) {
        // Verificar que el archivo sea una imagen
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['post_image']['type'], $allowed_types)) {
            // Verificar tamaño (máximo 5MB)
            if ($_FILES['post_image']['size'] <= 5000000) {
                // Leer el contenido del archivo
                $foto_publicacion = file_get_contents($_FILES['post_image']['tmp_name']);
                $tiene_imagen = true;
            } else {
                // Redirigir con error si la imagen es demasiado grande
                header('Location: ../error.php?msg=La imagen es demasiado grande (máximo 5MB)');
                exit();
            }
        } else {
            // Redirigir con error si el archivo no es una imagen
            header('Location: ../error.php?msg=El archivo no es una imagen válida');
            exit();
        }
    }
    
    // Fecha actual para la publicación
    $fecha_actual = date('Y-m-d H:i:s');
    
    // Inicializar likes y dislikes a 0
    $likes = 0;
    $dislikes = 0;
    
    try {
        // Preparar la consulta SQL según si hay imagen o no
        if ($tiene_imagen) {
            $stmt = $db->prepare("INSERT INTO publicacio (dataPublicacio, fotoPublicacio, contingut, likesPubli, dislikesPubli, mail, categoria) 
                                VALUES (:fecha, :foto, :contenido, :likes, :dislikes, :email, :categoria)");
            $stmt->bindParam(':foto', $foto_publicacion, PDO::PARAM_LOB);
        } else {
            $stmt = $db->prepare("INSERT INTO publicacio (dataPublicacio, contingut, likesPubli, dislikesPubli, mail, categoria) 
                                VALUES (:fecha, :contenido, :likes, :dislikes, :email, :categoria)");
        }
        
        // Vincular parámetros comunes
        $stmt->bindParam(':fecha', $fecha_actual);
        $stmt->bindParam(':contenido', $post_content);
        $stmt->bindParam(':likes', $likes, PDO::PARAM_INT);
        $stmt->bindParam(':dislikes', $dislikes, PDO::PARAM_INT);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':categoria', $categoria);
        
        // Ejecutar la consulta
        $stmt->execute();
        
        // Redirigir a la página principal con mensaje de éxito
        header('Location: ../php/home.php?success=1');
        exit();
        
    } catch (PDOException $e) {
        // En caso de error, redirigir a página de error con mensaje
        $error_message = "Error al crear la publicación: " . $e->getMessage();
        header('Location: ../error.php?msg=' . urlencode($error_message));
        exit();
    }
    
} else {
    // Si se intenta acceder directamente sin enviar el formulario
    header('Location: ../php/home.php');
    exit();
}
?>
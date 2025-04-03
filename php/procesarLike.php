<?php
// procesarLike.php - Este archivo procesará los likes

session_start();

// Comprobar si el usuario tiene sesión activa
if (!isset($_SESSION['username'])) {
    // Devolver respuesta JSON indicando que el usuario necesita iniciar sesión
    echo json_encode(['success' => false, 'message' => 'Debe iniciar sesión']);
    exit();
}

// Comprobar si se recibió el ID de la publicación
if (!isset($_POST['publicacion_id']) || !is_numeric($_POST['publicacion_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de publicación inválido']);
    exit();
}

require '../db.php';

try {
    // Obtener el ID de la publicación y el correo del usuario
    $publicacionId = (int)$_POST['publicacion_id'];
    $username = $_SESSION['username'];
    
    // Obtener el correo del usuario desde su nombre de usuario
    $userStmt = $db->prepare('SELECT mail FROM usuari WHERE username = ?');
    $userStmt->execute([$username]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userData) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit();
    }
    
    $userMail = $userData['mail'];
    
    // Comenzar transacción para garantizar integridad de datos
    $db->beginTransaction();
    
    // Verificar si el usuario ya ha dado like a esta publicación
    $checkStmt = $db->prepare('SELECT idReaccio FROM reaccio WHERE mail = ? AND idPubli = ?');
    $checkStmt->execute([$userMail, $publicacionId]);
    
    if ($checkStmt->rowCount() > 0) {
        // El usuario ya dio like, eliminar la reacción (quitar like)
        $deleteStmt = $db->prepare('DELETE FROM reaccio WHERE mail = ? AND idPubli = ?');
        $deleteStmt->execute([$userMail, $publicacionId]);
        
        // Decrementar el contador de likes en la publicación
        $updateStmt = $db->prepare('UPDATE publicacio SET likesPubli = likesPubli - 1 WHERE id = ? AND likesPubli > 0');
        $updateStmt->execute([$publicacionId]);
        
        $db->commit();
        
        // Obtener el número actualizado de likes
        $likeStmt = $db->prepare('SELECT likesPubli FROM publicacio WHERE id = ?');
        $likeStmt->execute([$publicacionId]);
        $likes = $likeStmt->fetch(PDO::FETCH_ASSOC)['likesPubli'];
        
        echo json_encode(['success' => true, 'action' => 'removed', 'likes' => $likes]);
    } else {
        // El usuario no ha dado like, registrar la reacción
        $insertStmt = $db->prepare('INSERT INTO reaccio (mail, idPubli) VALUES (?, ?)');
        $insertStmt->execute([$userMail, $publicacionId]);
        
        // Incrementar el contador de likes en la publicación
        $updateStmt = $db->prepare('UPDATE publicacio SET likesPubli = likesPubli + 1 WHERE id = ?');
        $updateStmt->execute([$publicacionId]);
        
        $db->commit();
        
        // Obtener el número actualizado de likes
        $likeStmt = $db->prepare('SELECT likesPubli FROM publicacio WHERE id = ?');
        $likeStmt->execute([$publicacionId]);
        $likes = $likeStmt->fetch(PDO::FETCH_ASSOC)['likesPubli'];
        
        echo json_encode(['success' => true, 'action' => 'added', 'likes' => $likes]);
    }
} catch (PDOException $e) {
    // Si hay un error, revertir la transacción
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
} finally {
    // Cerrar la conexión
    $db = null;
}
?>
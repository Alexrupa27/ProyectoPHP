<?php
session_start();

// Comprobar si el usuario está conectado
if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Usuario no autorizado']);
    exit();
}

// Comprobar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Comprobar datos requeridos
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['post_id']) || !isset($data['comment_content']) || empty($data['comment_content'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

require '../db.php';

try {
    // Obtener el mail del usuario actual
    $username = $_SESSION['username'];
    $userStmt = $db->prepare('SELECT mail, fotoPerfil FROM usuari WHERE username = ?');
    $userStmt->execute([$username]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$userData) {
        throw new Exception('Usuario no encontrado');
    }
    
    $userMail = $userData['mail'];
    $userPic = $userData['fotoPerfil'];
    
    // Preparar foto de perfil
    $profilePic = '../img/default.png'; // Foto por defecto
    if (!empty($userPic)) {
        if (file_exists($userPic)) {
            $profilePic = $userPic;
        } else {
            try {
                $profilePic = 'data:image/jpeg;base64,' . base64_encode($userPic);
            } catch (Exception $e) {
                $profilePic = '../img/default.png';
            }
        }
    }
    
    // Insertar el comentario
    $commentStmt = $db->prepare('INSERT INTO comentari (idPublicacio, mail, contingut) VALUES (?, ?, ?)');
    $commentStmt->execute([$data['post_id'], $userMail, $data['comment_content']]);
    
    $commentId = $db->lastInsertId();
    $currentDate = date('Y-m-d H:i:s');
    
    // Devolver respuesta con los datos del comentario para actualizar la UI
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'comment' => [
            'id' => $commentId,
            'username' => $username,
            'mail' => $userMail,
            'content' => $data['comment_content'],
            'date' => date('d/m/Y H:i'),
            'profilePic' => $profilePic
        ]
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
<?php 
// http://localhost/ProyectoPHP/mailCheckAccount.php?code=cbd5d2dbfb8e86c36f2b9b704051c62731ff44b1378f97e1ef7b1f822d86565f&mail=alex.ruizp@educem.net

$code = $_GET['code'];
$mail = $_GET['mail'];

require 'db.php';

$checkEmail = $db->prepare("SELECT activationCode FROM usuari WHERE mail = :mail");
$checkEmail->bindParam(':mail', $mail);
$checkEmail->execute();
$result = $checkEmail->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "<br>Código encontrado: " . $result;
} else {
    echo "<br>Correo no encontrado.";
}

echo "Ha ejecutado" . $code ."  -  ". $mail;
echo $checkEmail;
?>
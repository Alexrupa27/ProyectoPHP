<?php
$cadena_connexio = 'mysql:dbname=pistachad;host=localhost:3335';
$usuari = 'root';
$passwd = '';

try {
    // Ens connectem a la BDs
    $db = new PDO($cadena_connexio, $usuari, $passwd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Mostrar errores de PDO
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Modo de fetch por defecto
    ]);


} catch (PDOException $e) {
    echo 'Error amb la BDs: ' . $e->getMessage() . '<br>';
}
?>
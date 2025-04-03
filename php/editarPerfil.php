<?php
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanear datos de entrada
    $age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
    $ubicacion = filter_input(INPUT_POST, 'ubicacion', FILTER_SANITIZE_STRING);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
    
    session_start();
    $id = $_SESSION["idUser"];

    // Obtener el email del usuario
    $stmt = $db->prepare("SELECT * FROM usuari WHERE idUser = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $email = $user['mail'];

    // Variable para almacenar ruta de imagen
    $fotoPerfilPath = $user['fotoPerfil']; // Mantener la imagen anterior por defecto

    // Si se subió una nueva foto de perfil
    if (!empty($_FILES['fotoPerfil']['name'])) {
        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['fotoPerfil']['type'], $allowedTypes)) {
            // Generar nombre único para evitar sobreescrituras
            $targetDir = "../img/";
            $fileName = time() . '_' . basename($_FILES["fotoPerfil"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            
            // Verificar tamaño (por ejemplo, máximo 5MB)
            if ($_FILES['fotoPerfil']['size'] <= 5000000) {
                if (move_uploaded_file($_FILES["fotoPerfil"]["tmp_name"], $targetFilePath)) {
                    $fotoPerfilPath = $targetFilePath;
                }
            }
        }
    }

    // Actualizar los datos del usuario
    $stmt = $db->prepare("UPDATE usuari SET edat = :edad, ubi = :ubicacion, descripcio = :descrp, fotoPerfil = :fotoPerfil WHERE mail = :email");
    $stmt->execute([
        ':edad' => $age,
        ':ubicacion' => $ubicacion,
        ':descrp' => $bio,
        ':fotoPerfil' => $fotoPerfilPath,
        ':email' => $email
    ]);
    
    // Redireccionar a alguna página o mostrar mensaje de éxito
    header("Location: perfil.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modificar Perfil</title>
  <link rel="stylesheet" href="../css/editarPerfil.css">
  <link rel="shortcut icon" href="../img/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<header>
        <img src="../assets/icon.png" alt="Logo PistaChad">
        <nav id="myLinks">
          <ul>
            <a href="./home.php">Inicio</a>
            <a href="./perfil.php">Perfil</a>
            <a href="../Disconnect.php">Desconectar</a>
          </ul>
        </nav>
        <a href="javascript:void(0);" class="icon" onclick="hamburgesa()">
            <i class="fa fa-bars"></i> <!--  libreria font awesome -->
          </a>
    </header>
    <main>
<body>
  <div class="container">
    <h1>Modificar Perfil</h1>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="profile-form" enctype="multipart/form-data">
      <div class="form-group">
        <label for="age">Edad</label>
        <input type="number" id="age" name="age" required placeholder="Tu edad" value="<?php echo isset($user['edat']) ? htmlspecialchars($user['edat']) : ''; ?>">
      </div>
      
      <div class="form-group">
        <label for="ubicacion">Ubicación</label>
        <input type="text" id="ubicacion" name="ubicacion" required placeholder="Tu ubicación" value="<?php echo isset($user['ubi']) ? htmlspecialchars($user['ubi']) : ''; ?>">
      </div>
      
      <div class="form-group">
        <label for="fotoPerfil">Foto de Perfil</label>
        <input type="file" id="fotoPerfil" name="fotoPerfil" accept="image/jpeg,image/png,image/gif">
        <?php if(!empty($user['fotoPerfil'])): ?>
          <p class="current-image">Imagen actual: <?php echo basename($user['fotoPerfil']); ?></p>
        <?php endif; ?>
      </div>
      
      <div class="form-group">
        <label for="bio">Descripción Personal</label>
        <textarea id="bio" name="bio" placeholder="Cuéntanos sobre ti..." rows="4"><?php echo isset($user['descripcio']) ? htmlspecialchars($user['descripcio']) : ''; ?></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="submit-btn">Guardar Cambios</button>
      </div>
    </form>
  </div>
</body>
</main>
</html>
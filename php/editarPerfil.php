<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modificar Perfil</title>
  <link rel="stylesheet" href="../css/editarPerfil.css">
  <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

  <div class="container">
    <h1>Modificar Perfil</h1>
    <form action="#" method="post" class="profile-form">
      <div class="form-group">
        <label for="username">Nombre de Usuario</label>
        <input type="text" id="username" name="username" required placeholder="Tu nombre">
      </div>
      
      <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" required placeholder="tuemail@dominio.com">
      </div>
      
      <div class="form-group">
        <label for="profile-photo">Foto de Perfil</label>
        <input type="file" id="profile-photo" name="profile-photo" accept="image/*">
      </div>
      
      <div class="form-group">
        <label for="bio">Descripción Personal</label>
        <textarea id="bio" name="bio" placeholder="Cuéntanos sobre ti..." rows="4"></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="submit-btn">Guardar Cambios</button>
      </div>
    </form>
  </div>

</body>
</html>
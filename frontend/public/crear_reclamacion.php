<?php

  require_once '../../backend/auth/auth_check.php';
  require_once '../../backend/config/database.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $descripcion = $_POST['descripcion'] ?? '';

    $descripcion = trim($descripcion);

    if (empty($descripcion)) {
      $error = "Todos los campos son obligatorios";
    } else {

      $pdo = Database::getConnection();

      $usuario_id = $_SESSION['usuario_id'];

      $stmt = $pdo -> prepare("
        INSERT INTO reclamaciones (usuario_id, descripcion)
        VALUES (:usuario_id, :descripcion)
      ");

      $stmt -> execute([
        ':usuario_id' => $usuario_id,
        ':descripcion' => $descripcion
      ]);

      header("location: dashboard.php");
      exit;
    }
  }
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro reclamaciones</title>

    <link rel="stylesheet" href="../assets/css/styles.css">
  </head>

  <body>
    <form id="form_registro" method="POST" action="crear_reclamacion.php" class="form-registro">
      <fieldset>
        <legend>Registro de reclamaciones</legend>

        <?php if (!empty($error)): ?>
          <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <label for="descripcion">Descripción: </label>
        <textarea id="descripcion" name="descripcion" placeholder="Descripción de la reclamación a registrar" required></textarea>
      </fieldset>

      <br>

      <button type="submit" id="btn_registrar" class="btn">Registrar</button>
    </form>
  </body>
</html>
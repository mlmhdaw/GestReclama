<?php

  require_once '../../backend/auth/auth_check.php';
  require_once '../../backend/config/database.php';

  $pdo = Database::getConnection();
  
  $stmt_franq = $pdo -> query("SELECT id, nombre FROM franquicias WHERE activo = 1 ORDER BY nombre ASC");
  $franquicias = $stmt_franq -> fetchAll();
  $franquicia_id = $_POST['franquicia_id'] ?? '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $descripcion = $_POST['descripcion'] ?? '';
    $descripcion = trim($descripcion);

    if (empty($descripcion) || empty($franquicia_id)) {
      $error = "Todos los campos son obligatorios";
    } else {

      $usuario_id = $_SESSION['usuario_id'];

      $stmt = $pdo -> prepare("
        INSERT INTO reclamaciones (usuario_id, descripcion, franquicia_id)
        VALUES (:usuario_id, :descripcion, :franquicia_id)
      ");

      $stmt -> execute([
        ':usuario_id'  => $usuario_id,
        ':descripcion' => $descripcion,
        ':franquicia_id'  => $franquicia_id
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

        <br> <br>

        <label for="franquicia_id">Franquicia de registro: </label>
        <select id="franquicia_id" name="franquicia_id">
          <option value="">Seleccione franquicia</option>
          <?php foreach ($franquicias as $franq): ?>
            <option value="<?= $franq['id'] ?>"
              <?= ($franquicia_id == $franq['id']) ? 'selected' : '' ?>>
              <?= $franq['id'] . "  ---  " . $franq['nombre']?>
            </option>
          <?php endforeach; ?>
        </select>
      </fieldset>

      <br>

      <button type="submit" id="btn_registrar" class="btn">Registrar</button>
      <a href='dashboard.php'>Volver a panel principal</a>
    </form>
  </body>
</html>
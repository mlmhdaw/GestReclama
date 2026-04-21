<?php

  require_once __DIR__ . '/../../backend/auth/auth_check.php';
  require_once __DIR__ . '/../../backend/config/database.php';
  require_once __DIR__ . '/../../backend/helpers/validator.php';
  require_once __DIR__ . '/../config/app.php';

  $tituloVista = "Registrar Reclamación";

  $error = '';
  $descripcion = '';

  $pdo = Database::getConnection();
  
  $stmt_franq = $pdo -> query("SELECT id, nombre FROM franquicias WHERE activo = 1 ORDER BY nombre ASC");
  $franquicias = $stmt_franq -> fetchAll();
  $franquicia_id = limpiarTexto($_POST['franquicia_id'] ?? '');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $descripcion = limpiarTexto($_POST['descripcion'] ?? '');

    if (
      estaVacio($descripcion) || 
      estaVacio($franquicia_id) ||
      !validarId($franquicia_id) ||
      !existeEnBD($pdo, 'franquicias', 'id', $franquicia_id)
    ) {
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

      header("location: /");
      exit;
    }
  }

  ob_start();
?>

<form id="form_registro" method="POST" action="/crear_reclamacion.php" class="form-registro">
  <fieldset>
    <legend>Registro de reclamaciones</legend>

    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <label for="descripcion">Descripción: </label>
    <textarea id="descripcion" name="descripcion" placeholder="Descripción de la reclamación a registrar" required>
      <?= htmlspecialchars($descripcion ?? '') ?>
    </textarea>

    <br> <br>

    <label for="franquicia_id">Franquicia de registro: </label>
    <select id="franquicia_id" name="franquicia_id">
      <option value="">Seleccione franquicia</option>
      <?php foreach ($franquicias as $franq): ?>
        <option value="<?= $franq['id'] ?>"
          <?= ($franquicia_id == $franq['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($franq['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </fieldset>

  <br>

  <button type="submit" id="btn_registrar" class="btn">Registrar</button>
</form>

<?php
  $contenido = ob_get_clean();
  require_once __DIR__ . '/../layout/layout.php';
?>
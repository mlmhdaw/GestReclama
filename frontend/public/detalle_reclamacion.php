<?php
  
  require_once __DIR__ . '/../../backend/auth/auth_check.php';
  require_once __DIR__ . '/../../backend/config/database.php';
  require_once __DIR__ . '/../../backend/helpers/validator.php';  
  require_once __DIR__ . '/../config/app.php';

  $tituloVista = "Seguimiento de reclamación";

  $error = '';
  
  $pdo = Database::getConnection();

  $reclamacion_id = limpiarTexto($_GET['id'] ?? '');

  $stmt_estados = $pdo -> query("SELECT id, nombre FROM estados ORDER BY id ASC");
  $estados      = $stmt_estados -> fetchAll();

  if (
    estaVacio($reclamacion_id) || 
    !validarId($reclamacion_id)
  ) {
    header("Location: /listar_reclamaciones.php");
    exit;
  }

  $usuario_id = $_SESSION['usuario_id'];

  $params_reclamacion = [
    'id'         => $reclamacion_id,
    'usuario_id' => $usuario_id
  ];

  $params_acciones = [
    'reclamacion_id' => $reclamacion_id,
    'usuario_id'     => $usuario_id
  ];

  $estado_seleccionado  = limpiarTexto($_POST['estado_id'] ?? '');
  $comentario_insertado = limpiarTexto($_POST['comentario'] ?? '');

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado_id  = $estado_seleccionado;
    $comentario = $comentario_insertado;

    if (
      estaVacio($estado_id) || 
      !validarId($estado_id) ||
      !existeEnBD($pdo, 'estados', 'id', $estado_id) || 
      estaVacio($comentario)
    ) {
      $error = "Todos los campos son obligatorios";
    } else {

      $stmt_check = $pdo->prepare("
        SELECT id
        FROM reclamaciones
        WHERE id = :id AND usuario_id = :usuario_id
      ");

      $stmt_check->execute([
        'id' => $reclamacion_id,
        'usuario_id' => $usuario_id
      ]);

      if (!$stmt_check->fetch()) {
        header("Location: /");
        exit;
      }

      $stmt = $pdo -> prepare("
        INSERT INTO acciones_reclamacion (
          reclamacion_id, 
          usuario_id,
          estado_id,
          comentario
        )
        VALUES (
          :reclamacion_id,
          :usuario_id,
          :estado_id,
          :comentario
        )
      ");

      $stmt -> execute([
        'reclamacion_id' => $reclamacion_id,  
        'usuario_id'     => $usuario_id,
        'estado_id'      => $estado_id,
        'comentario'     => $comentario
      ]);

      $stmt_update = $pdo -> prepare("
        UPDATE reclamaciones
        SET estado_id = :estado_id
        WHERE id      = :reclamacion_id
      ");

      $stmt_update -> execute([
        'estado_id'      => $estado_id,
        'reclamacion_id' => $reclamacion_id
      ]);

      header("Location: /detalle_reclamacion.php?id=" . $reclamacion_id);
      exit;
    }
  }

  $consulta_reclamacion =
    "SELECT 
      r.id, 
      r.descripcion, 
      r.fecha,
      r.tipo,
      f.nombre AS f_nombre,
      e.nombre AS e_nombre
    FROM reclamaciones r
    JOIN franquicias f ON f.id = r.franquicia_id
    JOIN estados e     ON e.id = r.estado_id
    WHERE
      r.id = :id AND 
      r.usuario_id = :usuario_id AND
      r.activo = 1
    LIMIT 1
  ";

  $stmt = $pdo -> prepare($consulta_reclamacion);
  $stmt -> execute ($params_reclamacion);
  $reclamacion = $stmt -> fetch(PDO::FETCH_ASSOC);

  if (!$reclamacion) {
    header("Location: /");
    exit;
  }

  $consulta_acciones = 
    "SELECT
      a.id,
      a.comentario,
      a.fecha,
      est.nombre AS est_nombre
    FROM acciones_reclamacion a
    JOIN estados est       ON est.id = a.estado_id
    JOIN reclamaciones rec ON rec.id = a.reclamacion_id
    WHERE
      a.reclamacion_id = :reclamacion_id AND
      rec.usuario_id = :usuario_id
    ORDER BY a.fecha DESC
  ";

  $stmt = $pdo -> prepare($consulta_acciones);
  $stmt -> execute ($params_acciones);
  $acciones = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  ob_start();

?>

<fieldset>
  <legend>Detalles de la reclamación</legend>
      
  <p><strong>ID: </strong> <?= htmlspecialchars($reclamacion['id']) ?></p>
  <p><strong>Fecha registro: </strong> <?= htmlspecialchars($reclamacion['fecha']) ?></p>
  <p><strong>Tipo: </strong> <?= htmlspecialchars($reclamacion['tipo']) ?></p>
  <p><strong>Franquicia: </strong> <?= htmlspecialchars($reclamacion['f_nombre']) ?></p>
  <p><strong>Estado actual: </strong> <?= htmlspecialchars($reclamacion['e_nombre']) ?></p>
  <p><strong>Descripción: </strong> <?= htmlspecialchars($reclamacion['descripcion']) ?></p>
</fieldset>

<fieldset>
  <legend>Historial de acciones</legend>
      
  <table>
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Comentario</th>
        <th>Detalle</th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($acciones)): ?>
        <?php foreach ($acciones as $acc): ?>
          <tr>
            <td><?= htmlspecialchars($acc['fecha']) ?></td>
            <td><?= htmlspecialchars($acc['est_nombre']) ?></td>
            <td><?= htmlspecialchars($acc['comentario']) ?></td>
            <td>
              <a href="/detalle_accion.php?id=<?= htmlspecialchars($acc['id']) ?>">
                Ver detalle
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4">No hay acciones para esta reclamación</td>
          </tr>
        <?php endif; ?>
    </tbody>
  </table>

</fieldset>

<?php if (!empty($error)): ?>
  <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/detalle_reclamacion.php?id=<?= htmlspecialchars($reclamacion_id) ?>">
  <fieldset>
    <legend>Registrar nueva acción y comentario</legend>
        
    <label for="estado_id">Nuevo estado: </label>
    <select id="estado_id" name="estado_id">
      <option value="">Seleccione estado</option>
      <?php foreach ($estados as $est): ?>
        <option value="<?= $est['id'] ?>"
          <?= ($estado_seleccionado == $est['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($est['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <br> <br>

    <label for="comentario">Comentario: </label>
    <textarea
      id="comentario" 
      name="comentario" 
      placeholder="descripción de la acción realizada" 
      required><?= htmlspecialchars($comentario_insertado) ?></textarea>
  </fieldset>
      
  <button type="submit" id="btn_nueva_accion" class="btn">Registrar</button>
</form>

<?php
  $contenido = ob_get_clean();
  require_once __DIR__ . '/../layout/layout.php';
?>
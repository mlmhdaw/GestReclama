<?php

  require_once __DIR__ . '/../../backend/auth/auth_check.php';
  require_once __DIR__ . '/../../backend/config/database.php';
  require_once __DIR__ . '/../../backend/helpers/validator.php';
  require_once __DIR__ . '/../config/app.php';
      
  $tituloVista = "Consultar reclamaciones";

  $error = '';

  $pdo = Database::getConnection();

  $stmt_franquicias  = $pdo -> query("SELECT id, nombre FROM franquicias WHERE activo = 1 ORDER BY nombre ASC");
  $franquicias = $stmt_franquicias -> fetchAll();

  $stmt_estados = $pdo -> query("SELECT id, nombre FROM estados WHERE activo = 1 ORDER BY id ASC");
  $estados = $stmt_estados -> fetchAll();

  $franquicia_id = limpiarTexto($_GET['franquicia_id'] ?? '');
  $estado_id     = limpiarTexto($_GET['estado_id']     ?? '');
  $id            = limpiarTexto($_GET['id']            ?? '');
  $desde         = limpiarTexto($_GET['desde']         ?? '');
  $hasta         = limpiarTexto($_GET['hasta']         ?? '');
  
  $usuario_id = $_SESSION['usuario_id'];

  $condiciones = [];
  $params      = ['usuario_id' => $usuario_id];

  if (
    !estaVacio($franquicia_id) &&
    validarId($franquicia_id)
  ) {
    $condiciones[]        = "r.franquicia_id = :franquicia";
    $params['franquicia'] = $franquicia_id;
  }

  if (
    !estaVacio($estado_id) &&
    validarId($estado_id)
  ) {
    $condiciones[]    = "r.estado_id = :estado";
    $params['estado'] = $estado_id;
  }
  
  if (
    !estaVacio($desde) &&
    strtotime($desde)
  ) {
    $condiciones[]   = "r.fecha >= :desde";
    $params['desde'] = $desde;
  }

  if (
    !estaVacio($hasta) &&
    strtotime($hasta)
  ) {
    $condiciones[]   = "r.fecha <= :hasta";
    $params['hasta'] = $hasta;
  }

  if (
    !estaVacio($id) && 
    validarId($id)
  ) {
    $condiciones[] = "r.id = :id";
    $params['id']  = $id;
  }
 
  $consulta = "SELECT
                r.id,
                r.descripcion,
                r.fecha,
                f.nombre AS f_nombre,
                e.nombre AS e_nombre
               FROM reclamaciones r
               JOIN franquicias f ON r.franquicia_id = f.id
               JOIN estados e ON r.estado_id = e.id
               WHERE r.usuario_id = :usuario_id AND r.activo = 1";
               

  if (!empty($condiciones)) {
    $filtros = implode(" AND ", $condiciones);
    $consulta .= " AND " . $filtros;
    $consulta .= " ORDER BY r.fecha DESC LIMIT 30";
  }
  
  $stmt          = $pdo -> prepare($consulta);
  $stmt          -> execute ($params);
  $reclamaciones = $stmt -> fetchAll();

  ob_start();

?>

<form id="form_filtros" method="GET" action="/listar_reclamaciones.php" class="form-filtros">
  <fieldset>
    <legend>Filtros de búsqueda de reclamaciones</legend>

    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <label for="franquicia_id">Franquicia: </label>
    <select id="franquicia_id" name="franquicia_id">
      <option value="">Seleccione franquicia</option>
      <?php foreach ($franquicias as $franq): ?>
        <option value="<?= $franq['id'] ?>"
          <?= ($franquicia_id == $franq['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($franq['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label for="estado_id">Estado: </label>
    <select id="estado_id" name="estado_id">
      <option value="">Seleccione estado</option>
      <?php foreach ($estados as $est): ?>
        <option value="<?= $est['id'] ?>"
          <?= ($estado_id == $est['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($est['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
        
    <br> <br>

    <label for="desde">Desde: </label>
    <input type="date" id="desde" name="desde" value="<?= htmlspecialchars($desde) ?>">

    <label for="hasta">Hasta: </label>
    <input type="date" id="hasta" name="hasta" value="<?= htmlspecialchars($hasta) ?>">
      
    <br> <br>
        
    <label for="id">Id reclamación: </label>
    <input type="text" id="id" name="id" value="<?= htmlspecialchars($id) ?>">

  </fieldset>

  <br>

  <button type="submit" id="btn_filtrar" class="btn">Filtrar</button>
</form>

<div>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Franquicia</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Descripción</th>
        <th>Opción</th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($reclamaciones)): ?>
        <?php foreach ($reclamaciones as $reclam): ?>
          <tr>
            <td><?= htmlspecialchars($reclam['id']) ?></td>
            <td><?= htmlspecialchars($reclam['f_nombre']) ?></td>
            <td><?= htmlspecialchars($reclam['fecha']) ?></td>
            <td><?= htmlspecialchars($reclam['e_nombre']) ?></td>
            <td><?= htmlspecialchars($reclam['descripcion']) ?></td>
            <td>
              <a href="/detalle_reclamacion.php?id=<?= htmlspecialchars($reclam['id']) ?>">
                Ver detalle
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="6">No hay reclamaciones para este usuario</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<br>

<?php
  $contenido = ob_get_clean();
  require_once __DIR__ . '/../layout/layout.php';
?>
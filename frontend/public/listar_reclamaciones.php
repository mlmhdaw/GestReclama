<?php

  require_once '../../backend/auth/auth_check.php';
  require_once '../../backend/config/database.php';
      
  $pdo = Database::getConnection();

  $stmt_franquicias  = $pdo -> query("SELECT id, nombre FROM franquicias WHERE activo = 1 ORDER BY nombre ASC");
  $franquicias = $stmt_franquicias -> fetchAll();

  $stmt_estados = $pdo -> query("SELECT id, nombre FROM estados WHERE activo = 1 ORDER BY id ASC");
  $estados = $stmt_estados -> fetchAll();

  $franquicia_id = $_GET['franquicia_id'] ?? '';
  $estado_id     = $_GET['estado_id']     ?? '';
  $desde         = $_GET['desde']         ?? '';
  $hasta         = $_GET['hasta']         ?? '';
  $id            = $_GET['id']            ?? '';

  $usuario_id = $_SESSION['usuario_id'];

  $condiciones = [];
  $params      = ['usuario_id' => $usuario_id];

  if (!empty($franquicia_id)) {
    $condiciones[]        = "r.franquicia_id = :franquicia";
    $params['franquicia'] = $franquicia_id;
  }

  if (!empty($estado_id)) {
    $condiciones[]    = "r.estado_id = :estado";
    $params['estado'] = $estado_id;
  }
  
  if (!empty($desde)) {
    $condiciones[]   = "r.fecha >= :desde";
    $params['desde'] = $desde;
  }

  if (!empty($hasta)) {
    $condiciones[]   = "r.fecha <= :hasta";
    $params['hasta'] = $hasta;
  }

  if (!empty($id) && is_numeric($id)) {
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
  }
  
  $stmt          = $pdo -> prepare($consulta);
  $stmt          -> execute ($params);
  $reclamaciones = $stmt -> fetchAll(); 

?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de reclamaciones</title>

    <link rel="stylesheet" href="../assets/css/styles.css">
  </head>

  <body>

    <form id="form_filtros" method="GET" action="listar_reclamaciones.php" class="form-filtros">
      <fieldset>
        <legend>Filtros de búsqueda de reclamaciones</legend>

        <?php if (!empty($error)): ?>
          <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <label for="franquicia_id">Franquicia: </label>
        <select id="franquicia_id" name="franquicia_id">
          <option value="">Seleccione franquicia</option>
          <?php foreach ($franquicias as $franq): ?>
            <option value="<?= $franq['id'] ?>"
              <?= ($franquicia_id == $franq['id']) ? 'selected' : '' ?>>
              <?= $franq['id'] . "  ---  " . $franq['nombre']?>
            </option>
          <?php endforeach; ?>
        </select>

        <label for="estado_id">Estado: </label>
        <select id="estado_id" name="estado_id">
          <option value="">Seleccione estado</option>
          <?php foreach ($estados as $est): ?>
            <option value="<?= $est['id'] ?>"
              <?= ($estado_id == $est['id']) ? 'selected' : '' ?>>
              <?= $est['id'] . "  ---  " . $est['nombre']?>
            </option>
          <?php endforeach; ?>
        </select>
        
        <br> <br>

        <label for="desde">Desde: </label>
        <input type="date" id="desde" name="desde" value="<?= $desde ?>">

        <label for="hasta">Hasta: </label>
        <input type="date" id="hasta" name="hasta" value="<?= $hasta ?>">
      
        <br> <br>
        
        <label for="id">Id reclamación: </label>
        <input type="text" id="id" name="id" value="<?= $id ?>">

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
                <td><?= $reclam['id'] ?></td>
                <td><?= $reclam['f_nombre'] ?></td>
                <td><?= $reclam['fecha'] ?></td>
                <td><?= $reclam['e_nombre'] ?></td>
                <td><?= $reclam['descripcion'] ?></td>
                <td>
                  <a href="detalle_reclamacion.php?id=<?=$reclam['id'] ?>">
                  Ver detalle
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

    <a href='dashboard.php'>Volver a panel principal</a>
  </body>
</html>
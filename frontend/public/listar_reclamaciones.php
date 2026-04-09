<?php

  require_once '../../backend/auth/auth_check.php';
  require_once '../../backend/config/database.php';
      
  $desde = $_GET['desde'] ?? '';
  $hasta = $_GET['hasta'] ?? '';
  $id    = $_GET['id']    ?? '';

  $pdo        = Database::getConnection();
  $usuario_id = $_SESSION['usuario_id'];

  $condiciones = [];
  $params      = ['usuario_id' => $usuario_id];

  if (!empty($desde)) {
    $condiciones[]   = "fecha >= :desde";
    $params['desde'] = $desde;
  }

  if (!empty($hasta)) {
    $condiciones[]   = "fecha <= :hasta";
    $params['hasta'] = $hasta;
  }

  if (!empty($id) && is_numeric($id)) {
    $condiciones[] = "id = :id";
    $params['id']  = $id;
  }
 
  $consulta = "SELECT r.id, r.descripcion, r.fecha 
               FROM reclamaciones r
               WHERE usuario_id = :usuario_id";

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
            <th>Descripción</th>
            <th>Fecha</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($reclamaciones)): ?>
            <?php foreach ($reclamaciones as $reclam): ?>
              <tr>
                <td><?= $reclam['id'] ?></td>
                <td><?= $reclam['descripcion'] ?></td>
                <td><?= $reclam['fecha'] ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3">No hay reclamaciones para este usuario</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <br>

    <a href='dashboard.php'>Volver a panel principal</a>
  </body>
</html>
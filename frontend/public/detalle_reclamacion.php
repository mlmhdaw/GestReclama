<?php
  
  require_once '../../backend/auth/auth_check.php';
  require_once '../../backend/config/database.php';

  $pdo = Database::getConnection();

  $reclamacion_id = $_GET['id'] ?? '';

  $stmt_estados = $pdo -> query("SELECT id, nombre FROM estados ORDER BY id ASC");
  $estados = $stmt_estados -> fetchAll();

  if (empty($reclamacion_id) || !is_numeric($reclamacion_id)) {
    header("Location: listar_reclamaciones.php");
    exit;
  }

  $usuario_id = $_SESSION['usuario_id'];

  $params = [
    'id' => $reclamacion_id,
    'usuario_id' => $usuario_id
  ];

  $estado_seleccionado   = $_POST['estado_id'] ?? '';
  $comentario_insertado = $_POST['comentario'] ?? '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado_id = $estado_seleccionado;
    $comentario = trim($comentario_insertado);

    if (empty($estado_id) || !is_numeric($estado_id) || empty($comentario)) {
      $error = "Todos los campos son obligatorios";
    } else {

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
        WHERE id = :reclamacion_id
      ");

      $stmt_update -> execute([
        'estado_id' => $estado_id,
        'reclamacion_id' => $reclamacion_id
      ]);

      header("Location: detalle_reclamacion.php?id=" . $reclamacion_id);
      exit;
    }
  }

  $consulta = "SELECT 
                  r.id, 
                  r.descripcion, 
                  r.fecha,
                  r.tipo,
                  f.nombre AS f_nombre,
                  e.nombre AS e_nombre
                FROM reclamaciones r
                JOIN franquicias f ON f.id = r.franquicia_id
                JOIN estados e ON e.id = r.estado_id
                WHERE r.id = :id AND 
                      r.usuario_id = :usuario_id AND
                      r.activo = 1
                LIMIT 1
  ";

  $stmt = $pdo -> prepare($consulta);
  $stmt -> execute ($params);
  $reclamacion = $stmt -> fetch(PDO::FETCH_ASSOC);

  if (!$reclamacion) {
    header("Location: listar_reclamaciones.php");
    exit;
  }

?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de reclamación</title>

    <link rel="stylesheet" href="../assets/css/styles.css">
  </head>

  <body>
    <fieldset>
      <legend>Detalles de la reclamación</legend>
      
      <p><strong>ID: </strong> <?= $reclamacion['id'] ?></p>
      <p><strong>Fecha registro: </strong> <?= $reclamacion['fecha'] ?></p>
      <p><strong>Tipo: </strong> <?= $reclamacion['tipo'] ?></p>
      <p><strong>Franquicia: </strong> <?= $reclamacion['f_nombre'] ?></p>
      <p><strong>Estado actual: </strong> <?= $reclamacion['e_nombre'] ?></p>
      <p><strong>Descripción: </strong> <?= $reclamacion['descripcion'] ?></p>
    </fieldset>

    <form method="POST" action="detalle_reclamacion.php?id=<?= $reclamacion_id ?>">
      <fieldset>
        <legend>Registrar nueva acción y comentario</legend>
        
        <label for="estado_id">Nuevo estado: </label>
        <select id="estado_id" name="estado_id">
          <option value="">Seleccione estado</option>
          <?php foreach ($estados as $est): ?>
            <option value="<?= $est['id'] ?>"
              <?= ($estado_seleccionado == $est['id']) ? 'selected' : '' ?>>
              <?= $est['id'] . "  ---  " . $est['nombre']?>
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

    <a href="listar_reclamaciones.php">Volver al listado</a>
  </body>
</html>
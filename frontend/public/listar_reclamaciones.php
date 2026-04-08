<?php

  require_once '../../backend/auth/auth_check.php';
  require_once '../../backend/config/database.php';
  
  $pdo = Database::getConnection();

  $usuario_id = $_SESSION['usuario_id'];

  $stmt = $pdo -> prepare("SELECT id, descripcion, fecha FROM reclamaciones WHERE usuario_id = :usuario_id");

  $stmt -> execute (['usuario_id' => $usuario_id]);

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
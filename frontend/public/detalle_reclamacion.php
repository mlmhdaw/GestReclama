<?php
  
  require_once '../../backend/auth/auth_check.php';
  require_once '../../backend/config/database.php';

  $pdo = Database::getConnection();

  $id = $_GET['id'] ?? '';

  if (empty($id) || !is_numeric($id)) {
    header("Location: listar_reclamaciones.php");
    exit;
  }

  $usuario_id = $_SESSION['usuario_id'];

  $params = [
    'id' => $id,
    'usuario_id' => $usuario_id
  ];

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
  </body>
</html>
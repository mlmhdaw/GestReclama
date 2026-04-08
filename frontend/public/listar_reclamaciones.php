<?php

  $pdo = Database::getConnection();

  $usuario_id = $_SESSION['usuario_id'];

  $stmt = $pdo -> prepare("SELECT id, descripcion, fecha FROM reclamaciones WHERE usuario_id = :usuario_id");

  $stmt -> execute (['usuario_id' => $usuario_id]);

  $reclamaciones = $stmt -> fetchall();

?>
<?php
  require_once '../../backend/auth/auth_check.php';
  require_once '../config/app.php';

  $tituloVista = "Panel principal";
  $nombreUsuario = "Usuario";
  $rolUsuario = "Rol";

  ob_start();

?>

<h1>Panel principal</h1>

<?php
  $contenido = ob_get_clean();

  require_once '../layout/layout.php';
?>
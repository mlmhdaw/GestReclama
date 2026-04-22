<?php
  require_once '../../backend/auth/auth_check.php';
  require_once '../config/app.php';

  $tituloVista   = "Panel principal";

  ob_start();

?>

<div class="dashboard">
  <div class="card">
    <p>Reclamaciones pendientes</p>
    <span>0</span>
  </div>
  
  <div class="card">
    <p>Notificaciones pendientes</p>
    <span>0</span>
  </div>
</div>

<?php
  $contenido = ob_get_clean();
  require_once '../layout/layout.php';
?>
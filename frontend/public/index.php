<?php
  require_once '../../backend/auth/auth_check.php';
  require_once '../config/app.php';

  $tituloVista   = "Panel principal";
  $menuActivo    = 'panel';

  ob_start();
?>

<div class="layout-grid">
  <div class="card card--dashboard">
    <p>Reclamaciones pendientes</p>
    <span>0</span>
  </div>
  
  <div class="card card--dashboard">
    <p>Notificaciones pendientes</p>
    <span>0</span>
  </div>
</div>

<?php
  $contenido = ob_get_clean();
  require_once '../layout/layout.php';
?>
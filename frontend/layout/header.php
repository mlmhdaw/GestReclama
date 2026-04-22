<header class="header">
  <div class="header-left">
    <img src="assets/img/logo.svg" alt="GestReclama">
    <span>GestReclama</span>
  </div>

  <?php  
    $nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';
    $rolUsuario    = $_SESSION['rol']   ?? 'Rol';
  ?>

  <div class="header-center">
    <?php if (isset($nombreUsuario) && isset($rolUsuario)) : ?>
      <span>Bienvenido/a: <?= htmlspecialchars($nombreUsuario) ?></span>
      <span>Rol: <?= htmlspecialchars($rolUsuario) ?></span>
    <?php endif; ?>
  </div>

  <div class="header-right">
    <?= $tituloVista ?>
  </div>
</header>
<header class="header">
  <div class="header-left">
    <img src="assets/img/logo.svg" alt="GestReclama">
    <span>GestReclama</span>
  </div>

  <div class="header-center">
    <?php if (isset($nombreUsuario) && isset($rolUsuario)) : ?>
      <span>Bienvenido/a: <?= $nombreUsuario ?></span>
      <span>Rol: <?= $rolUsuario ?></span>
    <?php endif; ?>
  </div>

  <div class="header-right">
    <?= $tituloVista ?>
  </div>
</header>
<header class="header">
  <div class="header__left">
    <img src="<?= $baseUrl ?>/assets/img/logo.svg" alt="GestReclama">
    <span>GestReclama</span>
  </div>

  <?php if (($layoutMode ?? 'auth') === 'app') : ?>
    <?php  
      $nombreUsuario = $_SESSION['nombre'] ?? '';
      $rolUsuario    = $_SESSION['rol']   ?? '';
    ?>
    
    <div class="header__center">
      <span>Bienvenido/a: <?= htmlspecialchars($nombreUsuario) ?></span>
      <span>Rol: <?= htmlspecialchars($rolUsuario) ?></span>
    </div>
  <?php endif; ?>

  <div class="header__right">
    <?= $tituloVista ?>
  </div>
</header>
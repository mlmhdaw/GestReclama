<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title><?= $tituloVista ?></title>

    <!-- BASE -->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/base.css">

    <!-- LAYOUT -->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/layout.css">

    <!-- COMPONENTS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/components.css">
  </head>

  <body class="<?= ($layoutMode ?? 'app') === 'auth' ? 'layout--auth' : '' ?>">

    <?php require_once __DIR__ . '/header.php'; ?>

    <div class="layout">
      <?php if (($layoutMode ?? 'app') === 'app') : ?>
        <?php require_once __DIR__ . '/sidebar.php'; ?>
      <?php endif; ?>

      <main class="content">
        <div class="content-wrapper">
          <?= $contenido ?>
        </div>
      </main>
    </div>

  </body>
</html>
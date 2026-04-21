<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <title><?= $tituloVista ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/styles.css">
  </head>

  <body>
    <?php require_once __DIR__ . '/header.php'; ?>
    <div class="layout">
      <?php if (!isset($sinSidebar)) : ?>
          <?php require_once __DIR__ . '/sidebar.php'?>
      <?php endif; ?>

      <main class="content">
        <div class="content-wrapper">
          <?= $contenido ?>
        </div>
      </main>
    </div>

  </body>
</html>
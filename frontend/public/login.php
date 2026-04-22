<?php

  session_start();

  require_once __DIR__ . '/../../backend/config/database.php';
  require_once __DIR__ . '/../../backend/helpers/validator.php';
  require_once __DIR__ . '/../config/app.php';
  
  $tituloVista = "Login";
  $layoutMode  = 'auth';

  $error = '';
  $email = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = strtolower(limpiarTexto($_POST['email']));
    $password = limpiarTexto($_POST['password'] ?? '');
    
    if (
      estaVacio($email) || 
      estaVacio($password) 
    ) {
      $error = "Todos los campos son obligatorios";
    } else {

      $pdo = Database::getConnection();

      $stmt = $pdo->prepare(
        "SELECT id, email, password, rol, nombre
         FROM usuarios 
         WHERE email = :email 
         LIMIT 1"
      );
      $stmt->execute(['email' => $email]);
      $usuario = $stmt->fetch();

      if (!$usuario || (!password_verify($password, $usuario['password']))) {
        $error =  "Credenciales incorrectas...";
      } else {
        
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['email']      = $usuario['email'];
        $_SESSION['nombre']     = $usuario['nombre'];
        $_SESSION['rol']        = $usuario['rol'];

        header("location: /");
        exit;
      }
    }
  }

  ob_start();
?>

<div class="layout-center">
  <div class="card card--base card--login">

    <h2 class="title">GestReclama</h2>
    <p class="subtitle">Acceso al sistema</p>

    <form method="POST" action="/login.php" class="form form--login">

      <div class="form__group form__group--center">
        <label for="email">Usuario:</label>
        <input
          type="email" 
          id="email" 
          name="email" 
          value="<?= htmlspecialchars($email) ?>" 
          placeholder="miemail@dominio.es" 
          required
        >

        <label for="password">Contraseña:</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="******"
          required
        >
      </div>

      <div class="form__error">
        <?php if (!empty($error)): ?>
          <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
      </div>
      
      <div class="form__actions">
        <button type="submit" class="btn">Iniciar sesión</button>
      </div>
      
      <a href="" class="link">¿Olvidó su contraseña?</a>

    </form>
  </div>
</div>

<?php
  $contenido = ob_get_clean();
  require_once __DIR__ . '/../layout/layout.php';
?>
<?php

  session_start();

  require_once __DIR__ . '/../../backend/config/database.php';
  require_once __DIR__ . '/../../backend/helpers/validator.php';
  require_once __DIR__ . '/../config/app.php';
  
  $tituloVista = "Login";
  $sinSidebar  = true;
  
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

      $stmt = $pdo -> prepare
        ("SELECT id, email, password, rol, nombre
        FROM usuarios 
        WHERE email = :email 
        LIMIT 1"
      );
      $stmt -> execute(['email' => $email]);
      $usuario = $stmt -> fetch();

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

<div class="login-container">
  <div class="login-card">

    <h2 class="login-title">GestReclama</h2>
    <p class="login-subtitle">Acceso al sistema</p>

    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form id="form-login" method="POST" action="/login.php" class="form-login">

      <div class="form-group">
        <label for="email">Usuario: </label>
        <input
          type="email" 
          id="email" 
          name="email" 
          value="<?= htmlspecialchars($email) ?>" 
          placeholder="miemail@dominio.es" required
        >

        <label for="password">Contraseña: </label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="******"
          required
        >

        <button type="submit" id="btn_entrar" class="btn">Iniciar sesión</button>
        <a href="" class="pass-recovery">¿Olvidó su contraseña?</a>
      </div>
    </form>
  </div>
</div>

<?php
  $contenido = ob_get_clean();
  require_once __DIR__ . '/../layout/layout.php';
?>
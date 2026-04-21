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

      $stmt = $pdo -> prepare("SELECT id, email, password FROM usuarios WHERE email = :email LIMIT 1");
      $stmt -> execute(['email' => $email]);
      $usuario = $stmt -> fetch();

      if (!$usuario || (!password_verify($password, $usuario['password']))) {
        $error =  "Credenciales incorrectas...";
      } else {
        
        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['email']   = $usuario['email'];

        header("location: /");
        exit;
      }
    }
  }

  ob_start();
?>

<form id="form_login" method="POST" action="/login.php" class="form-login">
  <fieldset>
    <legend>Acceso a GestReclama</legend>

    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <label for="email">Email: </label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="miemail@dominio.es" required>

    <br>

    <label for="password">Contraseña: </label>
    <input type="password" id="password" name="password" placeholder="******" required>
  </fieldset>

  <br>

  <button type="submit" id="btn_entrar" class="btn">Entrar</button>
</form>

<?php
  $contenido = ob_get_clean();
  require_once __DIR__ . '/../layout/layout.php';
?>
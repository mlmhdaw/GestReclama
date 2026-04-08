<?php

  session_start();

  require_once __DIR__ . '/../../backend/config/database.php';

  $error = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!$email != '' && !$password != '') {
      $error = "Todos los campos son obligatorios";
    } else {

      $email = strtolower(trim($email));
      $password = trim($password);

      $pdo = Database::getConnection();

      $stmt = $pdo -> prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
      $stmt -> execute(['email' => $email]);
      $usuario = $stmt -> fetch();

      if (!$usuario || (!password_verify($password, $usuario['password']))) {
        $error =  "Credenciales incorrectas...";
      } else {
        
        session_regenerate_id(true);

        $_SESSION['user_id']    = $usuario['id'];
        $_SESSION['email'] = $usuario['email'];

        header("location: dashboard.php");
        exit;
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" href="../assets/css/styles.css">
  </head>

  <body>
    <form id="form_login" method="POST" action="login.php" class="form-login">
      <fieldset>
        <legend>Acceso a GestReclama</legend>

        <?php if (!empty($error)): ?>
          <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <label for="email">Email: </label>
        <input type="email" id="email" name="email" placeholder="miemail@dominio.es" required>

        <br>

        <label for="password">Contraseña: </label>
        <input type="password" id="password" name="password" placeholder="******" required>
      </fieldset>

      <br>

      <button type="submit" id="btn_entrar" class="btn">Entrar</button>
    </form>
  </body>
</html>
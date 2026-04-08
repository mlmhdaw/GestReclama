<?php

session_start();

if (!isset($_SESSION['user_id'])) {
  header("location: login.php");
  exit;
} else {
  echo "<a href='logout.php'>Cerrar sesión</a>";
}
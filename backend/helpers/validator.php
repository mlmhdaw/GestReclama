<?php

  function limpiarTexto($valor) {
      return trim($valor ?? '');
  }

  function estaVacio($valor) {
      return $valor === '';
  }

  function esEntero($valor) {
      return ctype_digit($valor);
  }

  function validarId($valor) {
      return $valor !== '' && ctype_digit($valor);
  }

  function existeEnBD($pdo, $tabla, $campo, $valor) {
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM $tabla WHERE $campo = :valor");
      $stmt->execute(['valor' => $valor]);
      return $stmt->fetchColumn() > 0;
  }

?>
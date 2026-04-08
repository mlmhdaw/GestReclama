<?php

class Database {
  public const HOST     = '127.0.0.1';
  public const DBNAME   = 'gestreclama';
  public const USERNAME = 'gestreclama_dev';
  public const PASSWORD = 'Dev0000.';
  public const CHARSET  = 'utf8mb4';

  private static ?PDO $connection = null;

  public static function getConnection(): PDO {
    if (self::$connection === null) {

      $dsn = "mysql:host=" . Database::HOST .
              ";dbname="   . Database::DBNAME .
              ";charset="  . Database::CHARSET;
      
      try {
        self::$connection = new PDO(
          $dsn,
          Database::USERNAME,
          Database::PASSWORD,
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
          ]
        );
    
      } catch (PDOException $e) {
          die ('Error de conexión: ' . $e->getMessage());
        
        }
    }
    return self::$connection;
  }
}
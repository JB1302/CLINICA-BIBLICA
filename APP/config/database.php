<?php
class Database {
  private static $conn = null;

  public static function get() {
    if (!self::$conn) {
      $username = 'clinica';
      $password = 'clinica123';
      // 👇 clave: usar el host del PC desde Docker
      $connectionString = '//host.docker.internal:1521/xe'; // o XEPDB1 si tu esquema está allí

      self::$conn = oci_connect($username, $password, $connectionString, 'AL32UTF8');
      if (!self::$conn) {
        $e = oci_error();
        throw new Exception('Error de conexión: '.$e['message']);
      }
    }
    return self::$conn;
  }
}

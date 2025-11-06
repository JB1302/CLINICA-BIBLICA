<?php
class Database {
  private static $conn = null;

  public static function get() {
    if (!self::$conn) {
      $username = 'JBARRANTES40180';
      $password = '123';
      $connectionString = '//host.docker.internal:1521/XEPDB1';

      $conn = oci_connect($username, $password, $connectionString, 'AL32UTF8');
      if (!$conn) {
        $e = oci_error();
        throw new Exception('Error de conexión: ' . $e['message']);
      }

      self::$conn = $conn;
      register_shutdown_function(fn() => oci_close(self::$conn));
    }
    return self::$conn;
  }
}

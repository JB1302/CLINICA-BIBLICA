<?php
require_once __DIR__ . '/../config/database.php';

class Personal
{
  private $conn;

  public function __construct()
  {
    $this->conn = Database::get();
  }

  public function obtenerTodos(): array
  {
    $sql = "SELECT ID_PERSONAL, PRIMER_NOMBRE, SEGUNDO_NOMBRE, PUESTO, ACTIVO, CORREO_ELECTRONICO, TELEFONO, DIRECCION, PROVINCIA, CANTON, DISTRITO

FROM Personal
ORDER BY ID_Personal";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }
}

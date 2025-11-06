<?php
require_once __DIR__ . '/../config/database.php';

class Paciente {
  private $conn;

  public function __construct() {
    $this->conn = Database::get();
  }

  public function obtenerTodos(): array {
    $sql = "SELECT ID_PACIENTE, CEDULA, PRIMER_NOMBRE, SEGUNDO_NOMBRE,
                   PRIMER_APELLIDO, SEGUNDO_APELLIDO, FECHA_NACIMIENTO,
                   SEXO, OBSERVACIONES, TELEFONO, DIRECCION, CORREO_ELECTRONICO
            FROM JBARRANTES40180.PACIENTE
            ORDER BY ID_PACIENTE";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }
}

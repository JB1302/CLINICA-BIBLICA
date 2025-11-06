<?php
require_once __DIR__ . '/../config/database.php';

class Paciente {
  public function obtenerTodos() {
    $conn = Database::get();
    $sql = "SELECT ID_PACIENTE, PRIMER_NOMBRE, PRIMER_APELLIDO, CEDULA, TELEFONO
            FROM PACIENTE
            ORDER BY ID_PACIENTE DESC";
    $st = oci_parse($conn, $sql);
    oci_execute($st);

    $pacientes = [];
    while ($r = oci_fetch_assoc($st)) {
      $pacientes[] = $r;
    }
    oci_free_statement($st);
    return $pacientes;
  }
}
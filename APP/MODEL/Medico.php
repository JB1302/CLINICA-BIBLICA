<?php
require_once __DIR__ . '/../config/database.php';

class Medico
{
  private $conn;

  public function __construct()
  {
    $this->conn = Database::get();
  }

  public function obtenerTodos(): array
  {
    $sql = "SELECT
  m.id_medico,
  p.primer_nombre || ' ' || p.segundo_nombre AS nombre_medico,
  e.nombre AS especialidad
FROM medico m
JOIN personal p
  ON p.id_personal = m.id_personal
JOIN medico_especialidad me
  ON me.id_medico = m.id_medico
JOIN especialidad e
  ON e.id_especialidad = me.id_especialidad
ORDER BY nombre_medico, e.nombre";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }
}

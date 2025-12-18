<?php
require_once __DIR__ . '/../config/database.php';

class Dashboard
{
  private $conn;

  public function __construct()
  {
    $this->conn = Database::get();
  }
  //  método para obtener citas por estado
  public function citasPorEstado(): array
  {
    $sql = "
      SELECT 
        e.nombre_estado AS \"estado\", 
        COUNT(*) AS \"cantidad\"
      FROM cita c
      JOIN estado_cita e ON e.id_estado = c.id_estado
      GROUP BY e.nombre_estado
      ORDER BY e.nombre_estado";
    $st = oci_parse($this->conn, $sql);
    oci_execute($st);

    $out = [];
    while ($r = oci_fetch_assoc($st)) $out[] = $r;

    oci_free_statement($st);
    return $out;
  }
  //  método para obtener atenciones por médico
  public function atencionesPorMedico(): array
  {
    $sql = "
      SELECT 
        p.primer_nombre || ' ' || p.segundo_nombre AS \"medico\",
        COUNT(*) AS \"atenciones\"
      FROM atencion a
      JOIN cita c ON c.id_cita = a.id_cita
      JOIN medico m ON m.id_medico = c.id_medico
      JOIN personal p ON p.id_personal = m.id_personal
      GROUP BY p.primer_nombre || ' ' || p.segundo_nombre
      ORDER BY 2 DESC, 1";
    $st = oci_parse($this->conn, $sql);
    oci_execute($st);

    $out = [];
    while ($r = oci_fetch_assoc($st)) $out[] = $r;

    oci_free_statement($st);
    return $out;
  }
  //  método para obtener pacientes nuevos por mes
  public function pacientesNuevosPorMes(): array
  {
    $sql = "
      SELECT 
        TO_CHAR(e.creado_en, 'YYYY-MM') AS \"mes\",
        COUNT(*) AS \"nuevos\"
      FROM expediente e
      GROUP BY TO_CHAR(e.creado_en, 'YYYY-MM')
      ORDER BY 1";
    $st = oci_parse($this->conn, $sql);
    oci_execute($st);

    $out = [];
    while ($r = oci_fetch_assoc($st)) $out[] = $r;

    oci_free_statement($st);
    return $out;
  }

// método para obtener KPIs
public function getKpis(): array {
  // Ajusta el WHERE de paciente si no tienes columna ACTIVO
  $sql = "
    SELECT
      (SELECT COUNT(*) FROM paciente /*WHERE activo = 1*/) AS pacientes,
      (SELECT COUNT(*) FROM cita)                           AS citas,
      (SELECT COUNT(*) FROM atencion)                       AS atenciones
    FROM dual";

  $st = oci_parse($this->conn, $sql);
  oci_execute($st);

  $row = oci_fetch_assoc($st) ?: [];
  oci_free_statement($st);

  $row = array_change_key_case($row, CASE_LOWER);

  return [
    'pacientes'  => (int)($row['pacientes']  ?? 0),
    'citas'      => (int)($row['citas']      ?? 0),
    'atenciones' => (int)($row['atenciones'] ?? 0),
  ];
}


}
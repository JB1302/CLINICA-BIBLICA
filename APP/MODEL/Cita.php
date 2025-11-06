<?php
require_once __DIR__ . '/../config/database.php';

class Cita
{
  private $conn;

  public function __construct()
  {
    $this->conn = Database::get();
  }

  public function obtenerTodos(): array
  {
    $sql = "SELECT
  c.id_cita,
  TO_CHAR(c.fecha, 'DD/MM/YYYY') AS fecha,
  TO_CHAR(c.hora_agendada_inicio, 'HH24:MI') AS hora_inicio,
  TO_CHAR(c.hora_agendada_fin, 'HH24:MI') AS hora_fin,

  p.id_paciente,
  p.cedula,
  p.primer_nombre || ' ' || p.segundo_nombre || ' ' ||
  p.primer_apellido || ' ' || p.segundo_apellido AS paciente,

  per.id_personal,
  per.primer_nombre || ' ' || per.segundo_nombre AS nombre_medico,

  e.nombre AS especialidad,
  con.nombre AS consultorio,
  cli.nombre AS clinica,
  es.nombre_estado AS estado_cita,
  mc.nombre AS motivo_cancelacion,
  ag.dia_semana,
  TO_CHAR(ag.hora_inicio, 'HH24:MI') AS turno_inicio,
  TO_CHAR(ag.hora_fin, 'HH24:MI') AS turno_fin,
  c.observaciones
FROM cita c
JOIN paciente p ON p.id_paciente = c.id_paciente
JOIN medico m ON m.id_medico = c.id_medico
JOIN personal per ON per.id_personal = m.id_personal
JOIN consultorio con ON con.id_consultorio = c.id_consultorio
JOIN clinica cli ON cli.id_clinica = con.id_clinica
JOIN estado_cita es ON es.id_estado = c.id_estado
LEFT JOIN motivo_cancelacion mc ON mc.id_motivo_cancelacion = c.id_motivo_cancelacion
LEFT JOIN agenda_medica ag ON ag.id_agenda = c.id_agenda
LEFT JOIN medico_especialidad me ON me.id_medico = m.id_medico
LEFT JOIN especialidad e ON e.id_especialidad = me.id_especialidad
ORDER BY c.fecha DESC, c.hora_agendada_inicio DESC";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }
}

<?php
require_once __DIR__ . '/../config/database.php';

class Medico
{
  private $conn;

  public function __construct()
  {
    $this->conn = Database::get();
  }

  /**
   * Listar médicos con nombre y especialidad actual
   */
  public function obtenerTodos(): array
  {
    //  Obtener horario directamente desde MEDICO.ID_HORARIO con JOIN a AGENDA_HORARIO
    $sql = "SELECT
              m.id_medico                                AS ID_MEDICO,
              p.primer_nombre || ' ' || p.primer_apellido AS NOMBRE_MEDICO,
              e.nombre                                   AS ESPECIALIDAD,
              e.id_especialidad                          AS ID_ESPECIALIDAD,
              m.id_horario                               AS ID_HORARIO,
              ah.horario                                 AS HORARIOS
            FROM medico m
              JOIN personal p
                ON p.id_personal = m.id_personal
              LEFT JOIN medico_especialidad me
                ON me.id_medico = m.id_medico
               AND me.hasta IS NULL
              LEFT JOIN especialidad e
                ON e.id_especialidad = me.id_especialidad
              LEFT JOIN agenda_horario ah
                ON ah.id_horario = m.id_horario
            ORDER BY NOMBRE_MEDICO, e.nombre";
    
    
    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) {
      $rows[] = $r;
    }

    oci_free_statement($stmt);
    return $rows;
  }

  /**
   * Listar personal disponible para ser asignado como médico
   */
  public function obtenerPersonalDisponible(): array
  {
    $sql = "
      SELECT
        p.id_personal,
        p.primer_nombre || ' ' || p.primer_apellido AS NOMBRE_COMPLETO,
        p.puesto
      FROM personal p
      WHERE NOT EXISTS (
        SELECT 1
        FROM medico m
        WHERE m.id_personal = p.id_personal
      )
      AND LOWER(p.puesto) LIKE '%doctor%'   -- esto cubre Doctor y Doctora
      ORDER BY NOMBRE_COMPLETO
    ";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($row = oci_fetch_assoc($stmt)) {
      $rows[] = $row;
    }

    oci_free_statement($stmt);
    return $rows;
  }


  /**
   * Listar especialidades para combos
   */
  public function obtenerEspecialidades(): array
  {
    $sql = "SELECT ID_ESPECIALIDAD, NOMBRE
            FROM ESPECIALIDAD
            ORDER BY NOMBRE ASC";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) {
      $rows[] = $r;
    }

    oci_free_statement($stmt);
    return $rows;
  }

  /**
   * Crear médico -> pkg_medico.agregar_medico
   * Espera: ID_PERSONAL, ID_ESPECIALIDAD
   */
  public function crear(array $data): array
  {
    $sql = "BEGIN
              pkg_medico.agregar_medico(
                :pin_id_personal,
                :pin_id_especialidad,
                :pout_resultado,
                :pout_mensaje
              );
            END;";

    $stmt = oci_parse($this->conn, $sql);

    $idPersonal = (int)($data['ID_PERSONAL'] ?? 0);
    $idEsp      = (int)($data['ID_ESPECIALIDAD'] ?? 0);

    oci_bind_by_name($stmt, ':pin_id_personal',     $idPersonal);
    oci_bind_by_name($stmt, ':pin_id_especialidad', $idEsp);

    $resultado = 0;
    $mensaje   = '';

    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 32);
    oci_bind_by_name($stmt, ':pout_mensaje',   $mensaje,   4000);

    oci_execute($stmt);
    oci_free_statement($stmt);

    return [
      'resultado' => (int)$resultado,
      'mensaje'   => $mensaje,
    ];
  }

  /**
   * Actualizar médico -> pkg_medico.editar_medico
   * Espera: ID_MEDICO, NOMBRE (completo), ID_ESPECIALIDAD
   * Agregar actualización de ID_HORARIO directamente en MEDICO
   */
  public function actualizar(array $data): array
  {
    $sql = "BEGIN
              pkg_medico.editar_medico(
                :pin_id_medico,
                :pin_nombre,
                :pin_id_especialidad,
                :pout_resultado,
                :pout_mensaje
              );
            END;";

    $stmt = oci_parse($this->conn, $sql);

    $idMedico = (int)($data['ID_MEDICO'] ?? 0);
    $nombre   = $data['NOMBRE'] ?? '';
    $idEsp    = (int)($data['ID_ESPECIALIDAD'] ?? 0);

    oci_bind_by_name($stmt, ':pin_id_medico',       $idMedico);
    oci_bind_by_name($stmt, ':pin_nombre',          $nombre);
    oci_bind_by_name($stmt, ':pin_id_especialidad', $idEsp);

    $resultado = 0;
    $mensaje   = '';

    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 32);
    oci_bind_by_name($stmt, ':pout_mensaje',   $mensaje,   4000);

    oci_execute($stmt);
    oci_free_statement($stmt);

    //  Actualizar ID_HORARIO directamente en MEDICO si se proporcionó
    if ((int)$resultado === 1 && isset($data['ID_HORARIO'])) {
      $idHorario = $data['ID_HORARIO'];
      
      if ($idHorario === null || $idHorario === '') {
        // Si no hay horario, establecer NULL
        $sqlUpdateHorario = "UPDATE MEDICO SET ID_HORARIO = NULL WHERE ID_MEDICO = :id_medico";
        $stmtUpdateHorario = oci_parse($this->conn, $sqlUpdateHorario);
        oci_bind_by_name($stmtUpdateHorario, ':id_medico', $idMedico);
      } else {
        // Actualizar con el nuevo horario
        $idHorario = (int)$idHorario;
        $sqlUpdateHorario = "UPDATE MEDICO SET ID_HORARIO = :id_horario WHERE ID_MEDICO = :id_medico";
        $stmtUpdateHorario = oci_parse($this->conn, $sqlUpdateHorario);
        oci_bind_by_name($stmtUpdateHorario, ':id_horario', $idHorario);
        oci_bind_by_name($stmtUpdateHorario, ':id_medico', $idMedico);
      }
      
      oci_execute($stmtUpdateHorario);
      oci_free_statement($stmtUpdateHorario);
      oci_commit($this->conn);
    }

    return [
      'resultado' => (int)$resultado,
      'mensaje'   => $mensaje,
    ];
  }

  /**
   * Eliminar médico -> pkg_medico.eliminar_medico
   */
  public function eliminar(int $idMedico): array
  {
    $sql = "BEGIN
              pkg_medico.eliminar_medico(
                :pin_id_medico,
                :pout_resultado,
                :pout_mensaje
              );
            END;";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_id_medico', $idMedico);

    $resultado = 0;
    $mensaje   = '';

    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 32);
    oci_bind_by_name($stmt, ':pout_mensaje',   $mensaje,   4000);

    oci_execute($stmt);
    oci_free_statement($stmt);

    return [
      'resultado' => (int)$resultado,
      'mensaje'   => $mensaje,
    ];
  }
}
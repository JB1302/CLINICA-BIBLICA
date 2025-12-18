<?php
require_once __DIR__ . '/../config/database.php';

class Cita
{
  private $conn;
  // Constructor para inicializar la conexión a la base de datos
  public function __construct()
  {
    $this->conn = Database::get();
  }
  //  método para listar todas las citas
  public function obtenerTodos(): array
  {
    $sql = "SELECT
      c.id_cita,
      TO_CHAR(c.fecha, 'DD/MM/YYYY') AS fecha,
      TO_CHAR(c.fecha, 'YYYY-MM-DD')      AS FECHA_HTML,
      TO_CHAR(c.hora_agendada_inicio, 'HH24:MI') AS hora_inicio,
      TO_CHAR(c.hora_agendada_fin, 'HH24:MI') AS hora_fin,
      

      p.id_paciente,
      c.id_medico AS id_medico,
      p.cedula,
      p.primer_nombre || ' ' || p.segundo_nombre || ' ' ||
      p.primer_apellido || ' ' || p.segundo_apellido AS paciente,

      per.id_personal,
      per.primer_nombre || ' ' || per.segundo_nombre AS nombre_medico,

      e.nombre AS especialidad,
      con.id_consultorio,
      con.nombre AS consultorio,
      cli.id_clinica,
      cli.nombre AS clinica,
      c.id_estado AS ESTADO,
      es.nombre_estado AS estado_cita,
      mc.nombre AS motivo_cancelacion,

      -- Comentado porque AGENDA_MEDICA no tiene columnas HORA_INICIO/HORA_FIN

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
      ORDER BY c.id_cita DESC";
    // Ejecutar la consulta
    $stmt = oci_parse($this->conn, $sql);
    $ok = @oci_execute($stmt);
    // Manejar errores de ejecución
    if (!$ok) {
        $e = oci_error($stmt) ?: oci_error($this->conn);
        oci_free_statement($stmt);
        error_log('Cita::obtenerTodos: ' . ($e['message'] ?? 'Error Oracle'));
        return [];
    }

    $rows = [];
    // Obtener los resultados
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }

  //  método para cancelar una cita
  public function cancelarCita(int $idCita, ?int $idMotivoCancelacion, ?string $observaciones): array
  {
      $sql = "
        BEGIN
          pkg_cita.cancelar_cita(
            :pin_id_cita,
            :pin_id_motivo_cancelacion,
            :pin_observaciones,
            :pout_resultado,
            :pout_mensaje
          );
        END;
      ";
      // Preparar la sentencia
      $stmt = oci_parse($this->conn, $sql);

      oci_bind_by_name($stmt, ':pin_id_cita', $idCita);
      oci_bind_by_name($stmt, ':pin_id_motivo_cancelacion', $idMotivoCancelacion);
      oci_bind_by_name($stmt, ':pin_observaciones', $observaciones, 4000);

      $resultado = 0;
      $mensaje   = '';

      oci_bind_by_name($stmt, ':pout_resultado', $resultado, 10);
      oci_bind_by_name($stmt, ':pout_mensaje',   $mensaje,   4000);
      // Ejecutar la sentencia
      $ok = @oci_execute($stmt);
      // Manejar errores de ejecución
      if (!$ok) {
          $e = oci_error($stmt) ?: oci_error($this->conn);
          oci_free_statement($stmt);

          $mensajeOracle = $e['message'] ?? 'Error al cancelar la cita';
          $mensajeLimpio = $mensajeOracle;

          // Se queda con el texto del ORA-20xxx
          if (preg_match('/ORA-20\d{3}:\s*(.+?)(?:ORA-|$)/s', $mensajeOracle, $m)) {
              $mensajeLimpio = trim($m[1]);
          } else {
              // fallback: primera línea
              $mensajeLimpio = trim(strtok($mensajeOracle, "\n"));
          }

          return [
              'resultado' => 0,
              'mensaje'   => $mensajeLimpio,
          ];
      }
      oci_free_statement($stmt);

      return [
          'resultado' => $resultado,
          'mensaje'   => $mensaje,
      ];

  }
  //  método para obtener estados de cita
  public function obtenerEstadosCita(): array
  {
      $sql = "SELECT id_estado, nombre_estado FROM estado_cita ORDER BY nombre_estado";
      $stmt = oci_parse($this->conn, $sql);
      oci_execute($stmt);

      $estados = [];
      while ($row = oci_fetch_assoc($stmt)) {
          $estados[] = $row;
      }

      oci_free_statement($stmt);
      return $estados;
  }

  //  método para obtener motivos de cancelación
  public function obtenerMotivosCancelacion(): array
  {
      $sql = "SELECT id_motivo_cancelacion, nombre FROM motivo_cancelacion ORDER BY nombre";
      $stmt = oci_parse($this->conn, $sql);
      oci_execute($stmt);

      $motivos = [];
      while ($row = oci_fetch_assoc($stmt)) {
          $motivos[] = $row;
      }

      oci_free_statement($stmt);
      return $motivos;
  }

  // metodo para obtener clínicas
  public function obtenerClinicas(): array
  {
      $sql = "SELECT ID_CLINICA, NOMBRE, CODIGO_CLINICA, TELEFONO FROM CLINICA ORDER BY NOMBRE";
      $stmt = oci_parse($this->conn, $sql);
      oci_execute($stmt);

      $clinicas = [];
      while ($row = oci_fetch_assoc($stmt)) {
          $clinicas[] = $row;
      }

      oci_free_statement($stmt);
      return $clinicas;
  }

  //  metodo para obtener consultorios (opcionalmente filtrados por clínica)
  public function obtenerConsultorios(?int $idClinica = null): array
  {
      if ($idClinica !== null) {
          $sql = "SELECT c.ID_CONSULTORIO, c.ID_CLINICA, c.NOMBRE, c.TIPO, cl.NOMBRE AS NOMBRE_CLINICA 
                  FROM CONSULTORIO c
                  JOIN CLINICA cl ON cl.ID_CLINICA = c.ID_CLINICA
                  WHERE c.ID_CLINICA = :id_clinica 
                  ORDER BY c.NOMBRE";
          $stmt = oci_parse($this->conn, $sql);
          oci_bind_by_name($stmt, ':id_clinica', $idClinica);
      } else {
          $sql = "SELECT c.ID_CONSULTORIO, c.ID_CLINICA, c.NOMBRE, c.TIPO, cl.NOMBRE AS NOMBRE_CLINICA 
                  FROM CONSULTORIO c
                  JOIN CLINICA cl ON cl.ID_CLINICA = c.ID_CLINICA
                  ORDER BY cl.NOMBRE, c.NOMBRE";
          $stmt = oci_parse($this->conn, $sql);
      }
      
      oci_execute($stmt);

      $consultorios = [];
      while ($row = oci_fetch_assoc($stmt)) {
          $consultorios[] = $row;
      }

      oci_free_statement($stmt);
      return $consultorios;
  }

    // metodo para crear una nueva cita
    public function crearCita(
    int $idPaciente,
    int $idMedico,
    string $fecha,         
    string $horaInicio,    
    string $horaFin,       
    int $idEstado,
    int $idConsultorio,
    ?string $observaciones = null
  ): array {

    $fechaFormat  = null;

    if ($fecha !== '') {
        // [0] = YYYY, [1] = MM, [2] = DD
        [$y, $m, $d] = explode('-', $fecha);
        $fechaFormat = sprintf('%02d/%02d/%04d', $d, $m, $y);
    }
    $sql = "
      BEGIN
        pkg_cita.agregar_cita(
          :pin_id_paciente,
          :pin_id_medico,
          :pin_fecha,
          :pin_hora_inicio,
          :pin_hora_fin,
          :pin_id_estado,
          :pin_id_consultorio,
          :pin_observaciones,
          :pout_resultado,
          :pout_mensaje
        );
      END;
    ";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_id_paciente', $idPaciente);
    oci_bind_by_name($stmt, ':pin_id_medico',   $idMedico);
    oci_bind_by_name($stmt, ':pin_fecha',       $fechaFormat);
    oci_bind_by_name($stmt, ':pin_hora_inicio', $horaInicio);
    oci_bind_by_name($stmt, ':pin_hora_fin',    $horaFin);
    oci_bind_by_name($stmt, ':pin_id_estado',   $idEstado);
    oci_bind_by_name($stmt, ':pin_id_consultorio', $idConsultorio);
    oci_bind_by_name($stmt, ':pin_observaciones', $observaciones, 4000);

    $resultado = 0;
    $mensaje   = '';
    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 10);
    oci_bind_by_name($stmt, ':pout_mensaje',   $mensaje,   4000);

    $ok = @oci_execute($stmt);
    if (!$ok) {
        $e = oci_error($stmt);
        oci_free_statement($stmt);
        $mensajeOracle = $e['message'] ?? '';
        $mensajeLimpio = $mensajeOracle;

        // Busca "ORA-200xx: " y se queda con lo que viene después
        if (preg_match('/ORA-20\d{3}:\s*(.+)$/m', $mensajeOracle, $m)) {
            $mensajeLimpio = $m[1]; 
        }
        return [
            'resultado' => 0,
            'mensaje'   => $mensajeLimpio,
        ];
    }

    oci_free_statement($stmt);

    return [
      'resultado' => $resultado,
      'mensaje'   => $mensaje,
    ];
  }

  //  método para actualizar una cita
  public function actualizarCita(
    int $idCita,
    int $idPaciente,
    int $idMedico,
    string $fecha,
    string $horaInicio,
    string $horaFin,
    int $idEstado,
    int $idConsultorio,
    ?int $idMotivoCancelacion = null,
    ?string $observaciones = null
  ): array {

    $fechaFormat  = null;

    if ($fecha !== '') {
        // [0] = YYYY, [1] = MM, [2] = DD
        [$y, $m, $d] = explode('-', $fecha);
        $fechaFormat = sprintf('%02d/%02d/%04d', $d, $m, $y);
    }

    $sql = "
      BEGIN
        pkg_cita.editar_cita(
          :pin_id_cita,
          :pin_id_paciente,
          :pin_id_medico,
          :pin_fecha,
          :pin_hora_inicio,
          :pin_hora_fin,
          :pin_id_estado,
          :pin_id_consultorio,
          :pin_id_motivo_cancelacion,
          :pin_observaciones,
          :pout_resultado,
          :pout_mensaje
        );
      END;
    ";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_id_cita',      $idCita);
    oci_bind_by_name($stmt, ':pin_id_paciente',  $idPaciente);
    oci_bind_by_name($stmt, ':pin_id_medico',    $idMedico);
    oci_bind_by_name($stmt, ':pin_fecha',        $fechaFormat);
    oci_bind_by_name($stmt, ':pin_hora_inicio',  $horaInicio);
    oci_bind_by_name($stmt, ':pin_hora_fin',     $horaFin);
    oci_bind_by_name($stmt, ':pin_id_estado',    $idEstado);
    oci_bind_by_name($stmt, ':pin_id_consultorio', $idConsultorio);
    oci_bind_by_name($stmt, ':pin_id_motivo_cancelacion', $idMotivoCancelacion);
    oci_bind_by_name($stmt, ':pin_observaciones', $observaciones, 4000);

    $resultado = 0;
    $mensaje   = '';

    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 10);
    oci_bind_by_name($stmt, ':pout_mensaje',   $mensaje,   4000);

    $ok = @oci_execute($stmt);
    if (!$ok) {
        $e = oci_error($stmt);
        oci_free_statement($stmt);

        return [
            'resultado' => 0,
            'mensaje'   => $e['message'] ?? 'Error al actualizar la cita',
        ];
    }

    oci_free_statement($stmt);

    return [
      'resultado' => $resultado,
      'mensaje'   => $mensaje,
    ];
  }

  //  método para eliminar una cita
  public function eliminarCita(int $idCita): array
  {
    $sql = "
      BEGIN
        pkg_cita.eliminar_cita(
          :pin_id_cita,
          :pout_resultado,
          :pout_mensaje
        );
      END;
    ";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_id_cita', $idCita);

    $resultado = 0;
    $mensaje   = '';
    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 10);
    oci_bind_by_name($stmt, ':pout_mensaje',   $mensaje,   4000);

    oci_execute($stmt);
    oci_free_statement($stmt);

    return [
      'resultado' => $resultado,
      'mensaje'   => $mensaje,
    ];
  }

  // Obtener horario del medico directamente desde MEDICO.ID_HORARIO
  public function obtenerHorariosMedico(int $idMedico): array
  {
    $sql = "SELECT ah.horario, ah.dia_semana, ah.turno, ah.hora_inicio, ah.hora_fin
            FROM medico m
            JOIN agenda_horario ah ON ah.id_horario = m.id_horario
            WHERE m.id_medico = :id_medico";

    $stmt = oci_parse($this->conn, $sql);
    oci_bind_by_name($stmt, ':id_medico', $idMedico);
    oci_execute($stmt);

    $horarios = [];
    while ($row = oci_fetch_assoc($stmt)) {
      $horarios[] = $row;
    }

    oci_free_statement($stmt);
    return $horarios;
  }
}
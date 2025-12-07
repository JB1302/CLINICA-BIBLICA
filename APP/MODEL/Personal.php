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
    // Cambio de Adry: Agregados PRIMER_APELLIDO, SEGUNDO_APELLIDO, HORARIO de AGENDA_HORARIO y FECHA_REGISTRO de CONTRATO
    // Cambio de Adry: Ordenamiento descendente por ID_PERSONAL para mostrar los más recientes primero
    $sql = "SELECT p.ID_PERSONAL, p.PRIMER_NOMBRE, p.SEGUNDO_NOMBRE, p.PRIMER_APELLIDO, p.SEGUNDO_APELLIDO, 
                   p.PUESTO, p.ACTIVO, p.CORREO_ELECTRONICO, p.TELEFONO, p.DIRECCION, 
                   p.PROVINCIA, p.CANTON, p.DISTRITO, p.HORARIO_TRABAJO,
                   ah.HORARIO AS HORARIO_TEXTO,
                   c.FECHA_REGISTRO, c.VIGENTE AS CONTRATO_VIGENTE
            FROM Personal p
            LEFT JOIN Contrato c ON p.ID_PERSONAL = c.ID_PERSONAL
            LEFT JOIN AGENDA_HORARIO ah ON p.HORARIO_TRABAJO = ah.ID_HORARIO
            ORDER BY p.ID_PERSONAL DESC";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }

  public function crear(array $data): array
  {
    // Cambio de Adry: Agregados PRIMER_APELLIDO, SEGUNDO_APELLIDO y HORARIO_TRABAJO al procedimiento
    $sql = "BEGIN
              pkg_personal.agregar_personal(
                :pin_primer_nombre,
                :pin_segundo_nombre,
                :pin_primer_apellido,
                :pin_segundo_apellido,
                :pin_puesto,
                :pin_activo,
                :pin_correo_electronico,
                :pin_telefono,
                :pin_direccion,
                :pin_provincia,
                :pin_canton,
                :pin_distrito,
                :pin_horario_trabajo,
                :pout_resultado,
                :pout_mensaje
              );
            END;";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_primer_nombre', $data['PRIMER_NOMBRE']);
    oci_bind_by_name($stmt, ':pin_segundo_nombre', $data['SEGUNDO_NOMBRE']);
    oci_bind_by_name($stmt, ':pin_primer_apellido', $data['PRIMER_APELLIDO']);
    oci_bind_by_name($stmt, ':pin_segundo_apellido', $data['SEGUNDO_APELLIDO']);
    oci_bind_by_name($stmt, ':pin_puesto', $data['PUESTO']);
    oci_bind_by_name($stmt, ':pin_activo', $data['ACTIVO']);
    oci_bind_by_name($stmt, ':pin_correo_electronico', $data['CORREO_ELECTRONICO']);
    oci_bind_by_name($stmt, ':pin_telefono', $data['TELEFONO']);
    oci_bind_by_name($stmt, ':pin_direccion', $data['DIRECCION']);
    oci_bind_by_name($stmt, ':pin_provincia', $data['PROVINCIA']);
    oci_bind_by_name($stmt, ':pin_canton', $data['CANTON']);
    oci_bind_by_name($stmt, ':pin_distrito', $data['DISTRITO']);
    oci_bind_by_name($stmt, ':pin_horario_trabajo', $data['HORARIO_TRABAJO']);

    $resultado = 0;
    $mensaje   = '';
    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 32);
    oci_bind_by_name($stmt, ':pout_mensaje', $mensaje,4000);
    $ok = @oci_execute($stmt);
    if (!$ok) {
        $e = oci_error($stmt);
        oci_free_statement($stmt);
        $mensajeOracle = $e['message'] ?? '';
        $mensajeLimpio = $mensajeOracle;

        // Busca "ORA-200xx: " y se queda con lo que viene después
        if (preg_match('/ORA-20\d{3}:\s*(.+)$/m', $mensajeOracle, $m)) {
            $mensajeLimpio = $m[1]; // solo el texto del trigger
        }
        return [
            'resultado' => 0,
            'mensaje'   => $mensajeLimpio,
        ];
    }
    oci_free_statement($stmt);

    // Cambio de Adry: Si se creó exitosamente el personal, crear el contrato
    if ((int)$resultado === 1 && !empty($data['FECHA_CONTRATACION'])) {
      // Obtener el ID del personal recién creado
      $sqlId = "SELECT MAX(ID_PERSONAL) as ULTIMO_ID FROM PERSONAL";
      $stmtId = oci_parse($this->conn, $sqlId);
      oci_execute($stmtId);
      $row = oci_fetch_assoc($stmtId);
      $idPersonal = $row['ULTIMO_ID'];
      oci_free_statement($stmtId);

      // Insertar el contrato
      if ($idPersonal) {
        $sqlContrato = "INSERT INTO CONTRATO (ID_PERSONAL, FECHA_REGISTRO, VIGENTE) 
                        VALUES (:id_personal, TO_DATE(:fecha, 'YYYY-MM-DD'), 'S')";
        $stmtContrato = oci_parse($this->conn, $sqlContrato);
        oci_bind_by_name($stmtContrato, ':id_personal', $idPersonal);
        oci_bind_by_name($stmtContrato, ':fecha', $data['FECHA_CONTRATACION']);
        oci_execute($stmtContrato);
        oci_free_statement($stmtContrato);
        oci_commit($this->conn);
      }
    }

    // Cambio de Adry: Si se creó el personal exitosamente y tiene horario, actualizar MEDICO.ID_HORARIO si es médico
    if ((int)$resultado === 1 && !empty($data['HORARIO_TRABAJO'])) {
      // Obtener el ID del personal recién creado si no se tiene
      if (!isset($idPersonal)) {
        $sqlId = "SELECT MAX(ID_PERSONAL) as ULTIMO_ID FROM PERSONAL";
        $stmtId = oci_parse($this->conn, $sqlId);
        oci_execute($stmtId);
        $row = oci_fetch_assoc($stmtId);
        $idPersonal = $row['ULTIMO_ID'];
        oci_free_statement($stmtId);
      }

      if ($idPersonal) {
        $idHorario = (int)$data['HORARIO_TRABAJO'];
        
        // Actualizar ID_HORARIO en MEDICO si existe
        $sqlUpdateMedico = "UPDATE MEDICO 
                            SET ID_HORARIO = :id_horario 
                            WHERE ID_PERSONAL = :id_personal";
        $stmtUpdateMedico = oci_parse($this->conn, $sqlUpdateMedico);
        oci_bind_by_name($stmtUpdateMedico, ':id_horario', $idHorario);
        oci_bind_by_name($stmtUpdateMedico, ':id_personal', $idPersonal);
        @oci_execute($stmtUpdateMedico);
        oci_free_statement($stmtUpdateMedico);
        oci_commit($this->conn);
      }
    }

    return [
      'resultado' => (int)$resultado,
      'mensaje'   => $mensaje,
    ];
  }
  public function actualizar(array $data): array
  {
    // Cambio de Adry: Agregados PRIMER_APELLIDO, SEGUNDO_APELLIDO y HORARIO_TRABAJO al procedimiento
    $sql = "BEGIN
              pkg_personal.editar_personal(
                :pin_id_personal,
                :pin_primer_nombre,
                :pin_segundo_nombre,
                :pin_primer_apellido,
                :pin_segundo_apellido,
                :pin_puesto,
                :pin_activo,
                :pin_correo_electronico,
                :pin_telefono,
                :pin_direccion,
                :pin_provincia,
                :pin_canton,
                :pin_distrito,
                :pin_horario_trabajo,
                :pout_resultado,
                :pout_mensaje
              );
            END;";

    $stmt = oci_parse($this->conn, $sql);

    $idPersonal = (int)($data['ID_PERSONAL'] ?? 0);
    oci_bind_by_name($stmt, ':pin_id_personal', $data['ID_PERSONAL']);
    oci_bind_by_name($stmt, ':pin_primer_nombre', $data['PRIMER_NOMBRE']);
    oci_bind_by_name($stmt, ':pin_segundo_nombre', $data['SEGUNDO_NOMBRE']);
    oci_bind_by_name($stmt, ':pin_primer_apellido', $data['PRIMER_APELLIDO']);
    oci_bind_by_name($stmt, ':pin_segundo_apellido', $data['SEGUNDO_APELLIDO']);
    oci_bind_by_name($stmt, ':pin_puesto', $data['PUESTO']);
    oci_bind_by_name($stmt, ':pin_activo', $data['ACTIVO']);
    oci_bind_by_name($stmt, ':pin_correo_electronico', $data['CORREO_ELECTRONICO']);
    oci_bind_by_name($stmt, ':pin_telefono', $data['TELEFONO']);
    oci_bind_by_name($stmt, ':pin_direccion', $data['DIRECCION']);
    oci_bind_by_name($stmt, ':pin_provincia', $data['PROVINCIA']);
    oci_bind_by_name($stmt, ':pin_canton', $data['CANTON']);
    oci_bind_by_name($stmt, ':pin_distrito', $data['DISTRITO']);
    oci_bind_by_name($stmt, ':pin_horario_trabajo', $data['HORARIO_TRABAJO']);

    $resultado = 0;
    $mensaje   = '';
    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 32);
    oci_bind_by_name($stmt, ':pout_mensaje', $mensaje,4000);
    $ok = @oci_execute($stmt);
    if (!$ok) {
        $e = oci_error($stmt);
        oci_free_statement($stmt);
        $mensajeOracle = $e['message'] ?? '';
        $mensajeLimpio = $mensajeOracle;

        // Busca "ORA-200xx: " y se queda con lo que viene después
        if (preg_match('/ORA-20\d{3}:\s*(.+)$/m', $mensajeOracle, $m)) {
            $mensajeLimpio = $m[1]; // solo el texto del trigger
        }
        return [
            'resultado' => 0,
            'mensaje'   => $mensajeLimpio,
        ];
    }
    oci_free_statement($stmt);

    // Cambio de Adry: Si se actualizó exitosamente y hay fecha de contratación, actualizar el contrato
    if ((int)$resultado === 1 && !empty($data['FECHA_CONTRATACION']) && $idPersonal > 0) {
      // Verificar si ya existe un contrato
      $sqlCheck = "SELECT COUNT(*) as TOTAL FROM CONTRATO WHERE ID_PERSONAL = :id_personal";
      $stmtCheck = oci_parse($this->conn, $sqlCheck);
      oci_bind_by_name($stmtCheck, ':id_personal', $idPersonal);
      oci_execute($stmtCheck);
      $row = oci_fetch_assoc($stmtCheck);
      $existe = (int)$row['TOTAL'];
      oci_free_statement($stmtCheck);

      if ($existe > 0) {
        // Actualizar contrato existente
        $sqlContrato = "UPDATE CONTRATO 
                        SET FECHA_REGISTRO = TO_DATE(:fecha, 'YYYY-MM-DD')
                        WHERE ID_PERSONAL = :id_personal";
        $stmtContrato = oci_parse($this->conn, $sqlContrato);
        oci_bind_by_name($stmtContrato, ':id_personal', $idPersonal);
        oci_bind_by_name($stmtContrato, ':fecha', $data['FECHA_CONTRATACION']);
        oci_execute($stmtContrato);
        oci_free_statement($stmtContrato);
      } else {
        // Crear nuevo contrato si no existe
        $sqlContrato = "INSERT INTO CONTRATO (ID_PERSONAL, FECHA_REGISTRO, VIGENTE) 
                        VALUES (:id_personal, TO_DATE(:fecha, 'YYYY-MM-DD'), 'S')";
        $stmtContrato = oci_parse($this->conn, $sqlContrato);
        oci_bind_by_name($stmtContrato, ':id_personal', $idPersonal);
        oci_bind_by_name($stmtContrato, ':fecha', $data['FECHA_CONTRATACION']);
        oci_execute($stmtContrato);
        oci_free_statement($stmtContrato);
      }
      oci_commit($this->conn);
    }

    // Cambio de Adry: Si se actualizó el personal exitosamente y tiene horario, actualizar MEDICO.ID_HORARIO si es médico
    if ((int)$resultado === 1 && !empty($data['HORARIO_TRABAJO']) && $idPersonal > 0) {
      $idHorario = (int)$data['HORARIO_TRABAJO'];
      
      // Actualizar ID_HORARIO en MEDICO si existe
      $sqlUpdateMedico = "UPDATE MEDICO 
                          SET ID_HORARIO = :id_horario 
                          WHERE ID_PERSONAL = :id_personal";
      $stmtUpdateMedico = oci_parse($this->conn, $sqlUpdateMedico);
      oci_bind_by_name($stmtUpdateMedico, ':id_horario', $idHorario);
      oci_bind_by_name($stmtUpdateMedico, ':id_personal', $idPersonal);
      @oci_execute($stmtUpdateMedico);
      oci_free_statement($stmtUpdateMedico);
      oci_commit($this->conn);
    }

    return [
      'resultado' => (int)$resultado,
      'mensaje'   => $mensaje,
    ];
  }

  public function eliminar(int $idPersonal): array
  {
    $sql = "BEGIN
              pkg_personal.eliminar_personal(
                :pin_id_personal,
                :pout_resultado,
                :pout_mensaje
              );
            END;";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_id_personal', $idPersonal);

    $resultado = 0;
    $mensaje   = '';
    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 32);
    oci_bind_by_name($stmt, ':pout_mensaje', $mensaje,4000);
    oci_execute($stmt);
    oci_free_statement($stmt);

    return [
      'resultado' => (int)$resultado,
      'mensaje'   => $mensaje,
    ];
  }

  // Cambio de Adry: Agregado método para obtener horarios de AGENDA_HORARIO
  public function obtenerHorarios(): array
  {
    $sql = "SELECT ID_HORARIO, HORARIO, DIA_SEMANA, TURNO
            FROM AGENDA_HORARIO
            ORDER BY DIA_SEMANA, TURNO";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }

  // Método para obtener detalle completo de un personal
  public function obtenerDetalle(int $id): array
  {
    $sql = "SELECT p.ID_PERSONAL, 
                   p.PRIMER_NOMBRE || ' ' || p.SEGUNDO_NOMBRE || ' ' || p.PRIMER_APELLIDO || ' ' || p.SEGUNDO_APELLIDO AS NOMBRE_COMPLETO,
                   p.PUESTO, p.ACTIVO, p.CORREO_ELECTRONICO, p.TELEFONO, 
                   p.DIRECCION, p.PROVINCIA, p.CANTON, p.DISTRITO, 
                   p.HORARIO_TRABAJO,
                   ah.HORARIO AS HORARIO_TEXTO,
                   c.FECHA_REGISTRO
            FROM Personal p
            LEFT JOIN Contrato c ON p.ID_PERSONAL = c.ID_PERSONAL
            LEFT JOIN AGENDA_HORARIO ah ON p.HORARIO_TRABAJO = ah.ID_HORARIO
            WHERE p.ID_PERSONAL = :id";

    $stmt = oci_parse($this->conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);

    $personal = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$personal) {
      return ['error' => 'Personal no encontrado'];
    }

    // Formatear fecha si existe
    if (!empty($personal['FECHA_REGISTRO'])) {
      $fecha = $personal['FECHA_REGISTRO'];
      if ($fecha instanceof DateTime) {
        $personal['FECHA_REGISTRO'] = $fecha->format('d/m/Y');
      }
    }

    return ['personal' => $personal];
  }

}

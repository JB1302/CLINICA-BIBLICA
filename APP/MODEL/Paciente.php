<?php
require_once __DIR__ . '/../config/database.php';

class Paciente {
  private $conn;

  public function __construct() {
    $this->conn = Database::get();
  }
  // Obtener todos los pacientes
  public function obtenerTodos(): array {
    $sql = "SELECT ID_PACIENTE, CEDULA, PRIMER_NOMBRE, SEGUNDO_NOMBRE,
                   PRIMER_APELLIDO, SEGUNDO_APELLIDO, TO_CHAR(FECHA_NACIMIENTO, 'YYYY-MM-DD') AS FECHA_NACIMIENTO,
                   SEXO, OBSERVACIONES, TELEFONO, DIRECCION, CORREO_ELECTRONICO
            FROM PACIENTE
            ORDER BY ID_PACIENTE DESC";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }
  //  método para crear un nuevo paciente
  public function crear(array $data): array {
    $sql = "
      BEGIN
        pkg_paciente.agregar_paciente(
          :pin_cedula,
          :pin_primer_nombre,
          :pin_segundo_nombre,
          :pin_primer_apellido,
          :pin_segundo_apellido,
          :pin_fecha_nacimiento,
          :pin_sexo,
          :pin_observaciones,
          :pin_telefono,
          :pin_direccion,
          :pin_correo_electronico,
          :pout_resultado,
          :pout_mensaje
        );
      END;
    ";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_cedula', $data['CEDULA']);
    oci_bind_by_name($stmt, ':pin_primer_nombre', $data['PRIMER_NOMBRE']);
    oci_bind_by_name($stmt, ':pin_segundo_nombre', $data['SEGUNDO_NOMBRE']);
    oci_bind_by_name($stmt, ':pin_primer_apellido', $data['PRIMER_APELLIDO']);
    oci_bind_by_name($stmt, ':pin_segundo_apellido', $data['SEGUNDO_APELLIDO']);
    oci_bind_by_name($stmt, ':pin_fecha_nacimiento', $data['FECHA_NACIMIENTO']);
    oci_bind_by_name($stmt, ':pin_sexo', $data['SEXO']);
    oci_bind_by_name($stmt, ':pin_observaciones', $data['OBSERVACIONES']);
    oci_bind_by_name($stmt, ':pin_telefono', $data['TELEFONO']);
    oci_bind_by_name($stmt, ':pin_direccion', $data['DIRECCION']);
    oci_bind_by_name($stmt, ':pin_correo_electronico', $data['CORREO_ELECTRONICO']);

    // OUT
    $resultado = 0;
    $mensaje   = '';

    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 32);
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
      'resultado' => (int)$resultado,
      'mensaje' => $mensaje,
    ];
  }
  // metodo para actualizar un paciente
  public function actualizar(array $data): array {
    $sql = "
      BEGIN
        pkg_paciente.editar_paciente(
          :pin_id,
          :pin_cedula,
          :pin_primer_nombre,
          :pin_segundo_nombre,
          :pin_primer_apellido,
          :pin_segundo_apellido,
          :pin_fecha_nacimiento,
          :pin_sexo,
          :pin_observaciones,
          :pin_telefono,
          :pin_direccion,
          :pin_correo_electronico,
          :pout_resultado,
          :pout_mensaje
        );
      END;
    ";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_id', $data['ID_PACIENTE']);
    oci_bind_by_name($stmt, ':pin_cedula', $data['CEDULA']);
    oci_bind_by_name($stmt, ':pin_primer_nombre', $data['PRIMER_NOMBRE']);
    oci_bind_by_name($stmt, ':pin_segundo_nombre', $data['SEGUNDO_NOMBRE']);
    oci_bind_by_name($stmt, ':pin_primer_apellido', $data['PRIMER_APELLIDO']);
    oci_bind_by_name($stmt, ':pin_segundo_apellido', $data['SEGUNDO_APELLIDO']);
    oci_bind_by_name($stmt, ':pin_fecha_nacimiento', $data['FECHA_NACIMIENTO']);
    oci_bind_by_name($stmt, ':pin_sexo', $data['SEXO']);
    oci_bind_by_name($stmt, ':pin_observaciones', $data['OBSERVACIONES']);
    oci_bind_by_name($stmt, ':pin_telefono', $data['TELEFONO']);
    oci_bind_by_name($stmt, ':pin_direccion', $data['DIRECCION']);
    oci_bind_by_name($stmt, ':pin_correo_electronico', $data['CORREO_ELECTRONICO']);

    // OUT
    $resultado = 0;
    $mensaje   = '';

    oci_bind_by_name($stmt, ':pout_resultado', $resultado, 32);
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
      'resultado' => (int)$resultado,
      'mensaje'   => $mensaje,
    ];
  }
  // metodo para eliminar un paciente
  public function eliminar(int $idPaciente): array {
    $sql = "
      BEGIN
        pkg_paciente.eliminar_paciente(
          :pin_id,
          :pout_resultado,
          :pout_mensaje
        );
      END;
    ";

    $stmt = oci_parse($this->conn, $sql);

    oci_bind_by_name($stmt, ':pin_id', $idPaciente);

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

  // Obtener lista de pacientes ordenados por nombre completo
  public function obtenerTodosOrdenados()
  {
      $sql = "
          SELECT 
              id_paciente,
              primer_nombre,
              segundo_nombre,
              primer_apellido,
              segundo_apellido,
              primer_nombre 
                  || ' ' || NVL(segundo_nombre, '') 
                  || ' ' || primer_apellido 
                  || ' ' || NVL(segundo_apellido, '') 
                  AS nombre_completo
          FROM paciente
          ORDER BY primer_nombre ASC, segundo_nombre ASC
      ";

      $stmt = oci_parse($this->conn, $sql);
      oci_execute($stmt);

      $lista = [];
      while ($row = oci_fetch_assoc($stmt)) {
          $lista[] = array_change_key_case($row, CASE_UPPER);
      }

      oci_free_statement($stmt);
      return $lista;
  }
  //  métodos para vistas específicas
  public function obtenerSoloTelefonoFormato506(): array
  {
      $sql = "
          SELECT *
          FROM v_pacientes_telefono_formato_506
          ORDER BY PRIMER_NOMBRE, PRIMER_APELLIDO
      ";

      $stmt = oci_parse($this->conn, $sql);
      oci_execute($stmt);

      $rows = [];
      while ($r = oci_fetch_assoc($stmt)) {
          $rows[] = $r;
      }

      oci_free_statement($stmt);
      return $rows;
  }
  //  método para listar pacientes por provincias
  public function obtenerSoloProvincia(): array
  {
      $sql = "
          SELECT *
          FROM v_pacientes_direccion_alajuela_heredia
          ORDER BY PRIMER_NOMBRE, PRIMER_APELLIDO
      ";

      $stmt = oci_parse($this->conn, $sql);
      oci_execute($stmt);

      $rows = [];
      while ($r = oci_fetch_assoc($stmt)) {
          $rows[] = $r;
      }

      oci_free_statement($stmt);
      return $rows;
  }

  //  método para listar pacientes que terminan en ia
  public function obtenerSoloIa(): array
  {
      $sql = "
          SELECT *
          FROM v_pacientes_nombre_termina_ia
          ORDER BY PRIMER_NOMBRE, PRIMER_APELLIDO
      ";

      $stmt = oci_parse($this->conn, $sql);
      oci_execute($stmt);

      $rows = [];
      while ($r = oci_fetch_assoc($stmt)) {
          $rows[] = $r;
      }

      oci_free_statement($stmt);
      return $rows;
  }

  //  método para listar pacientes con correo gmail
  public function obtenerSoloGmail(): array
  {
      $sql = "
          SELECT *
          FROM v_pacientes_correo_gmail
          ORDER BY PRIMER_NOMBRE, PRIMER_APELLIDO
      ";

      $stmt = oci_parse($this->conn, $sql);
      oci_execute($stmt);

      $rows = [];
      while ($r = oci_fetch_assoc($stmt)) {
          $rows[] = $r;
      }

      oci_free_statement($stmt);
      return $rows;
  }


}
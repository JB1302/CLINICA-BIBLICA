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
            FROM PACIENTE
            ORDER BY ID_PACIENTE";

    $stmt = oci_parse($this->conn, $sql);
    oci_execute($stmt);

    $rows = [];
    while ($r = oci_fetch_assoc($stmt)) $rows[] = $r;

    oci_free_statement($stmt);
    return $rows;
  }

  public function crear(array $data): bool {
    $sql = "INSERT INTO PACIENTE (
              CEDULA, PRIMER_NOMBRE, SEGUNDO_NOMBRE, PRIMER_APELLIDO, 
              SEGUNDO_APELLIDO, FECHA_NACIMIENTO, SEXO, OBSERVACIONES, 
              TELEFONO, DIRECCION, CORREO_ELECTRONICO
            ) VALUES (
              :cedula, :primer_nombre, :segundo_nombre, :primer_apellido,
              :segundo_apellido, TO_DATE(:fecha_nacimiento, 'YYYY-MM-DD'), 
              :sexo, :observaciones, :telefono, :direccion, :correo_electronico
            )";

    $stmt = oci_parse($this->conn, $sql);
    
    oci_bind_by_name($stmt, ':cedula', $data['CEDULA']);
    oci_bind_by_name($stmt, ':primer_nombre', $data['PRIMER_NOMBRE']);
    oci_bind_by_name($stmt, ':segundo_nombre', $data['SEGUNDO_NOMBRE']);
    oci_bind_by_name($stmt, ':primer_apellido', $data['PRIMER_APELLIDO']);
    oci_bind_by_name($stmt, ':segundo_apellido', $data['SEGUNDO_APELLIDO']);
    oci_bind_by_name($stmt, ':fecha_nacimiento', $data['FECHA_NACIMIENTO']);
    oci_bind_by_name($stmt, ':sexo', $data['SEXO']);
    oci_bind_by_name($stmt, ':observaciones', $data['OBSERVACIONES']);
    oci_bind_by_name($stmt, ':telefono', $data['TELEFONO']);
    oci_bind_by_name($stmt, ':direccion', $data['DIRECCION']);
    oci_bind_by_name($stmt, ':correo_electronico', $data['CORREO_ELECTRONICO']);

    $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    oci_free_statement($stmt);
    
    return $result;
  }

  public function actualizar(int $id, array $data): bool {
    $sql = "UPDATE PACIENTE SET
              CEDULA = :cedula,
              PRIMER_NOMBRE = :primer_nombre,
              SEGUNDO_NOMBRE = :segundo_nombre,
              PRIMER_APELLIDO = :primer_apellido,
              SEGUNDO_APELLIDO = :segundo_apellido,
              FECHA_NACIMIENTO = TO_DATE(:fecha_nacimiento, 'YYYY-MM-DD'),
              SEXO = :sexo,
              OBSERVACIONES = :observaciones,
              TELEFONO = :telefono,
              DIRECCION = :direccion,
              CORREO_ELECTRONICO = :correo_electronico
            WHERE ID_PACIENTE = :id";

    $stmt = oci_parse($this->conn, $sql);
    
    oci_bind_by_name($stmt, ':id', $id);
    oci_bind_by_name($stmt, ':cedula', $data['CEDULA']);
    oci_bind_by_name($stmt, ':primer_nombre', $data['PRIMER_NOMBRE']);
    oci_bind_by_name($stmt, ':segundo_nombre', $data['SEGUNDO_NOMBRE']);
    oci_bind_by_name($stmt, ':primer_apellido', $data['PRIMER_APELLIDO']);
    oci_bind_by_name($stmt, ':segundo_apellido', $data['SEGUNDO_APELLIDO']);
    oci_bind_by_name($stmt, ':fecha_nacimiento', $data['FECHA_NACIMIENTO']);
    oci_bind_by_name($stmt, ':sexo', $data['SEXO']);
    oci_bind_by_name($stmt, ':observaciones', $data['OBSERVACIONES']);
    oci_bind_by_name($stmt, ':telefono', $data['TELEFONO']);
    oci_bind_by_name($stmt, ':direccion', $data['DIRECCION']);
    oci_bind_by_name($stmt, ':correo_electronico', $data['CORREO_ELECTRONICO']);

    $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    oci_free_statement($stmt);
    
    return $result;
  }

  public function eliminar(int $id): bool {
    $sql = "DELETE FROM PACIENTE WHERE ID_PACIENTE = :id";
    
    $stmt = oci_parse($this->conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    
    $result = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    oci_free_statement($stmt);
    
    return $result;
  }
}

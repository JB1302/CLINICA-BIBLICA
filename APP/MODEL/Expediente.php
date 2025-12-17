<?php
require_once __DIR__ . '/../config/database.php';

// Modelo para gestión de expedientes médicos
class Expediente
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::get();
    }

    // Obtener todos los expedientes con JOIN a PACIENTE
    public function obtenerTodos(): array
    {
        $sql = "SELECT
            e.id_expediente,
            e.id_paciente,
            p.cedula,
            p.primer_nombre || ' ' || p.segundo_nombre || ' ' ||
            p.primer_apellido || ' ' || p.segundo_apellido AS nombre_paciente,
            TO_CHAR(e.creado_en, 'DD/MM/YYYY') AS fecha_creacion,
            TO_CHAR(e.creado_en, 'YYYY-MM-DD') AS fecha_creacion_html,
            e.notas,
            p.telefono,
            p.correo_electronico,
            p.direccion,
            TO_CHAR(p.fecha_nacimiento, 'DD/MM/YYYY') AS fecha_nacimiento,
            p.sexo
            FROM expediente e
            JOIN paciente p ON p.id_paciente = e.id_paciente
            ORDER BY e.id_expediente DESC";

        $stmt = oci_parse($this->conn, $sql);
        oci_execute($stmt);

        $rows = [];
        while ($r = oci_fetch_assoc($stmt)) {
            $rows[] = $r;
        }

        oci_free_statement($stmt);
        return $rows;
    }

    // Obtener detalle de expediente por ID con información del paciente
    public function obtenerPorId(int $idExpediente): ?array
    {
        $sql = "SELECT
            e.id_expediente,
            e.id_paciente,
            p.cedula,
            p.primer_nombre || ' ' || p.segundo_nombre || ' ' ||
            p.primer_apellido || ' ' || p.segundo_apellido AS nombre_paciente,
            TO_CHAR(e.creado_en, 'DD/MM/YYYY') AS fecha_creacion,
            TO_CHAR(e.creado_en, 'YYYY-MM-DD') AS fecha_creacion_html,
            e.notas,
            p.telefono,
            p.correo_electronico,
            p.direccion,
            TO_CHAR(p.fecha_nacimiento, 'DD/MM/YYYY') AS fecha_nacimiento,
            p.sexo,
            p.observaciones AS observaciones_paciente
            FROM expediente e
            JOIN paciente p ON p.id_paciente = e.id_paciente
            WHERE e.id_expediente = :id_expediente";

        $stmt = oci_parse($this->conn, $sql);
        oci_bind_by_name($stmt, ':id_expediente', $idExpediente);
        oci_execute($stmt);

        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);

        return $row ?: null;
    }

    //  Obtener historial de citas con JOIN a MEDICO, PERSONAL y ESPECIALIDAD
    public function obtenerCitasPorExpediente(int $idExpediente): array
    {
        $sql = "SELECT
            c.id_cita,
            TO_CHAR(c.fecha, 'DD/MM/YYYY') AS fecha,
            TO_CHAR(c.hora_agendada_inicio, 'HH24:MI') AS hora_inicio,
            TO_CHAR(c.hora_agendada_fin, 'HH24:MI') AS hora_fin,
            per.primer_nombre || ' ' || per.segundo_nombre AS nombre_medico,
            esp.nombre AS especialidad,
            ec.nombre_estado AS estado,
            c.observaciones
            FROM expediente e
            JOIN cita c ON c.id_paciente = e.id_paciente
            JOIN medico m ON m.id_medico = c.id_medico
            JOIN personal per ON per.id_personal = m.id_personal
            LEFT JOIN medico_especialidad me ON me.id_medico = m.id_medico
            LEFT JOIN especialidad esp ON esp.id_especialidad = me.id_especialidad
            JOIN estado_cita ec ON ec.id_estado = c.id_estado
            WHERE e.id_expediente = :id_expediente
            ORDER BY c.fecha DESC, c.hora_agendada_inicio DESC";

        $stmt = oci_parse($this->conn, $sql);
        oci_bind_by_name($stmt, ':id_expediente', $idExpediente);
        oci_execute($stmt);

        $rows = [];
        while ($r = oci_fetch_assoc($stmt)) {
            $rows[] = $r;
        }

        oci_free_statement($stmt);
        return $rows;
    }

    // Crear expediente llamando a pkg_expediente.crear_expediente
    public function crearExpediente(int $idPaciente, ?string $notas = null): array
    {
        // Asegurar que notas no sea null
        if ($notas === null || trim($notas) === '') {
            $notas = 'Expediente creado';
        }
        
        $sql = "
            BEGIN
                pkg_expediente.crear_expediente(
                    :pin_id_paciente,
                    :pin_notas,
                    :pout_resultado,
                    :pout_mensaje
                );
            END;
        ";

        $stmt = oci_parse($this->conn, $sql);

        oci_bind_by_name($stmt, ':pin_id_paciente', $idPaciente);
        oci_bind_by_name($stmt, ':pin_notas', $notas, 100);

        $resultado = 0;
        $mensaje   = '';

        oci_bind_by_name($stmt, ':pout_resultado', $resultado, 10);
        oci_bind_by_name($stmt, ':pout_mensaje', $mensaje, 4000);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return [
            'resultado' => $resultado,
            'mensaje'   => $mensaje,
        ];
    }

    // Actualizar solo las NOTAS del expediente
    public function actualizarExpediente(int $idExpediente, ?string $notas = null): array
    {
        $sql = "
            BEGIN
                pkg_expediente.actualizar_expediente(
                    :pin_id_expediente,
                    :pin_notas,
                    :pout_resultado,
                    :pout_mensaje
                );
            END;
        ";

        $stmt = oci_parse($this->conn, $sql);

        oci_bind_by_name($stmt, ':pin_id_expediente', $idExpediente);
        oci_bind_by_name($stmt, ':pin_notas', $notas, 100);

        $resultado = 0;
        $mensaje   = '';

        oci_bind_by_name($stmt, ':pout_resultado', $resultado, 10);
        oci_bind_by_name($stmt, ':pout_mensaje', $mensaje, 4000);

        oci_execute($stmt);
        oci_free_statement($stmt);

        return [
            'resultado' => $resultado,
            'mensaje'   => $mensaje,
        ];
    }

    //  Listar pacientes que aún no tienen expediente creado
    public function obtenerPacientesSinExpediente(): array
    {
        $sql = "SELECT
            p.id_paciente,
            p.cedula,
            p.primer_nombre || ' ' || p.segundo_nombre || ' ' ||
            p.primer_apellido || ' ' || p.segundo_apellido AS nombre_completo
            FROM paciente p
            WHERE NOT EXISTS (
                SELECT 1 FROM expediente e WHERE e.id_paciente = p.id_paciente
            )
            ORDER BY p.primer_nombre, p.primer_apellido";

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

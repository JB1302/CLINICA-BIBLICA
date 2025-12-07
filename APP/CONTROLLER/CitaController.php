<?php
// Cambio de Adry: Manejo de peticiones AJAX ANTES de cargar dependencias para evitar output
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'obtener_horarios_medico') {
    require_once __DIR__ . '/../MODEL/Cita.php';
    header('Content-Type: application/json');
    
    if (isset($_GET['id_medico'])) {
        $idMedico = (int)$_GET['id_medico'];
        $cita = new Cita();
        $horarios = $cita->obtenerHorariosMedico($idMedico);
        
        echo json_encode([
            'success' => true,
            'horarios' => $horarios
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'mensaje' => 'ID de médico no proporcionado'
        ]);
    }
    exit;
}

require_once __DIR__ . '/../MODEL/Cita.php';
require_once __DIR__ . '/../MODEL/Paciente.php';
require_once __DIR__ . '/../MODEL/Medico.php';


class CitaController {
  public function listarCitas(): array {
    try {
      return (new Cita())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('CitaController: ' . $e->getMessage());
      return [];
    }
  }
  public function listarPacientes(): array
  {
      try {
          $paciente = new Paciente();
          return $paciente->obtenerTodosOrdenados();
      } catch (Throwable $e) {
          error_log('CitaController::listarPacientes: ' . $e->getMessage());
          return [];
      }
  }

  public function listarMedicos(): array
  {
      try {
          $medico = new Medico();
          return $medico->obtenerTodos();
      } catch (Throwable $e) {
          error_log('CitaController::listarMedicos: ' . $e->getMessage());
          return [];
      }
  }


  public function cancelar(array $data): array
  {
      try {
          $cita = new Cita();

          $idCita = (int)$data['id_cita'];
          $idMotivo = isset($data['id_motivo_cancelacion']) && $data['id_motivo_cancelacion'] !== ''
              ? (int)$data['id_motivo_cancelacion']
              : null;

          $observaciones = $data['observaciones'] ?? null;

          return $cita->cancelarCita($idCita, $idMotivo, $observaciones);
      } catch (Throwable $e) {
          error_log('CitaController::cancelar: ' . $e->getMessage());
          return [
              'resultado' => 0,
              'mensaje'   => 'Error interno al cancelar la cita',
          ];
      }
  }
  
  public function listarEstados(): array
  {
      try {
          $cita = new Cita();
          return $cita->obtenerEstadosCita();
      } catch (Throwable $e) {
          error_log('CitaController::listarEstados: ' . $e->getMessage());
          return [];
      }
  }

  // Cambio de Adry: Agregado método para listar motivos de cancelación
  public function listarMotivosCancelacion(): array
  {
      try {
          $cita = new Cita();
          return $cita->obtenerMotivosCancelacion();
      } catch (Throwable $e) {
          error_log('CitaController::listarMotivosCancelacion: ' . $e->getMessage());
          return [];
      }
  }

  // Cambio de Adry: Agregado método para listar clínicas
  public function listarClinicas(): array
  {
      try {
          $cita = new Cita();
          return $cita->obtenerClinicas();
      } catch (Throwable $e) {
          error_log('CitaController::listarClinicas: ' . $e->getMessage());
          return [];
      }
  }

  // Cambio de Adry: Agregado método para listar consultorios por clínica
  public function listarConsultorios(?int $idClinica = null): array
  {
      try {
          $cita = new Cita();
          return $cita->obtenerConsultorios($idClinica);
      } catch (Throwable $e) {
          error_log('CitaController::listarConsultorios: ' . $e->getMessage());
          return [];
      }
  }

  public function crear(array $data): array {
    try {
      $cita = new Cita();

      return $cita->crearCita(
        (int)$data['id_paciente'],
        (int)$data['id_medico'],
        $data['fecha'],
        $data['hora_inicio'],
        $data['hora_fin'],
        (int)$data['id_estado'],
        (int)$data['id_consultorio'],
        $data['observaciones'] ?? null
      );
    } catch (Throwable $e) {
      error_log('CitaController::crear: ' . $e->getMessage());
      return [
        'resultado' => 0,
        'mensaje'   => 'Error interno al crear la cita',
      ];
    }
  }

  public function actualizar(array $data): array {
    try {
      $cita = new Cita();

      return $cita->actualizarCita(
        (int)$data['id_cita'],
        (int)$data['id_paciente'],
        (int)$data['id_medico'],
        $data['fecha'],
        $data['hora_inicio'],
        $data['hora_fin'],
        (int)$data['id_estado'],
        (int)$data['id_consultorio'],
        isset($data['id_motivo_cancelacion']) && $data['id_motivo_cancelacion'] !== ''
          ? (int)$data['id_motivo_cancelacion']
          : null,
        $data['observaciones'] ?? null
      );
    } catch (Throwable $e) {
      error_log('CitaController::actualizar: ' . $e->getMessage());
      return [
        'resultado' => 0,
        'mensaje'   => 'Error interno al actualizar la cita',
      ];
    }
  }

  public function eliminar(int $idCita): array {
    try {
      $cita = new Cita();
      return $cita->eliminarCita($idCita);
    } catch (Throwable $e) {
      error_log('CitaController::eliminar: ' . $e->getMessage());
      return [
        'resultado' => 0,
        'mensaje'   => 'Error interno al eliminar la cita',
      ];
    }
  }

  // Cambio de Adry: Obtener horarios disponibles de un médico
  public function obtenerHorariosMedico(int $idMedico): array {
    try {
      $cita = new Cita();
      return $cita->obtenerHorariosMedico($idMedico);
    } catch (Throwable $e) {
      error_log('CitaController::obtenerHorariosMedico: ' . $e->getMessage());
      return [];
    }
  }
}

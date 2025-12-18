<?php
//  Manejo de peticiones AJAX ANTES de cargar dependencias para evitar output
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
  //  método para listar todas las citas
  public function listarCitas(): array {
    try {
      return (new Cita())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('CitaController: ' . $e->getMessage());
      return [];
    }
  }
  //  método para listar todos los pacientes
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
  //  método para listar todos los médicos
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

  //  método para cancelar una cita
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
  //  método para listar estados de cita
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

  //  método para listar motivos de cancelacion
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

  // método para listar las clínicas
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

  // metodo para listar consultorios por clinica
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
  //  método para crear una nueva cita
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
  //  método para actualizar una cita
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
  //  método para eliminar una cita
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

  // Obtener horarios disponibles de un doctor
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
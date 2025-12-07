<?php
require_once __DIR__ . '/../MODEL/Paciente.php';

class PacienteController {
  public function listarPacientes(): array {
    try {
      return (new Paciente())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('PacienteController: ' . $e->getMessage());
      return [];
    }
  }

  public function crear(array $data): array {
    try {
      return (new Paciente())->crear($data);
    } catch (Throwable $e) {
      error_log('PacienteController::crear: ' . $e->getMessage());
      return ['resultado' => 0, 'mensaje' => 'Error interno al crear paciente'];
    }
  }

  public function actualizar(array $data): array {
    try {
      return (new Paciente())->actualizar($data);
    } catch (Throwable $e) {
      error_log('PacienteController::actualizar: ' . $e->getMessage());
      return ['resultado' => 0, 'mensaje' => 'Error interno al actualizar paciente'];
    }
  }

  public function eliminar(int $id): array {
    try {
      return (new Paciente())->eliminar($id);
    } catch (Throwable $e) {
      error_log('PacienteController::eliminar: ' . $e->getMessage());
      return ['resultado' => 0, 'mensaje' => 'Error interno al eliminar paciente'];
    }
  }
}
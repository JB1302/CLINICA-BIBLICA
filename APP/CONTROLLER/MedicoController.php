<?php
require_once __DIR__ . '/../MODEL/Medico.php';

class MedicoController {

  public function listarMedicos(): array {
    try {
      return (new Medico())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('MedicoController::listarMedicos: ' . $e->getMessage());
      return [];
    }
  }

  public function listarEspecialidades(): array {
    try {
      return (new Medico())->obtenerEspecialidades();
    } catch (Throwable $e) {
      error_log('MedicoController::listarEspecialidades: ' . $e->getMessage());
      return [];
    }
  }
  public function listarPersonalDisponible(): array {
    try {
      return (new Medico())->obtenerPersonalDisponible();
    } catch (Throwable $e) {
      error_log('MedicoController::obtenerPersonalDisponible: ' . $e->getMessage());
      return [];
    }
  }

  // === CREATE ===
  public function crear(array $data): array {
    try {
      return (new Medico())->crear($data);
    } catch (Throwable $e) {
      error_log('MedicoController::crear: ' . $e->getMessage());
      return [
        'resultado' => 0,
        'mensaje'   => 'Error interno al crear médico',
      ];
    }
  }

  // === UPDATE ===
  public function actualizar(array $data): array {
    try {
      return (new Medico())->actualizar($data);
    } catch (Throwable $e) {
      error_log('MedicoController::actualizar: ' . $e->getMessage());
      return [
        'resultado' => 0,
        'mensaje'   => 'Error interno al actualizar médico',
      ];
    }
  }

  // === DELETE ===
  public function eliminar(int $id): array {
    try {
      return (new Medico())->eliminar($id);
    } catch (Throwable $e) {
      error_log('MedicoController::eliminar: ' . $e->getMessage());
      return [
        'resultado' => 0,
        'mensaje'   => 'Error interno al eliminar médico',
      ];
    }
  }
}

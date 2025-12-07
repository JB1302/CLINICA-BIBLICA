<?php
require_once __DIR__ . '/../MODEL/Personal.php';

class PersonalController {
  public function listarPersonals(): array {
    try {
      return (new Personal())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('PersonalController: ' . $e->getMessage());
      return [];
    }
  }

  public function crearPersonal(array $data): array {
    try {
      return (new Personal())->crear($data);
    } catch (Throwable $e) {
      error_log('PersonalController::crear ' . $e->getMessage());
      return [
        'RESULTADO' => 0,
        'MENSAJE' => 'Error al crear el personal.'
      ];
    }
  }

  public function actualizarPersonal(array $data): array {
    try {
      return (new Personal())->actualizar($data);
    } catch (Throwable $e) {
      error_log('PersonalController::actualizar ' . $e->getMessage());
      return [
        'RESULTADO' => 0,
        'MENSAJE' => 'Error al actualizar el personal.'
      ];
    }
  }

  public function eliminarPersonal(int $id): array {
    try {
      return (new Personal())->eliminar($id);
    } catch (Throwable $e) {
      error_log('PersonalController::eliminar ' . $e->getMessage());
      return [
        'RESULTADO' => 0,
        'MENSAJE' => 'Error al eliminar el personal.'
      ];
    }
  }

  // Cambio de Adry: Agregado método para listar horarios de AGENDA_HORARIO
  public function listarHorarios(): array {
    try {
      return (new Personal())->obtenerHorarios();
    } catch (Throwable $e) {
      error_log('PersonalController::listarHorarios ' . $e->getMessage());
      return [];
    }
  }

  // Método para obtener detalle de un personal
  public function obtenerDetalle(): array {
    try {
      $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
      if ($id <= 0) {
        return ['error' => 'ID no válido'];
      }
      return (new Personal())->obtenerDetalle($id);
    } catch (Throwable $e) {
      error_log('PersonalController::obtenerDetalle ' . $e->getMessage());
      return ['error' => 'Error al obtener el detalle'];
    }
  }
}

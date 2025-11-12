<?php
require_once __DIR__ . '/../MODEL/Medico.php';

class MedicoController {
  public function listarMedicos(): array {
    try {
      return (new Medico())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('MedicoController: ' . $e->getMessage());
      return [];
    }
  }

  public function listarEspecialidades(): array {
    try {
      return (new Medico())->obtenerEspecialidades();
    } catch (Throwable $e) {
      error_log('MedicoController (especialidades): ' . $e->getMessage());
      return [];
    }
  }
}

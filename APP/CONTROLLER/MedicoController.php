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
}

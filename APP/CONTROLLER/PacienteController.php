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
}

<?php
require_once __DIR__ . '/../MODEL/Cita.php';

class CitaController {
  public function listarCitas(): array {
    try {
      return (new Cita())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('CitaController: ' . $e->getMessage());
      return [];
    }
  }
}

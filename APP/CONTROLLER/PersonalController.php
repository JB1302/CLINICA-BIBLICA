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
}

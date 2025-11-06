<?php
require_once __DIR__ . '/../models/Paciente.php';

class PacienteController {
  public function listarPacientes() {
    try {
      $pacienteModel = new Paciente();
      return $pacienteModel->obtenerTodos();
    } catch (Exception $e) {
      return [];
    }
  }
}
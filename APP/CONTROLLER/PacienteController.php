<?php
require_once __DIR__ . '/../MODEL/Paciente.php';

class PacienteController {
  //  método para listar todos los pacientes
  public function listarPacientes(): array {
    try {
      return (new Paciente())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('PacienteController: ' . $e->getMessage());
      return [];
    }
  }
  //  método para listar pacientes con correo Gmail
  public function listarPacientesGmail(): array {
    try {
        return (new Paciente())->obtenerSoloGmail();
    } catch (Throwable $e) {
        error_log('PacienteController::listarPacientesGmail: ' . $e->getMessage());
        return [];
    }
  }
  //  método para listar pacientes de que terminen en ia
  public function listarPacientesIa(): array {
    try {
        return (new Paciente())->obtenerSoloIa();
    } catch (Throwable $e) {
        error_log('PacienteController::listarPacientesIa: ' . $e->getMessage());
        return [];
    }
  }
  //  método para listar pacientes de Heredia y Alajuela
  public function listarPacientesProvincia(): array {
    try {
        return (new Paciente())->obtenerSoloProvincia();
    } catch (Throwable $e) {
        error_log('PacienteController::listarPacientesProvincia: ' . $e->getMessage());
        return [];
    }
  }
  //  método para listar pacientes con teléfono en formato +506
  public function listarPacientesTelefono(): array {
    try {
        return (new Paciente())->obtenerSoloTelefonoFormato506();
    } catch (Throwable $e) {
        error_log('PacienteController::listarPacientesTelefono: ' . $e->getMessage());
        return [];
    }
  }
  // metodo para crear un nuevo paciente
  public function crear(array $data): array {
    try {
      return (new Paciente())->crear($data);
    } catch (Throwable $e) {
      error_log('PacienteController::crear: ' . $e->getMessage());
      return ['resultado' => 0, 'mensaje' => 'Error interno al crear paciente'];
    }
  }
  // metodo para actualizar un paciente
  public function actualizar(array $data): array {
    try {
      return (new Paciente())->actualizar($data);
    } catch (Throwable $e) {
      error_log('PacienteController::actualizar: ' . $e->getMessage());
      return ['resultado' => 0, 'mensaje' => 'Error interno al actualizar paciente'];
    }
  }
  // metodo para eliminar un paciente
  public function eliminar(int $id): array {
    try {
      return (new Paciente())->eliminar($id);
    } catch (Throwable $e) {
      error_log('PacienteController::eliminar: ' . $e->getMessage());
      return ['resultado' => 0, 'mensaje' => 'Error interno al eliminar paciente'];
    }
  }
}
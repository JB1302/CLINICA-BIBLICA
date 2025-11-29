<?php
require_once __DIR__ . '/../MODEL/Paciente.php';

class PacienteController {
  public function listarPacientes(): array {
    try {
      return (new Paciente())->obtenerTodos();
    } catch (Throwable $e) {
      error_log('PacienteController listar: ' . $e->getMessage());
      return [];
    }
  }

  public function crear(): void {
    try {
      $data = [
        'CEDULA' => $_POST['CEDULA'] ?? '',
        'PRIMER_NOMBRE' => $_POST['PRIMER_NOMBRE'] ?? '',
        'SEGUNDO_NOMBRE' => $_POST['SEGUNDO_NOMBRE'] ?? '',
        'PRIMER_APELLIDO' => $_POST['PRIMER_APELLIDO'] ?? '',
        'SEGUNDO_APELLIDO' => $_POST['SEGUNDO_APELLIDO'] ?? '',
        'FECHA_NACIMIENTO' => $_POST['FECHA_NACIMIENTO'] ?? '',
        'SEXO' => $_POST['SEXO'] ?? '',
        'OBSERVACIONES' => $_POST['OBSERVACIONES'] ?? '',
        'TELEFONO' => $_POST['TELEFONO'] ?? '',
        'DIRECCION' => $_POST['DIRECCION'] ?? '',
        'CORREO_ELECTRONICO' => $_POST['CORREO_ELECTRONICO'] ?? ''
      ];

      $paciente = new Paciente();
      $resultado = $paciente->crear($data);

      if ($resultado) {
        header('Location: /pacientes.php?success=1');
      } else {
        header('Location: /pacientes.php?error=1');
      }
      exit;
    } catch (Throwable $e) {
      error_log('PacienteController crear: ' . $e->getMessage());
      header('Location: /pacientes.php?error=1');
      exit;
    }
  }

  public function actualizar(): void {
    try {
      $id = (int)($_POST['ID_PACIENTE'] ?? 0);
      
      $data = [
        'CEDULA' => $_POST['CEDULA'] ?? '',
        'PRIMER_NOMBRE' => $_POST['PRIMER_NOMBRE'] ?? '',
        'SEGUNDO_NOMBRE' => $_POST['SEGUNDO_NOMBRE'] ?? '',
        'PRIMER_APELLIDO' => $_POST['PRIMER_APELLIDO'] ?? '',
        'SEGUNDO_APELLIDO' => $_POST['SEGUNDO_APELLIDO'] ?? '',
        'FECHA_NACIMIENTO' => $_POST['FECHA_NACIMIENTO'] ?? '',
        'SEXO' => $_POST['SEXO'] ?? '',
        'OBSERVACIONES' => $_POST['OBSERVACIONES'] ?? '',
        'TELEFONO' => $_POST['TELEFONO'] ?? '',
        'DIRECCION' => $_POST['DIRECCION'] ?? '',
        'CORREO_ELECTRONICO' => $_POST['CORREO_ELECTRONICO'] ?? ''
      ];

      $paciente = new Paciente();
      $resultado = $paciente->actualizar($id, $data);

      if ($resultado) {
        header('Location: /pacientes.php?success=2');
      } else {
        header('Location: /pacientes.php?error=2');
      }
      exit;
    } catch (Throwable $e) {
      error_log('PacienteController actualizar: ' . $e->getMessage());
      header('Location: /pacientes.php?error=2');
      exit;
    }
  }

  public function eliminar(): void {
    try {
      $id = (int)($_POST['ID_PACIENTE'] ?? 0);

      $paciente = new Paciente();
      $resultado = $paciente->eliminar($id);

      if ($resultado) {
        header('Location: /pacientes.php?success=3');
      } else {
        header('Location: /pacientes.php?error=3');
      }
      exit;
    } catch (Throwable $e) {
      error_log('PacienteController eliminar: ' . $e->getMessage());
      header('Location: /pacientes.php?error=3');
      exit;
    }
  }
}

// Procesar acciones si se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $controller = new PacienteController();
  
  switch ($_POST['action']) {
    case 'create':
      $controller->crear();
      break;
    case 'update':
      $controller->actualizar();
      break;
    case 'delete':
      $controller->eliminar();
      break;
  }
}

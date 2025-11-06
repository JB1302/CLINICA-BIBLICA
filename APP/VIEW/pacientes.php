<?php
// --- Cargar el controlador MVC ---
require_once __DIR__ . '/../controllers/PacienteController.php';
require_once __DIR__ . '/../config/database.php';

// Crear instancia del controlador
$controller = new PacienteController();

// Obtener los pacientes desde el controlador (que usa el modelo)
$pacientes = $controller->listarPacientes();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>⚕️ Clínica Bíblica</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="Assets/css/styles.css">
</head>

<body class="d-flex flex-column min-vh-100">

  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#4986b2;">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center" href="/home.php">
        ⚕️ Clinica Biblica
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="/home.php">Inicio |</a></li>
          <li class="nav-item"><a class="nav-link active" href="/pacientes.php">Pacientes |</a></li>
          <li class="nav-item"><a class="nav-link active" href="/citas.php">Citas |</a></li>
          <li class="nav-item"><a class="nav-link active" href="/medicos.php">Médicos |</a></li>
          <li class="nav-item"><a class="nav-link active" href="/reportes.php">Reportes</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-primary"><i class="fa-solid fa-user me-2"></i>Pacientes</h2>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
        <i class="fa-solid fa-plus me-1"></i> Nuevo Paciente
      </button>
    </div>

    <!-- Tabla de pacientes -->
    <div class="table-responsive shadow-sm">
      <table class="table table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Identificación</th>
            <th>Teléfono</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($pacientes)): ?>
            <?php foreach ($pacientes as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['ID_PACIENTE']) ?></td>
                <td><?= htmlspecialchars($p['PRIMER_NOMBRE'] . ' ' . $p['PRIMER_APELLIDO']) ?></td>
                <td><?= htmlspecialchars($p['CEDULA']) ?></td>
                <td><?= htmlspecialchars($p['TELEFONO']) ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning me-1">
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <button class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" class="text-center text-muted">No hay pacientes registrados</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <footer class="py-4 mt-auto bg-dark text-white text-center">
    <p class="mb-0 small">© 2025 Clínica Bíblica — Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
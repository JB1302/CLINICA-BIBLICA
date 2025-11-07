<?php
require_once __DIR__ . '/../CONTROLLER/CitaController.php';

$controller = new CitaController();
$pacientes = $controller->listarCitas();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>⚕️ Clinica Biblica</title>


  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

  <!-- Favicon -->
  <link rel="icon" href="https://getbootstrap.com/docs/5.3/assets/brand/bootstrap-logo.svg">

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


          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
              <i class="fa-solid fa-user-circle fs-5"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="/login.php">Iniciar sesión</a></li>
              <li><a class="dropdown-item" href="/register.php">Registrarse</a></li>
            </ul>
          </li>

        </ul>
      </div>
    </div>
  </nav>



  <main class="container py-5 px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-primary"><i class="fa-solid fa-calendar-check me-2"></i>Citas</h2>

      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
        <i class="fa-solid fa-plus me-1"></i> Nueva cita
      </button>
    </div>



    <div class="table-responsive shadow-sm">
      <table class="table table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>ID Cita</th>
            <th>Fecha</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>

            <th>Paciente</th>
            <th>Médico</th>
            <th>Especialidad</th>

            <th>Consultorio</th>
            <th>Clínica</th>

            <th>Estado</th>
            <th>Motivo Cancelación</th>

            <th>Turno</th>
            <th>Observaciones</th>

            <th class="text-center">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($pacientes)): ?>
            <?php foreach ($pacientes as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['ID_CITA'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['FECHA'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['HORA_INICIO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['HORA_FIN'] ?? '') ?></td>

                <td><?= htmlspecialchars($p['PACIENTE'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['NOMBRE_MEDICO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['ESPECIALIDAD'] ?? '') ?></td>

                <td><?= htmlspecialchars($p['CONSULTORIO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['CLINICA'] ?? '') ?></td>

                <td><?= htmlspecialchars($p['ESTADO_CITA'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['MOTIVO_CANCELACION'] ?? '') ?></td>

                <td><?= htmlspecialchars($p['TURNO_INICIO'] ?? '') ?> - <?= htmlspecialchars($p['TURNO_FIN'] ?? '') ?></td>

                <td><?= htmlspecialchars($p['OBSERVACIONES'] ?? '') ?></td>

                <td class="text-center">
                  <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditar">
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <button class="btn btn-sm btn-danger">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="14" class="text-center text-muted">No hay citas registradas</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  </main>

<!-- MODAL: NUEVA CITA -->
<div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-labelledby="lblNuevaCita" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="lblNuevaCita">
          <i class="fa-solid fa-plus me-2"></i>Nueva cita
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formNuevaCita" method="post" action="/APP/CONTROLLER/CitaController.php">
          <input type="hidden" name="action" value="crear">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Paciente</label>
              <select class="form-select" name="ID_PACIENTE" required>
                <option value="">Selecciona...</option>
                <!-- Opciones desde PHP -->
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Médico</label>
              <select class="form-select" name="ID_MEDICO" required>
                <option value="">Selecciona...</option>
                <!-- Opciones desde PHP -->
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Fecha</label>
              <input type="date" class="form-control" name="FECHA" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Hora inicio</label>
              <input type="time" class="form-control" name="HORA_INICIO" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Hora fin</label>
              <input type="time" class="form-control" name="HORA_FIN" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Estado</label>
              <select class="form-select" name="ESTADO_CITA" required>
                <option value="Pendiente">Pendiente</option>
                <option value="Confirmada">Confirmada</option>
                <option value="Atendida">Atendida</option>
                <option value="Cancelada">Cancelada</option>
                <option value="No asistió">No asistió</option>
              </select>
            </div>

            <div class="col-md-8">
              <label class="form-label">Motivo de cancelación</label>
              <textarea class="form-control" name="MOTIVO_CANCELACION" rows="2" placeholder="Solo si aplica"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Observaciones</label>
              <textarea class="form-control" name="OBSERVACIONES" rows="2"></textarea>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">Guardar cita</button>
            <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- MODAL: EDITAR CITA -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="lblEditarCita" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title text-dark" id="lblEditarCita">
          <i class="fa-solid fa-pen me-2"></i>Editar cita
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formEditarCita" method="post" action="/APP/CONTROLLER/CitaController.php">
          <input type="hidden" name="action" value="actualizar">
          <input type="hidden" name="ID_CITA" value="">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Paciente</label>
              <select class="form-select" name="ID_PACIENTE" required>
                <!-- Opciones desde PHP -->
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Médico</label>
              <select class="form-select" name="ID_MEDICO" required>
                <!-- Opciones desde PHP -->
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Fecha</label>
              <input type="date" class="form-control" name="FECHA" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Hora inicio</label>
              <input type="time" class="form-control" name="HORA_INICIO" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Hora fin</label>
              <input type="time" class="form-control" name="HORA_FIN" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Estado</label>
              <select class="form-select" name="ESTADO_CITA" required>
                <option value="Pendiente">Pendiente</option>
                <option value="Confirmada">Confirmada</option>
                <option value="Atendida">Atendida</option>
                <option value="Cancelada">Cancelada</option>
                <option value="No asistió">No asistió</option>
              </select>
            </div>

            <div class="col-md-8">
              <label class="form-label">Motivo de cancelación</label>
              <textarea class="form-control" name="MOTIVO_CANCELACION" rows="2" placeholder="Solo si aplica"></textarea>
            </div>

            <div class="col-12">
              <label class="form-label">Observaciones</label>
              <textarea class="form-control" name="OBSERVACIONES" rows="2"></textarea>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-warning flex-fill">Actualizar cita</button>
            <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<!-- MODAL: CANCELAR CITA -->
<div class="modal fade" id="modalCancelarCita" tabindex="-1" aria-labelledby="lblCancelarCita" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="lblCancelarCita">
          <i class="fa-solid fa-ban me-2"></i>Cancelar cita
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formCancelarCita" method="post" action="/APP/CONTROLLER/CitaController.php">
          <input type="hidden" name="action" value="cancelar">
          <input type="hidden" name="ID_CITA" value="">

          <p class="mb-3">Confirmá la cancelación de la cita <strong>#</strong>.</p>
          <div class="mb-3">
            <label class="form-label">Motivo de cancelación</label>
            <textarea class="form-control" name="MOTIVO_CANCELACION" rows="3" required></textarea>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-danger flex-fill">Cancelar cita</button>
            <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>





  <footer class="py-5 mt-auto" style=" color:#fff;">
    <div class="container">
      <div class="row align-items-center">

        <div class="col-md-6 text-md-start text-center mb-4 mb-md-0">
          <p class="mb-1">Correo: <a href="mailto:titulo@correo.com" class="text-light text-decoration-none">titulo@correo.com</a></p>
          <p class="mb-1">Teléfono: 555-555-555</p>
          <p class="mb-0">Dirección: 1234, Calle Principal, Ciudad Real</p>
        </div>

        <div class="col-md-6 text-md-end text-center">
          <div class="mb-3">
            <a href="#" class="me-3 text-light"><i class="fab fa-twitter fa-lg"></i></a>
            <a href="#" class="me-3 text-light"><i class="fab fa-instagram fa-lg"></i></a>
            <a href="#" class="text-light"><i class="fab fa-facebook fa-lg"></i></a>
          </div>

          <form class="d-inline-flex justify-content-center justify-content-md-end">
            <input type="email" class="form-control bg-dark text-white border-light me-2" placeholder="Email" style="max-width: 220px;">
            <button class="btn btn-outline-light" type="submit">Suscribirse</button>
          </form>
        </div>
      </div>

      <div class="text-center mt-3 mb-2 small">
        <a href="#" class="text-light text-decoration-none me-3">FAQ</a>
        <a href="#" class="text-light text-decoration-none me-3">SETTINGS</a>
      </div>

      <hr class="border-light mt-3">

      <p class="text-center mb-0 small">© 2025 Clinica Biblica. Todos los derechos reservados.</p>
    </div>
  </footer>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
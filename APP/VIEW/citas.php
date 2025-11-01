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
              <li><a class="dropdown-item" href="/views/login.php">Iniciar sesión</a></li>
              <li><a class="dropdown-item" href="/views/register.php">Registrarse</a></li>
            </ul>
          </li>

        </ul>
      </div>
    </div>
  </nav>




<main class="container py-5">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-calendar-check me-2"></i>Citas</h2>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
      <i class="fa-solid fa-plus me-1"></i> Nueva cita
    </button>
  </div>

  <!-- FILTROS -->
  <form class="row g-2 mb-4">
    <div class="col-md-3">
      <label class="form-label small mb-1">Estado</label>
      <select class="form-select" name="estado">
        <option value="">Todos</option>
        <option>Pendiente</option>
        <option>Confirmada</option>
        <option>Atendida</option>
        <option>Cancelada</option>
        <option>No asistió</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small mb-1">Fecha desde</label>
      <input type="date" class="form-control" name="desde">
    </div>
    <div class="col-md-3">
      <label class="form-label small mb-1">Fecha hasta</label>
      <input type="date" class="form-control" name="hasta">
    </div>
    <div class="col-md-3 d-grid align-self-end">
      <button class="btn btn-secondary"><i class="fa-solid fa-filter me-1"></i> Aplicar filtros</button>
    </div>
  </form>

  <!-- TABLA -->
  <div class="table-responsive shadow-sm">
    <table class="table table-hover align-middle">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>Paciente</th>
          <th>Médico</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Estado</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <!-- Filas de ejemplo (luego reemplaza con PHP) -->
        <tr>
          <td>101</td>
          <td data-paciente-id="1">Ana Rodríguez</td>
          <td data-medico-id="7">Dr. Quesada</td>
          <td>2025-11-05</td>
          <td>09:30</td>
          <td>
            <span class="badge bg-warning text-dark">Pendiente</span>
          </td>
          <td class="text-center">
            <button class="btn btn-sm btn-warning me-1 btn-editar"
              data-id="101"
              data-paciente="1"
              data-medico="7"
              data-fecha="2025-11-05"
              data-hora="09:30"
              data-estado="Pendiente"
              data-bs-toggle="modal" data-bs-target="#modalEditarCita">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button class="btn btn-sm btn-danger btn-cancelar"
              data-id="101"
              data-bs-toggle="modal" data-bs-target="#modalCancelarCita">
              <i class="fa-solid fa-ban"></i>
            </button>
          </td>
        </tr>

        <tr>
          <td>102</td>
          <td data-paciente-id="2">Luis Pérez</td>
          <td data-medico-id="3">Dra. Solano</td>
          <td>2025-11-06</td>
          <td>14:00</td>
          <td>
            <span class="badge bg-success">Confirmada</span>
          </td>
          <td class="text-center">
            <button class="btn btn-sm btn-warning me-1 btn-editar"
              data-id="102"
              data-paciente="2"
              data-medico="3"
              data-fecha="2025-11-06"
              data-hora="14:00"
              data-estado="Confirmada"
              data-bs-toggle="modal" data-bs-target="#modalEditarCita">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button class="btn btn-sm btn-danger btn-cancelar"
              data-id="102"
              data-bs-toggle="modal" data-bs-target="#modalCancelarCita">
              <i class="fa-solid fa-ban"></i>
            </button>
          </td>
        </tr>

      </tbody>
    </table>
  </div>
</main>

<!-- MODAL: NUEVA CITA -->
<div class="modal fade" id="modalNuevaCita" tabindex="-1" aria-labelledby="lblNuevaCita" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="lblNuevaCita"><i class="fa-solid fa-plus me-2"></i>Nueva cita</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formNuevaCita">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Paciente</label>
              <select class="form-select" name="paciente" required>
                <option value="">Selecciona...</option>
                <option value="1">Ana Rodríguez</option>
                <option value="2">Luis Pérez</option>
                <!-- cargar desde DB -->
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Médico</label>
              <select class="form-select" name="medico" required>
                <option value="">Selecciona...</option>
                <option value="7">Dr. Quesada</option>
                <option value="3">Dra. Solano</option>
                <!-- cargar desde DB -->
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Fecha</label>
              <input type="date" class="form-control" name="fecha" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Hora</label>
              <input type="time" class="form-control" name="hora" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Estado</label>
              <select class="form-select" name="estado" required>
                <option>Pendiente</option>
                <option>Confirmada</option>
                <option>Atendida</option>
                <option>Cancelada</option>
                <option>No asistió</option>
              </select>
            </div>

            <div class="col-12 d-none" id="wrapMotivoNuevo">
              <label class="form-label">Motivo de cancelación</label>
              <textarea class="form-control" name="motivo" rows="2" placeholder="Motivo..." ></textarea>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary w-100">Guardar cita</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL: EDITAR / REPROGRAMAR -->
<div class="modal fade" id="modalEditarCita" tabindex="-1" aria-labelledby="lblEditarCita" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title text-dark" id="lblEditarCita"><i class="fa-solid fa-pen me-2"></i>Editar cita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formEditarCita">
          <input type="hidden" name="id" id="editId">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Paciente</label>
              <select class="form-select" name="paciente" id="editPaciente" required>
                <option value="1">Ana Rodríguez</option>
                <option value="2">Luis Pérez</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Médico</label>
              <select class="form-select" name="medico" id="editMedico" required>
                <option value="7">Dr. Quesada</option>
                <option value="3">Dra. Solano</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Fecha</label>
              <input type="date" class="form-control" name="fecha" id="editFecha" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Hora</label>
              <input type="time" class="form-control" name="hora" id="editHora" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Estado</label>
              <select class="form-select" name="estado" id="editEstado" required>
                <option>Pendiente</option>
                <option>Confirmada</option>
                <option>Atendida</option>
                <option>Cancelada</option>
                <option>No asistió</option>
              </select>
            </div>

            <div class="col-12 d-none" id="wrapMotivoEditar">
              <label class="form-label">Motivo de cancelación</label>
              <textarea class="form-control" name="motivo" id="editMotivo" rows="2" placeholder="Motivo..."></textarea>
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-warning w-100">Actualizar cita</button>
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
        <h5 class="modal-title" id="lblCancelarCita"><i class="fa-solid fa-ban me-2"></i>Cancelar cita</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="formCancelarCita">
          <input type="hidden" name="id" id="cancelId">
          <p class="mb-3">Confirma la cancelación de la cita <strong id="cancelTextoId">#</strong>. Indica el motivo:</p>
          <div class="mb-3">
            <label class="form-label">Motivo de cancelación</label>
            <textarea class="form-control" name="motivo" id="cancelMotivo" rows="3" placeholder="Motivo..." required></textarea>
          </div>
          <button type="submit" class="btn btn-danger w-100">Cancelar cita</button>
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
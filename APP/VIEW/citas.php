<?php
require_once __DIR__ . '/../CONTROLLER/CitaController.php';

$controller = new CitaController();
// Manejo de solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
          // Crear nueva cita
          $res = $controller->crear($_POST);
          break;

        case 'update':
          // Actualizar cita existente
          $res = $controller->actualizar($_POST);
          break;

        case 'delete':
          // Eliminar cita
          $id = isset($_POST['id_cita']) ? (int)$_POST['id_cita'] : 0;
          $res = $controller->eliminar($id);
          break;

        case 'cancel':
          // Cancelar cita
          $res = $controller->cancelar($_POST);
          break;

        default:
          $res = ['resultado' => 0, 'mensaje' => 'Acción no válida'];
          break;
    }
    // Redirigir con mensaje
    header(
        'Location: citas.php?msg=' . urlencode($res['mensaje'])
        . '&ok=' . (int)($res['resultado'] ?? 0)
    );
    exit;
}
// Obtener datos para mostrar en la vista
$citas = $controller->listarCitas();
$estados = $controller->listarEstados();
$pacientes = $controller->listarPacientes();
$medicos = $controller->listarMedicos();
$motivosCancelacion = $controller->listarMotivosCancelacion();
$clinicas = $controller->listarClinicas();
$consultorios = $controller->listarConsultorios();
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
          <li class="nav-item"><a class="nav-link active" href="/expediente.php">Expedientes |</a></li>
          <li class="nav-item"><a class="nav-link active" href="/citas.php">Citas |</a></li>
          <li class="nav-item"><a class="nav-link active" href="/medicos.php">Médicos |</a></li>
          <li class="nav-item"><a class="nav-link active" href="/personal.php">Personal |</a></li>
          <li class="nav-item"><a class="nav-link active" href="/reportes.php">Reportes</a></li>
        </ul>
      </div>


    </div>
  </nav>




  <main class="container py-5">
    <?php if (isset($_GET['msg'])): ?>
      <div id="alertGlobal"
          class="alert alert-<?= ($_GET['ok'] ?? '0') == '1' ? 'success' : 'danger' ?> alert-dismissible fade show"
          role="alert">
        <?= htmlspecialchars($_GET['msg']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-calendar-check me-2"></i>Gestión de Citas
          </h3>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaCita">
            <i class="fa-solid fa-plus me-1"></i> Nueva Cita
          </button>
        </div>
      </div>
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

            <!-- <th>Turno</th> -->
            <th>Observaciones</th>

            <th class="text-center">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($citas)): ?>
            <?php foreach ($citas as $c): ?>
              <tr>
                <td><?= htmlspecialchars($c['ID_CITA'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['FECHA'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['HORA_INICIO'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['HORA_FIN'] ?? '') ?></td>

                <td><?= htmlspecialchars($c['PACIENTE'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['NOMBRE_MEDICO'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['ESPECIALIDAD'] ?? '') ?></td>

                <td><?= htmlspecialchars($c['CONSULTORIO'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['CLINICA'] ?? '') ?></td>

                <td><?= htmlspecialchars($c['ESTADO_CITA'] ?? '') ?></td>
                <td><?= htmlspecialchars($c['MOTIVO_CANCELACION'] ?? '') ?></td>

                <!-- <td><?= htmlspecialchars($c['TURNO_INICIO'] ?? '') ?> - <?= htmlspecialchars($c['TURNO_FIN'] ?? '') ?></td> -->

                <td><?= htmlspecialchars($c['OBSERVACIONES'] ?? '') ?></td>

                <td class="text-center">
                  <button
                    class="btn btn-sm btn-warning me-1"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                    data-id_cita="<?= htmlspecialchars($c['ID_CITA'] ?? '') ?>"
                    data-id_paciente="<?= htmlspecialchars($c['ID_PACIENTE'] ?? '') ?>"
                    data-id_medico="<?= htmlspecialchars($c['ID_MEDICO'] ?? '') ?>"
                     data-id_estado="<?= htmlspecialchars($c['ESTADO'] ?? '') ?>"
                    data-id_clinica="<?= htmlspecialchars($c['ID_CLINICA'] ?? '') ?>"
                    data-id_consultorio="<?= htmlspecialchars($c['ID_CONSULTORIO'] ?? '') ?>"
                    data-fecha="<?= htmlspecialchars($c['FECHA_HTML'] ?? '') ?>"
                    data-hora_inicio="<?= htmlspecialchars($c['HORA_INICIO_HTML'] ?? $c['HORA_INICIO'] ?? '') ?>"
                    data-hora_fin="<?= htmlspecialchars($c['HORA_FIN_HTML'] ?? $c['HORA_FIN'] ?? '') ?>"
                    data-observaciones="<?= htmlspecialchars($c['OBSERVACIONES'] ?? '') ?>"
                  >
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#modalCancelarCita"
                    data-id="<?= htmlspecialchars($c['ID_CITA'] ?? '') ?>"
                    data-nombre="<?= htmlspecialchars($c['PACIENTE'] ?? '') ?>">
                    <i class="fa-solid fa-ban"></i>
                  </button>
                  <button
                    class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEliminarCita"
                    data-id="<?= htmlspecialchars($c['ID_CITA'] ?? '') ?>"
                    data-nombre="<?= htmlspecialchars($c['PACIENTE'] ?? '') ?>">
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
          <form id="formNuevaCita" method="post" action="citas.php">
            <input type="hidden" name="action" value="create">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Paciente</label>
                <select class="form-select" name="id_paciente" required>
                  <option value="">Selecciona...</option>
                  <?php foreach ($pacientes as $p): ?>
                    <option value="<?= htmlspecialchars($p['ID_PACIENTE']) ?>">
                      <?= htmlspecialchars($p['NOMBRE_COMPLETO']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Médico</label>
                <select class="form-select" name="id_medico" id="nuevo-medico" required>
                  <option value="">Selecciona...</option>
                  <?php foreach ($medicos as $m): ?>
                    <option value="<?= htmlspecialchars($m['ID_MEDICO']) ?>">
                      <?= htmlspecialchars($m['NOMBRE_MEDICO']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12">
                <div id="horarios-info-nuevo" class="alert alert-info d-none">
                  <strong><i class="fa-solid fa-clock me-1"></i> Horarios disponibles:</strong>
                  <div id="horarios-texto-nuevo" class="mt-2"></div>
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" name="fecha" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Hora inicio</label>
                <input type="time" class="form-control" name="hora_inicio" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Hora fin</label>
                <input type="time" class="form-control" name="hora_fin" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Clínica</label>
                <select class="form-select" name="id_clinica" id="nuevo-clinica" required>
                  <option value="">Selecciona...</option>
                  <?php foreach ($clinicas as $cl): ?>
                    <option value="<?= htmlspecialchars($cl['ID_CLINICA']) ?>">
                      <?= htmlspecialchars($cl['NOMBRE']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Consultorio</label>
                <select class="form-select" name="id_consultorio" id="nuevo-consultorio" required>
                  <option value="">Selecciona clínica primero...</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Estado</label>
                <select class="form-select" name="id_estado" required>
                  <option value="">Selecciona...</option>
                  <?php foreach ($estados as $e): ?>
                    <option value="<?= htmlspecialchars($e['ID_ESTADO']) ?>" <?= (strtolower($e['NOMBRE_ESTADO']) == 'programada') ? 'selected' : '' ?>>
                      <?= htmlspecialchars($e['NOMBRE_ESTADO']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" name="observaciones" rows="2"></textarea>
              </div>
            </div>

            <div id="alertaNuevaCita" class="alert alert-danger d-none mt-3" role="alert"></div>
            <div id="alertaErrorCita" class="alert alert-danger d-none mt-3" role="alert"></div>

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
          <form id="formEditarCita" method="post" action="citas.php">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id_cita" value="">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Paciente</label>
                <select class="form-select" name="id_paciente" required>
                  <?php foreach ($pacientes as $p): ?>
                    <option value="<?= htmlspecialchars($p['ID_PACIENTE']) ?>">
                      <?= htmlspecialchars($p['NOMBRE_COMPLETO']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Médico</label>
                <select class="form-select" name="id_medico" id="editar-medico" required>
                  <?php foreach ($medicos as $m): ?>
                    <option value="<?= htmlspecialchars($m['ID_MEDICO']) ?>">
                      <?= htmlspecialchars($m['NOMBRE_MEDICO']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-12">
                <div id="horarios-info-editar" class="alert alert-info d-none">
                  <strong><i class="fa-solid fa-clock me-1"></i> Horarios disponibles:</strong>
                  <div id="horarios-texto-editar" class="mt-2"></div>
                </div>
              </div>

              <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" name="fecha" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Hora inicio</label>
                <input type="time" class="form-control" name="hora_inicio" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Hora fin</label>
                <input type="time" class="form-control" name="hora_fin" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Clínica</label>
                <select class="form-select" name="id_clinica" id="editar-clinica" required>
                  <option value="">Selecciona...</option>
                  <?php foreach ($clinicas as $cl): ?>
                    <option value="<?= htmlspecialchars($cl['ID_CLINICA']) ?>">
                      <?= htmlspecialchars($cl['NOMBRE']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Consultorio</label>
                <select class="form-select" name="id_consultorio" id="editar-consultorio" required>
                  <option value="">Selecciona clínica primero...</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select class="form-select" name="id_estado" required>
                  <option value="">Selecciona...</option>
                  <?php foreach ($estados as $e): ?>
                    <option value="<?= htmlspecialchars($e['ID_ESTADO']) ?>">
                      <?= htmlspecialchars($e['NOMBRE_ESTADO']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-8">
                <label class="form-label">Motivo de cancelación</label>
                <textarea class="form-control" name="id_motivo_cancelacion" rows="2" placeholder="Solo si aplica"></textarea>
              </div>

              <div class="col-12">
                <label class="form-label">Observaciones</label>
                <textarea class="form-control" name="observaciones" rows="2"></textarea>
              </div>
            </div>

            <div id="alertaEditarCita" class="alert alert-danger d-none mt-3" role="alert"></div>

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
          <form id="formCancelarCita" method="post" action="citas.php">
            <input type="hidden" name="action" value="cancel">
            <input type="hidden" name="id_cita" value="">

            <div class="mb-3">
              <label class="form-label">Motivo de cancelación <span class="text-danger">*</span></label>
              <select class="form-select" name="id_motivo_cancelacion" required>
                <option value="">Seleccione un motivo...</option>
                <?php foreach ($motivosCancelacion as $motivo): ?>
                  <option value="<?= $motivo['ID_MOTIVO_CANCELACION'] ?>">
                    <?= htmlspecialchars($motivo['NOMBRE']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Observaciones adicionales</label>
              <textarea class="form-control" name="observaciones" rows="3" placeholder="Opcional - Agregue detalles adicionales si lo desea"></textarea>
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

  <!-- MODAL: ELIMINAR CITA -->
  <div class="modal fade" id="modalEliminarCita" tabindex="-1" aria-labelledby="lblEliminarCita" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title" id="lblEliminarCita">
            <i class="fa-solid fa-trash me-2"></i>Eliminar cita
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="formEliminarCita" method="post" action="citas.php">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id_cita" value="">

            <p class="mb-3">
              ¿Seguro que desea <strong>eliminar</strong> la cita
              <strong id="textoCitaEliminar"></strong>?
              <br>
              <small class="text-muted">
                Esta acción elimina la cita definitivamente (solo es posible si no tiene atenciones registradas).
              </small>
            </p>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-dark flex-fill">Sí, eliminar</button>
              <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-dismiss="modal">Cancelar</button>
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
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Modal CANCELAR
      const modalCancelar = document.getElementById('modalCancelarCita');
      if (modalCancelar) {
        modalCancelar.addEventListener('show.bs.modal', event => {
          const button = event.relatedTarget;
          const idCita = button.getAttribute('data-id');
          const nombre = button.getAttribute('data-nombre');

          const form = document.getElementById('formCancelarCita');
          if (form) {
            form.querySelector('input[name="id_cita"]').value = idCita;
          }

          const p = form ? form.querySelector('p strong') : null;
          if (p) p.textContent = '#' + idCita + ' - ' + nombre;
        });
      }

      // Modal ELIMINAR
      const modalEliminar = document.getElementById('modalEliminarCita');
      if (modalEliminar) {
        modalEliminar.addEventListener('show.bs.modal', event => {
          const button = event.relatedTarget;
          const idCita = button.getAttribute('data-id');
          const nombre = button.getAttribute('data-nombre');

          const form = document.getElementById('formEliminarCita');
          if (form) {
            form.querySelector('input[name="id_cita"]').value = idCita;
          }

          const span = document.getElementById('textoCitaEliminar');
          if (span) span.textContent = '#' + idCita + ' - ' + nombre;
        });
      }
    });
  </script>

  <script>
    const modalEditar = document.getElementById('modalEditar');

    if (modalEditar) {
      // Cuando se abre: llenamos datos
      modalEditar.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // botón que abrió el modal
        const form = modalEditar.querySelector('#formEditarCita');

        const idCita       = button.getAttribute('data-id_cita');
        const idPaciente   = button.getAttribute('data-id_paciente');
        const idMedico     = button.getAttribute('data-id_medico');
        const idEstado     = button.getAttribute('data-id_estado');
        const idClinica    = button.getAttribute('data-id_clinica');
        const idConsultorio = button.getAttribute('data-id_consultorio');
        const fecha        = button.getAttribute('data-fecha');
        const horaInicio   = button.getAttribute('data-hora_inicio');
        const horaFin      = button.getAttribute('data-hora_fin');
        const observ       = button.getAttribute('data-observaciones');

        form.querySelector('input[name="id_cita"]').value      = idCita;
        form.querySelector('select[name="id_paciente"]').value = idPaciente;
        form.querySelector('select[name="id_medico"]').value   = idMedico;
        form.querySelector('input[name="fecha"]').value        = fecha;
        form.querySelector('input[name="hora_inicio"]').value  = horaInicio;
        form.querySelector('input[name="hora_fin"]').value     = horaFin;
        form.querySelector('select[name="id_estado"]').value   = idEstado;
        form.querySelector('textarea[name="observaciones"]').value = observ ?? '';

        // Clínica / consultorio
        form.querySelector('select[name="id_clinica"]').value = idClinica;
        if (idClinica) {
          cargarConsultorios(idClinica, 'editar-consultorio', idConsultorio);
        }

        // Horarios del médico
        if (idMedico) {
          cargarHorariosMedico(idMedico, 'editar');
        }

        // IMPORTANTE: al abrir, ocultar cualquier error viejo
        const alertaEditar = document.getElementById('alertaEditarCita');
        if (alertaEditar) {
          alertaEditar.classList.add('d-none');
          alertaEditar.textContent = '';
        }
      });

      // Cuando se cierra: limpiar errores y (si quieres) el formulario
      modalEditar.addEventListener('hidden.bs.modal', function () {
        const form = document.getElementById('formEditarCita');
        if (form) {
          form.reset(); // si prefieres no resetear, puedes comentar esta línea
        }

        const alertaEditar = document.getElementById('alertaEditarCita');
        if (alertaEditar) {
          alertaEditar.classList.add('d-none');
          alertaEditar.textContent = '';
        }

        // Ocultar info de horarios
        const infoHorarios = document.getElementById('horarios-info-editar');
        if (infoHorarios) {
          infoHorarios.classList.add('d-none');
        }

        // Borrar la alerta de cambio de horario si existe
        const alertaCambio = modalEditar.querySelector('.alerta-cambio-horario');
        if (alertaCambio) {
          alertaCambio.remove();
        }
      });
    }
  </script>

  <script>
    // Variables globales para almacenar horarios
    let horariosNuevo = null;
    let horariosEditar = null;
    
    // Función para cargar horarios del médico vía AJAX
    function cargarHorariosMedico(idMedico, tipo) {
      if (!idMedico) {
        document.getElementById('horarios-info-' + tipo).classList.add('d-none');
        if (tipo === 'nuevo') horariosNuevo = null;
        if (tipo === 'editar') horariosEditar = null;
        return;
      }

      const url = 'test_horarios.php?id_medico=' + idMedico;
      console.log('Fetching URL:', url);
      
      fetch(url)
        .then(response => {
          console.log('Response status:', response.status);
          return response.text();
        })
        .then(text => {
          console.log('Response text:', text);
          return JSON.parse(text);
        })
        .then(data => {
          console.log('Datos parseados:', data);
          const infoDiv = document.getElementById('horarios-info-' + tipo);
          const textoDiv = document.getElementById('horarios-texto-' + tipo);
          
          if (data.success && data.horarios && data.horarios.length > 0) {
            const horario = data.horarios[0];
            
            // Guardar horarios según tipo
            if (tipo === 'nuevo') horariosNuevo = horario;
            if (tipo === 'editar') horariosEditar = horario;
            
            let horariosHtml = '<strong>Horario del médico:</strong> ';
            horariosHtml += horario.HORARIO + ' (' + horario.HORA_INICIO + ' - ' + horario.HORA_FIN + ')';
            horariosHtml += '<br><small class="text-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i>Asegúrate de que las horas de la cita estén dentro de este horario.</small>';
            
            textoDiv.innerHTML = horariosHtml;
            infoDiv.classList.remove('d-none');
            
            // Si es editar, mostrar alerta adicional
            if (tipo === 'editar') {
              mostrarAlertaCambioHorario(horario);
            }
          } else {
            console.log('No se encontraron horarios o respuesta inválida');
            textoDiv.innerHTML = '<em class=\"text-muted\">Este médico no tiene horarios configurados.</em>';
            infoDiv.classList.remove('d-none');
            if (tipo === 'nuevo') horariosNuevo = null;
            if (tipo === 'editar') horariosEditar = null;
          }
        })
        .catch(error => {
          console.error('Error completo:', error);
          const infoDiv = document.getElementById('horarios-info-' + tipo);
          const textoDiv = document.getElementById('horarios-texto-' + tipo);
          textoDiv.innerHTML = '<em class=\"text-danger\">Error al cargar horarios: ' + error.message + '</em>';
          infoDiv.classList.remove('d-none');
          if (tipo === 'nuevo') horariosNuevo = null;
          if (tipo === 'editar') horariosEditar = null;
        });
    }
    
    // Función para mostrar alerta cuando cambia médico en edición
    function mostrarAlertaCambioHorario(horario) {
      const modalEditar = document.getElementById('editarModal');
      if (!modalEditar) {
        console.log('Modal editar no encontrado');
        return;
      }
      
      // Crear alerta temporal
      let alertaExistente = modalEditar.querySelector('.alerta-cambio-horario');
      if (alertaExistente) {
        alertaExistente.remove();
      }
      
      const alerta = document.createElement('div');
      alerta.className = 'alert alert-warning alerta-cambio-horario mt-3';
      alerta.innerHTML = `
        <strong><i class="fa-solid fa-clock-rotate-left me-2"></i>Cambio de médico detectado</strong><br>
        <small>El nuevo médico tiene horario: <strong>${horario.HORARIO}</strong> (${horario.HORA_INICIO} - ${horario.HORA_FIN})</small><br>
        <small class="text-danger">⚠️ Por favor, verifica y ajusta las horas de inicio y fin de la cita.</small>
      `;
      
      // Insertar alerta después del div de horarios
      const horariosDiv = document.getElementById('horarios-info-editar');
      if (horariosDiv && horariosDiv.parentNode) {
        horariosDiv.parentNode.insertBefore(alerta, horariosDiv.nextSibling);
      }
    }

    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
      // Evento cuando se selecciona un médico en modal nuevo
      const nuevoMedicoSelect = document.getElementById('nuevo-medico');
      if (nuevoMedicoSelect) {
        nuevoMedicoSelect.addEventListener('change', function() {
          console.log('Médico seleccionado (nuevo):', this.value);
          cargarHorariosMedico(this.value, 'nuevo');
        });
      }

      // Evento cuando se selecciona un médico en modal editar
      const editarMedicoSelect = document.getElementById('editar-medico');
      if (editarMedicoSelect) {
        editarMedicoSelect.addEventListener('change', function() {
          console.log('Médico seleccionado (editar):', this.value);
          cargarHorariosMedico(this.value, 'editar');
        });
      }
    });
  </script>

  <script>
    // Función para enviar formulario con AJAX
    function enviarFormularioAjax(formId, alertaId) {
        const form = document.getElementById(formId);
        const alerta = document.getElementById(alertaId);

        if (!form || !alerta) return;

        form.addEventListener('submit', function (e) {
          e.preventDefault();

          alerta.classList.add('d-none');
          alerta.textContent = '';

          const formData = new FormData(form);

          fetch('citas.php', {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (response.redirected) {
              const finalUrl = new URL(response.url);
              const ok  = finalUrl.searchParams.get('ok');
              const msgParam = finalUrl.searchParams.get('msg');
              const msg = msgParam ? decodeURIComponent(msgParam) : '';

              if (ok === '1') {
                window.location.href = response.url;
              } else {
                const mensaje = msg || 'No se pudo agendar/actualizar la cita.';
                alerta.textContent = mensaje;
                alerta.classList.remove('d-none');

                const modalContent = form.closest('.modal-content');
                if (modalContent) modalContent.scrollTop = 0;
              }
            } else {
              // Por si acaso no hubo redirect, recargo
              return response.text().then(() => {
                window.location.reload();
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud. Por favor intente nuevamente.');
          });
        });
      }

      document.addEventListener('DOMContentLoaded', function () {
        enviarFormularioAjax('formNuevaCita', 'alertaNuevaCita');
        enviarFormularioAjax('formEditarCita', 'alertaEditarCita');

        const nuevoClinica = document.getElementById('nuevo-clinica');
        if (nuevoClinica) {
          nuevoClinica.addEventListener('change', function () {
            cargarConsultorios(this.value, 'nuevo-consultorio');
          });
        }

        const editarClinica = document.getElementById('editar-clinica');
        if (editarClinica) {
          editarClinica.addEventListener('change', function () {
            cargarConsultorios(this.value, 'editar-consultorio');
          });
        }
      });

    const consultoriosPorClinica = <?= json_encode($consultorios) ?>;

    // Función global para cargar consultorios en un select
    function cargarConsultorios(idClinica, selectConsultorio, consultorioSeleccionado = null) {
      const select = document.getElementById(selectConsultorio);
      select.innerHTML = '<option value="">Selecciona...</option>';

      if (!idClinica) {
        select.innerHTML = '<option value="">Selecciona clínica primero...</option>';
        return;
      }

      const consultoriosFiltrados = consultoriosPorClinica.filter(c => c.ID_CLINICA == idClinica);

      consultoriosFiltrados.forEach(consultorio => {
        const option = document.createElement('option');
        option.value = consultorio.ID_CONSULTORIO;
        option.textContent = consultorio.NOMBRE + ' (' + consultorio.TIPO + ')';
        if (consultorioSeleccionado && consultorio.ID_CONSULTORIO == consultorioSeleccionado) {
          option.selected = true;
        }
        select.appendChild(option);
      });
    }
  </script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
        const alertGlobal = document.getElementById('alertGlobal');
        
        // Solo limpiamos la URL si realmente existe la alerta de arriba
        if (alertGlobal && window.location.search.includes("msg")) {
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    });
  </script>
  <script>
      setTimeout(() => {
          const alertGlobal = document.getElementById('alertGlobal');
          if (alertGlobal) alertGlobal.remove();
      }, 3000);
  </script>
</body>

</html>
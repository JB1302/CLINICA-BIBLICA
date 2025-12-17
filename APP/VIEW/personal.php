<?php
require_once __DIR__ . '/../CONTROLLER/PersonalController.php';

$controller = new PersonalController();

// Procesar AJAX para detalle
if (isset($_GET['ajax']) && $_GET['ajax'] === 'detalle') {
    header('Content-Type: application/json');
    echo json_encode($controller->obtenerDetalle());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  switch ($action) {
      case 'create':
          $res = $controller->crearPersonal($_POST);
          break;

      case 'update':
          $res = $controller->actualizarPersonal($_POST);
          break;

      case 'delete':
          $id  = isset($_POST['ID_PERSONAL']) ? (int)$_POST['ID_PERSONAL'] : 0;
          $res = $controller->eliminarPersonal($id);
          break;

      default:
          $res = ['resultado' => 0, 'mensaje' => 'Acción no válida'];
          break;
  }

  // POST-Redirect-GET para no repetir envíos al refrescar
  $ok  = $res['resultado'] ?? 0;
  $msg = $res['mensaje']   ?? '';

  header('Location: personal.php?ok=' . urlencode($ok) . '&msg=' . urlencode($msg));
  exit;
}

$Personals = $controller->listarPersonals();
$horarios = $controller->listarHorarios();
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


  <!-- MAIN -->
  <main class="container py-5">
    <!-- Alerta de confirmacion o error -->
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
            <i class="fa-solid fa-users me-2"></i>Gestión de Personal
          </h3>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Personal
          </button>
        </div>
      </div>
    </div>

    <!-- TABLA -->
    <div class="table-responsive shadow-sm">
      <table class="table table-sm table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>ID</th>
            <th>Primer Nombre</th>
            <th>Segundo Nombre</th>
            <th>Primer Apellido</th>
            <th>Segundo Apellido</th>
            <th>Puesto</th>
            <th>Teléfono</th>
            <th>Activo</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($Personals)): ?>
            <?php foreach ($Personals as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['ID_PERSONAL'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['SEGUNDO_APELLIDO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['PUESTO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['TELEFONO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['ACTIVO'] ?? '') ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-info me-1" onclick="verDetalle(<?= $p['ID_PERSONAL'] ?>)" title="Ver detalle">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-warning me-1 btn-editar"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                    data-id="<?= htmlspecialchars($p['ID_PERSONAL'] ?? '') ?>"
                    data-pnombre="<?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?>"
                    data-snombre="<?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?>"
                    data-papellido="<?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?>"
                    data-sapellido="<?= htmlspecialchars($p['SEGUNDO_APELLIDO'] ?? '') ?>"
                    data-puesto="<?= htmlspecialchars($p['PUESTO'] ?? '') ?>"
                    data-activo="<?= htmlspecialchars($p['ACTIVO'] ?? '') ?>"
                    data-correo="<?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?>"
                    data-telefono="<?= htmlspecialchars($p['TELEFONO'] ?? '') ?>"
                    data-direccion="<?= htmlspecialchars($p['DIRECCION'] ?? '') ?>"
                    data-provincia="<?= htmlspecialchars($p['PROVINCIA'] ?? '') ?>"
                    data-canton="<?= htmlspecialchars($p['CANTON'] ?? '') ?>"
                    data-distrito="<?= htmlspecialchars($p['DISTRITO'] ?? '') ?>"
                    data-horario="<?= htmlspecialchars($p['HORARIO_TRABAJO'] ?? '') ?>"
                    data-fecha="<?php 
                      if (!empty($p['FECHA_REGISTRO'])) {
                        $fecha = $p['FECHA_REGISTRO'];
                        if ($fecha instanceof DateTime) {
                          echo htmlspecialchars($fecha->format('Y-m-d'));
                        } else {
                          $dateObj = date_create($fecha);
                          echo $dateObj ? htmlspecialchars(date_format($dateObj, 'Y-m-d')) : '';
                        }
                      }
                    ?>">
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <form method="post" class="d-inline"
                        onsubmit="return confirm('¿Seguro que deseas eliminar este personal?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="ID_PERSONAL" value="<?= (int)$p['ID_PERSONAL'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar personal">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted">No hay Personals registrados</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </main>

  <!-- MODAL AGREGAR -->
  <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <form id="formNuevoPersonal" method="post" action="personal.php" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAgregarLabel">Nuevo Personal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <input type="hidden" name="action" value="create">
            <div id="alertaNuevoPersonal" class="alert alert-danger d-none" role="alert"></div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Primer Nombre</label>
                <input type="text" class="form-control" name="PRIMER_NOMBRE" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Segundo Nombre</label>
                <input type="text" class="form-control" name="SEGUNDO_NOMBRE">
              </div>

              <div class="col-md-6">
                <label class="form-label">Primer Apellido</label>
                <input type="text" class="form-control" name="PRIMER_APELLIDO" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Segundo Apellido</label>
                <input type="text" class="form-control" name="SEGUNDO_APELLIDO">
              </div>

              <div class="col-md-6">
                <label class="form-label">Puesto</label>
                <select class="form-select" name="PUESTO" required>
                  <option value="">Selecciona...</option>
                  <option value="Administrador">Administrador</option>
                  <option value="Asistente Administrativa">Asistente Administrativa</option>
                  <option value="Asistente de Laboratorio">Asistente de Laboratorio</option>
                  <option value="Asistente Médica">Asistente Médica</option>
                  <option value="Chofer de Ambulancia">Chofer de Ambulancia</option>
                  <option value="Contador">Contador</option>
                  <option value="Doctor Anestesiólogo">Doctor Anestesiólogo</option>
                  <option value="Doctor Cardiologo">Doctor Cardiologo</option>
                  <option value="Doctor Cirujano">Doctor Cirujano</option>
                  <option value="Doctor Especialista">Doctor Especialista</option>
                  <option value="Doctor General">Doctor General</option>
                  <option value="Doctor Geriatra">Doctor Geriatra</option>
                  <option value="Doctor Internista">Doctor Internista</option>
                  <option value="Doctor Neurólogo">Doctor Neurólogo</option>
                  <option value="Doctor Oncólogo">Doctor Oncólogo</option>
                  <option value="Doctor Ortopedista">Doctor Ortopedista</option>
                  <option value="Doctora General">Doctora General</option>
                  <option value="Doctora Ginecóloga">Doctora Ginecóloga</option>
                  <option value="Doctora Oftalmóloga">Doctora Oftalmóloga</option>
                  <option value="Doctora Pediatra">Doctora Pediatra</option>
                  <option value="Encargada de Limpieza">Encargada de Limpieza</option>
                  <option value="Enfermera">Enfermera</option>
                  <option value="Enfermera Asistente">Enfermera Asistente</option>
                  <option value="Enfermera Jefe">Enfermera Jefe</option>
                  <option value="Enfermero">Enfermero</option>
                  <option value="Farmacéutica">Farmacéutica</option>
                  <option value="Guardia de Seguridad">Guardia de Seguridad</option>
                  <option value="Mecánico de Equipo Médico">Mecánico de Equipo Médico</option>
                  <option value="Médico Residente">Médico Residente</option>
                  <option value="Nutricionista">Nutricionista</option>
                  <option value="Paramédico">Paramédico</option>
                  <option value="Recepcionista">Recepcionista</option>
                  <option value="Secretaria">Secretaria</option>
                  <option value="Secretaria Médica">Secretaria Médica</option>
                  <option value="Técnico de Laboratorio">Técnico de Laboratorio</option>
                  <option value="Técnico de Rayos X">Técnico de Rayos X</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Activo</label>
                <select class="form-select" name="ACTIVO" required>
                  <option value="S">S</option>
                  <option value="N">N</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" name="CORREO_ELECTRONICO">
              </div>
              <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="tel" class="form-control" name="TELEFONO">
              </div>

              <div class="col-12">
                <label class="form-label">Dirección</label>
                <input type="text" class="form-control" name="DIRECCION">
              </div>

              <div class="col-md-4">
                <label class="form-label">Provincia</label>
                <input type="text" class="form-control" name="PROVINCIA">
              </div>
              <div class="col-md-4">
                <label class="form-label">Canton</label>
                <input type="text" class="form-control" name="CANTON">
              </div>
              <div class="col-md-4">
                <label class="form-label">Distrito</label>
                <input type="text" class="form-control" name="DISTRITO">
              </div>

              <div class="col-md-6">
                <label class="form-label">Horario de Trabajo</label>
                <select class="form-select" name="HORARIO_TRABAJO">
                  <option value="">Selecciona...</option>
                  <?php foreach ($horarios as $h): ?>
                    <option value="<?= htmlspecialchars($h['ID_HORARIO']) ?>">
                      <?= htmlspecialchars($h['HORARIO']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Fecha de Contratación</label>
                <input type="date" class="form-control" name="FECHA_CONTRATACION" required>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Agregar</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- MODAL EDITAR -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <form id="formEditarPersonal" method="post" action="personal.php">

          <div class="modal-header">
            <h5 class="modal-title" id="modalEditarLabel">Editar Personal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <div class="modal-body">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="ID_PERSONAL" id="edit-id">
            <div id="alertaEditarPersonal" class="alert alert-danger d-none" role="alert"></div>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="edit-pnombre" class="form-label">Primer Nombre</label>
                <input type="text" class="form-control" id="edit-pnombre" name="PRIMER_NOMBRE" required>
              </div>
              <div class="col-md-6">
                <label for="edit-snombre" class="form-label">Segundo Nombre</label>
                <input type="text" class="form-control" id="edit-snombre" name="SEGUNDO_NOMBRE">
              </div>

              <div class="col-md-6">
                <label for="edit-papellido" class="form-label">Primer Apellido</label>
                <input type="text" class="form-control" id="edit-papellido" name="PRIMER_APELLIDO" required>
              </div>
              <div class="col-md-6">
                <label for="edit-sapellido" class="form-label">Segundo Apellido</label>
                <input type="text" class="form-control" id="edit-sapellido" name="SEGUNDO_APELLIDO">
              </div>

              <div class="col-md-6">
                <label for="edit-puesto" class="form-label">Puesto</label>
                <select class="form-select" id="edit-puesto" name="PUESTO" required>
                  <option value="">Selecciona...</option>
                  <option value="Administrador">Administrador</option>
                  <option value="Asistente Administrativa">Asistente Administrativa</option>
                  <option value="Asistente de Laboratorio">Asistente de Laboratorio</option>
                  <option value="Asistente Médica">Asistente Médica</option>
                  <option value="Chofer de Ambulancia">Chofer de Ambulancia</option>
                  <option value="Contador">Contador</option>
                  <option value="Doctor Anestesiólogo">Doctor Anestesiólogo</option>
                  <option value="Doctor Cardiologo">Doctor Cardiologo</option>
                  <option value="Doctor Cirujano">Doctor Cirujano</option>
                  <option value="Doctor Especialista">Doctor Especialista</option>
                  <option value="Doctor General">Doctor General</option>
                  <option value="Doctor Geriatra">Doctor Geriatra</option>
                  <option value="Doctor Internista">Doctor Internista</option>
                  <option value="Doctor Neurólogo">Doctor Neurólogo</option>
                  <option value="Doctor Oncólogo">Doctor Oncólogo</option>
                  <option value="Doctor Ortopedista">Doctor Ortopedista</option>
                  <option value="Doctora General">Doctora General</option>
                  <option value="Doctora Ginecóloga">Doctora Ginecóloga</option>
                  <option value="Doctora Oftalmóloga">Doctora Oftalmóloga</option>
                  <option value="Doctora Pediatra">Doctora Pediatra</option>
                  <option value="Encargada de Limpieza">Encargada de Limpieza</option>
                  <option value="Enfermera">Enfermera</option>
                  <option value="Enfermera Asistente">Enfermera Asistente</option>
                  <option value="Enfermera Jefe">Enfermera Jefe</option>
                  <option value="Enfermero">Enfermero</option>
                  <option value="Farmacéutica">Farmacéutica</option>
                  <option value="Guardia de Seguridad">Guardia de Seguridad</option>
                  <option value="Mecánico de Equipo Médico">Mecánico de Equipo Médico</option>
                  <option value="Médico Residente">Médico Residente</option>
                  <option value="Nutricionista">Nutricionista</option>
                  <option value="Paramédico">Paramédico</option>
                  <option value="Recepcionista">Recepcionista</option>
                  <option value="Secretaria">Secretaria</option>
                  <option value="Secretaria Médica">Secretaria Médica</option>
                  <option value="Técnico de Laboratorio">Técnico de Laboratorio</option>
                  <option value="Técnico de Rayos X">Técnico de Rayos X</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="edit-activo" class="form-label">Activo</label>
                <select class="form-select" id="edit-activo" name="ACTIVO" required>
                  <option value="">Selecciona</option>
                  <option value="S">S</option>
                  <option value="N">N</option>

                </select>
              </div>

              <div class="col-md-6">
                <label for="edit-correo" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="edit-correo" name="CORREO_ELECTRONICO">
              </div>
              <div class="col-md-6">
                <label for="edit-telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="edit-telefono" name="TELEFONO">
              </div>

              <div class="col-12">
                <label for="edit-direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="edit-direccion" name="DIRECCION">
              </div>

              <div class="col-md-4">
                <label for="edit-provincia" class="form-label">Provincia</label>
                <input type="text" class="form-control" id="edit-provincia" name="PROVINCIA">
              </div>
              <div class="col-md-4">
                <label for="edit-canton" class="form-label">Canton</label>
                <input type="text" class="form-control" id="edit-canton" name="CANTON">
              </div>
              <div class="col-md-4">
                <label for="edit-distrito" class="form-label">Distrito</label>
                <input type="text" class="form-control" id="edit-distrito" name="DISTRITO">
              </div>

              <div class="col-md-6">
                <label for="edit-horario" class="form-label">Horario de Trabajo</label>
                <select class="form-select" id="edit-horario" name="HORARIO_TRABAJO">
                  <option value="">Selecciona...</option>
                  <?php foreach ($horarios as $h): ?>
                    <option value="<?= htmlspecialchars($h['ID_HORARIO']) ?>">
                      <?= htmlspecialchars($h['HORARIO']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label for="edit-fecha-contratacion" class="form-label">Fecha de Contratación</label>
                <input type="date" class="form-control" id="edit-fecha-contratacion" name="FECHA_CONTRATACION" required>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- Modal Detalle Personal -->
  <div class="modal fade" id="detallePersonalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle del Personal</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="detalle-contenido">
          <div class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>




  <!-- FOOTER -->
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

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SCRIPT PARA RELLENAR EL MODAL -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalEditar  = document.getElementById('modalEditar');
      const modalAgregar = document.getElementById('modalAgregar');

      // ===== MODAL EDITAR =====
      if (modalEditar) {
        modalEditar.addEventListener('show.bs.modal', function(event) {
          const button = event.relatedTarget;

          // Lee todos los data-* del botón
          const id        = button.getAttribute('data-id')        || '';
          const pnombre   = button.getAttribute('data-pnombre')   || '';
          const snombre   = button.getAttribute('data-snombre')   || '';
          const papellido = button.getAttribute('data-papellido') || '';
          const sapellido = button.getAttribute('data-sapellido') || '';
          const puesto    = button.getAttribute('data-puesto')    || '';
          const activo    = button.getAttribute('data-activo')    || '';
          const correo    = button.getAttribute('data-correo')    || '';
          const telefono  = button.getAttribute('data-telefono')  || '';
          const direccion = button.getAttribute('data-direccion') || '';
          const provincia = button.getAttribute('data-provincia') || '';
          const canton    = button.getAttribute('data-canton')    || '';
          const distrito  = button.getAttribute('data-distrito')  || '';
          const horario   = button.getAttribute('data-horario')   || '';
          const fecha     = button.getAttribute('data-fecha')     || '';

          // Asigna a los campos del formulario
          document.getElementById('edit-id').value                  = id;
          document.getElementById('edit-pnombre').value             = pnombre;
          document.getElementById('edit-snombre').value             = snombre;
          document.getElementById('edit-papellido').value           = papellido;
          document.getElementById('edit-sapellido').value           = sapellido;
          document.getElementById('edit-puesto').value              = puesto;
          document.getElementById('edit-activo').value              = activo;
          document.getElementById('edit-correo').value              = correo;
          document.getElementById('edit-telefono').value            = telefono;
          document.getElementById('edit-direccion').value           = direccion;
          document.getElementById('edit-provincia').value           = provincia;
          document.getElementById('edit-canton').value              = canton;
          document.getElementById('edit-distrito').value            = distrito;
          document.getElementById('edit-horario').value             = horario;
          document.getElementById('edit-fecha-contratacion').value  = fecha;

          // Limpiar alerta de error vieja
          const alertaEditar = document.getElementById('alertaEditarPersonal');
          if (alertaEditar) {
            alertaEditar.classList.add('d-none');
            alertaEditar.textContent = '';
          }
        });

        // Al cerrar: limpiar errores y (opcional) resetear el formulario
        modalEditar.addEventListener('hidden.bs.modal', function() {
          const formEditar = document.getElementById('formEditarPersonal');
          if (formEditar) {
            // Si NO quieres resetear, comenta esta línea:
            formEditar.reset();
          }

          const alertaEditar = document.getElementById('alertaEditarPersonal');
          if (alertaEditar) {
            alertaEditar.classList.add('d-none');
            alertaEditar.textContent = '';
          }
        });
      }

      // ===== MODAL AGREGAR =====
      if (modalAgregar) {
        // Al abrir: limpiar errores
        modalAgregar.addEventListener('show.bs.modal', function() {
          const alertaNuevo = document.getElementById('alertaNuevoPersonal');
          if (alertaNuevo) {
            alertaNuevo.classList.add('d-none');
            alertaNuevo.textContent = '';
          }
        });

        // Al cerrar: reset + limpiar errores
        modalAgregar.addEventListener('hidden.bs.modal', function() {
          const formNuevo = document.getElementById('formNuevoPersonal');
          if (formNuevo) {
            // Si no quieres que borre lo digitado, comenta esta línea:
            formNuevo.reset();
          }

          const alertaNuevo = document.getElementById('alertaNuevoPersonal');
          if (alertaNuevo) {
            alertaNuevo.classList.add('d-none');
            alertaNuevo.textContent = '';
          }
        });
      }
    });

    // La función verDetalle se mantiene igual
    function verDetalle(idPersonal) {
      const modal = new bootstrap.Modal(document.getElementById('detallePersonalModal'));
      modal.show();

      fetch('personal.php?ajax=detalle&id=' + idPersonal)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            document.getElementById('detalle-contenido').innerHTML =
              `<div class="alert alert-danger">${data.error}</div>`;
            return;
          }

          let html = `
            <div class="card mb-3">
              <div class="card-header bg-primary text-white">
                <h6 class="mb-0">Información Personal</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Nombre Completo:</strong> ${data.personal.NOMBRE_COMPLETO}</p>
                    <p><strong>Puesto:</strong> ${data.personal.PUESTO}</p>
                    <p><strong>Activo:</strong> ${data.personal.ACTIVO === 'S' ? 'Sí' : 'No'}</p>
                    <p><strong>Correo:</strong> ${data.personal.CORREO_ELECTRONICO || 'N/A'}</p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Teléfono:</strong> ${data.personal.TELEFONO || 'N/A'}</p>
                    <p><strong>Provincia:</strong> ${data.personal.PROVINCIA || 'N/A'}</p>
                    <p><strong>Cantón:</strong> ${data.personal.CANTON || 'N/A'}</p>
                    <p><strong>Distrito:</strong> ${data.personal.DISTRITO || 'N/A'}</p>
                  </div>
                </div>
                <p><strong>Dirección:</strong> ${data.personal.DIRECCION || 'N/A'}</p>
                <p><strong>Horario:</strong> ${data.personal.HORARIO_TEXTO || 'N/A'}</p>
                <p><strong>Fecha de Contrato:</strong> ${data.personal.FECHA_REGISTRO || 'N/A'}</p>
              </div>
            </div>
          `;

          document.getElementById('detalle-contenido').innerHTML = html;
        })
        .catch(error => {
          document.getElementById('detalle-contenido').innerHTML =
            `<div class="alert alert-danger">Error al cargar el detalle</div>`;
        });
    }
    </script>

    <!-- Eliminar el mensaje al refrescar -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Si la URL contiene parámetros (msg, ok)
            if (window.location.search.includes("msg")) {

                // Eliminar los parámetros de la URL sin recargar la página
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        });
    </script>
    <!-- Eliminar el mensaje a los 3 segundos -->
    <script>
      setTimeout(() => {
          const alertGlobal = document.getElementById('alertGlobal');
          if (alertGlobal) alertGlobal.remove();
      }, 3000);
    </script>
    <script>
      // Función genérica para enviar formulario por AJAX y mostrar errores en el modal
      function enviarFormularioAjax(formId, alertaId) {
          const form   = document.getElementById(formId);
          const alerta = document.getElementById(alertaId);

          if (!form || !alerta) return;

          form.addEventListener('submit', function (e) {
              e.preventDefault();

              alerta.classList.add('d-none');
              alerta.textContent = '';

              const formData = new FormData(form);

              fetch('personal.php', {
                  method: 'POST',
                  body: formData
              })
              .then(response => {
                  if (response.redirected) {
                      const finalUrl = new URL(response.url);
                      const ok       = finalUrl.searchParams.get('ok');
                      const msgParam = finalUrl.searchParams.get('msg');
                      const msg      = msgParam ? decodeURIComponent(msgParam) : '';

                      if (ok === '1') {
                          // ÉXITO: recargo la página normalmente
                          window.location.href = response.url;
                      } else {
                          // ERROR: muestro el mensaje dentro del modal
                          const mensaje = msg || 'No se pudo guardar el personal.';
                          alerta.textContent = mensaje;
                          alerta.classList.remove('d-none');

                          const modalContent = form.closest('.modal-content');
                          if (modalContent) modalContent.scrollTop = 0;
                      }
                  } else {
                      // Por si acaso no hubo redirect, recargo la página
                      return response.text().then(() => {
                          window.location.reload();
                      });
                  }
              })
              .catch(error => {
                  console.error('Error:', error);
                  alert('Error al procesar la solicitud. Por favor, intente nuevamente.');
              });
          });
      }

      document.addEventListener('DOMContentLoaded', function () {
          enviarFormularioAjax('formNuevoPersonal',  'alertaNuevoPersonal');
          enviarFormularioAjax('formEditarPersonal', 'alertaEditarPersonal');
      });
    </script>

</body>

</html>
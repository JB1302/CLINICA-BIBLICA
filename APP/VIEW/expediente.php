<?php
require_once '/var/www/CONTROLLER/ExpedienteController.php';

$controller = new ExpedienteController();

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'crear') {
        $resultado = $controller->crear();
        header("Location: expediente.php?msg=" . urlencode($resultado['mensaje']) . "&ok=" . $resultado['ok']);
        exit;
    } elseif ($accion === 'actualizar') {
        $resultado = $controller->actualizar();
        header("Location: expediente.php?msg=" . urlencode($resultado['mensaje']) . "&ok=" . $resultado['ok']);
        exit;
    }
}

// Procesar AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] === 'detalle') {
    header('Content-Type: application/json');
    echo json_encode($controller->obtenerDetalle());
    exit;
}

$expedientes = $controller->obtenerTodos();
$pacientesSinExpediente = $controller->obtenerPacientesSinExpediente();

// Manejo de mensajes
$msg = $_GET['msg'] ?? '';
$ok = $_GET['ok'] ?? '';
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>⚕️ Clinica Biblica - Expedientes</title>


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
    <?php if (!empty($msg)): ?>
      <div class="alert alert-<?= $ok == 1 ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-file-medical me-2"></i>Gestión de Expedientes
          </h3>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoExpedienteModal">
            <i class="fa-solid fa-plus me-2"></i>Nuevo Expediente
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-primary">
              <tr>
                <th>ID</th>
                <th>Cédula</th>
                <th>Paciente</th>
                <th>Fecha Creación</th>
                <th>Teléfono</th>
                <th>Notas</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($expedientes)): ?>
                <tr>
                  <td colspan="7" class="text-center text-muted">No hay expedientes registrados</td>
                </tr>
              <?php else: ?>
                <?php foreach ($expedientes as $exp): ?>
                  <tr>
                    <td><?= htmlspecialchars($exp['ID_EXPEDIENTE']) ?></td>
                    <td><?= htmlspecialchars($exp['CEDULA']) ?></td>
                    <td><?= htmlspecialchars($exp['NOMBRE_PACIENTE']) ?></td>
                    <td><?= htmlspecialchars($exp['FECHA_CREACION']) ?></td>
                    <td><?= htmlspecialchars($exp['TELEFONO']) ?></td>
                    <td><?= htmlspecialchars($exp['NOTAS'] ?? 'Sin notas') ?></td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-info me-1" onclick="verDetalle(<?= $exp['ID_EXPEDIENTE'] ?>)" title="Ver detalle">
                        <i class="fa-solid fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-warning" onclick="editarExpediente(<?= $exp['ID_EXPEDIENTE'] ?>, '<?= htmlspecialchars($exp['NOTAS'] ?? '', ENT_QUOTES) ?>')" title="Editar">
                        <i class="fa-solid fa-edit"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal Nuevo Expediente -->
  <div class="modal fade" id="nuevoExpedienteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nuevo Expediente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
          <input type="hidden" name="accion" value="crear">
          <div class="modal-body">
            <div class="mb-3">
              <label for="nuevo-paciente" class="form-label">Paciente *</label>
              <select class="form-select" id="nuevo-paciente" name="id_paciente" required>
                <option value="">Seleccione un paciente</option>
                <?php foreach ($pacientesSinExpediente as $pac): ?>
                  <option value="<?= $pac['ID_PACIENTE'] ?>">
                    <?= htmlspecialchars($pac['CEDULA']) ?> - <?= htmlspecialchars($pac['NOMBRE_COMPLETO']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="nuevo-notas" class="form-label">Notas</label>
              <textarea class="form-control" id="nuevo-notas" name="notas" rows="3" maxlength="100"></textarea>
              <small class="text-muted">Máximo 100 caracteres</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Crear Expediente</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Editar Expediente -->
  <div class="modal fade" id="editarExpedienteModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Editar Expediente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
          <input type="hidden" name="accion" value="actualizar">
          <input type="hidden" id="editar-id" name="id_expediente">
          <div class="modal-body">
            <div class="mb-3">
              <label for="editar-notas" class="form-label">Notas</label>
              <textarea class="form-control" id="editar-notas" name="notas" rows="3" maxlength="100"></textarea>
              <small class="text-muted">Máximo 100 caracteres</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Detalle Expediente -->
  <div class="modal fade" id="detalleExpedienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalle del Expediente</h5>
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
    function editarExpediente(id, notas) {
      document.getElementById('editar-id').value = id;
      document.getElementById('editar-notas').value = notas;
      new bootstrap.Modal(document.getElementById('editarExpedienteModal')).show();
    }

    function verDetalle(idExpediente) {
      const modal = new bootstrap.Modal(document.getElementById('detalleExpedienteModal'));
      modal.show();

      fetch('expediente.php?ajax=detalle&id=' + idExpediente)
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
                <h6 class="mb-0">Información del Paciente</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Nombre:</strong> ${data.expediente.NOMBRE_PACIENTE}</p>
                    <p><strong>Cédula:</strong> ${data.expediente.CEDULA}</p>
                    <p><strong>Sexo:</strong> ${data.expediente.SEXO}</p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Teléfono:</strong> ${data.expediente.TELEFONO}</p>
                    <p><strong>Email:</strong> ${data.expediente.CORREO_ELECTRONICO}</p>
                    <p><strong>Fecha Nac.:</strong> ${data.expediente.FECHA_NACIMIENTO}</p>
                  </div>
                </div>
                <p><strong>Dirección:</strong> ${data.expediente.DIRECCION}</p>
                <p><strong>Notas del Expediente:</strong> ${data.expediente.NOTAS || 'Sin notas'}</p>
              </div>
            </div>

            <div class="card">
              <div class="card-header bg-info text-white">
                <h6 class="mb-0">Historial de Citas (${data.citas.length})</h6>
              </div>
              <div class="card-body">
                ${data.citas.length === 0 ? '<p class="text-muted">No hay citas registradas</p>' : `
                  <div class="table-responsive">
                    <table class="table table-sm table-hover">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Hora</th>
                          <th>Médico</th>
                          <th>Especialidad</th>
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody>
                        ${data.citas.map(cita => `
                          <tr>
                            <td>${cita.FECHA}</td>
                            <td>${cita.HORA_INICIO} - ${cita.HORA_FIN}</td>
                            <td>${cita.NOMBRE_MEDICO}</td>
                            <td>${cita.ESPECIALIDAD || 'N/A'}</td>
                            <td><span class="badge bg-${cita.ESTADO === 'Programada' ? 'primary' : cita.ESTADO === 'Completada' ? 'success' : 'danger'}">${cita.ESTADO}</span></td>
                          </tr>
                        `).join('')}
                      </tbody>
                    </table>
                  </div>
                `}
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
</body>

</html>
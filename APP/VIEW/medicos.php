<?php
require_once __DIR__ . '/../CONTROLLER/MedicoController.php';

$controller = new MedicoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'create':
            $res = $controller->crear([
                'ID_PERSONAL'     => (int)($_POST['ID_PERSONAL']     ?? 0),
                'ID_ESPECIALIDAD' => (int)($_POST['ID_ESPECIALIDAD'] ?? 0),
            ]);
            break;

        case 'update':
            $res = $controller->actualizar([
                'ID_MEDICO'       => (int)($_POST['ID_MEDICO']       ?? 0),
                'NOMBRE'          => $_POST['NOMBRE']                ?? '',
                'ID_ESPECIALIDAD' => (int)($_POST['ID_ESPECIALIDAD'] ?? 0),
                'ID_HORARIO'      => !empty($_POST['ID_HORARIO']) ? (int)$_POST['ID_HORARIO'] : null,
            ]);
            break;

        case 'delete':
            $id  = (int)($_POST['ID_MEDICO'] ?? 0);
            $res = $controller->eliminar($id);
            break;

        default:
            $res = ['resultado' => 0, 'mensaje' => 'Acción no válida'];
            break;
    }

    header('Location: medicos.php?msg=' . urlencode($res['mensaje'])
                           . '&ok=' . (int)($res['resultado'] ?? 0));
    exit;
}

$medicos = $controller->listarMedicos();
$especialidades = $controller->listarEspecialidades();
$personalDisponible = $controller->listarPersonalDisponible();

require_once __DIR__ . '/../CONTROLLER/PersonalController.php';
$personalController = new PersonalController();
$horarios = $personalController->listarHorarios();

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
    <!-- Alerta de confirmacion o error -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= ($_GET['ok'] ?? '0') == '1' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-user-doctor me-2"></i>Gestión de Médicos
          </h3>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearMedico">
            <i class="fa-solid fa-plus me-1"></i> Nuevo Médico
          </button>
        </div>
      </div>
    </div>


    <div class="table-responsive shadow-sm">
      <table class="table table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Especialidad</th>
            <th>Horarios</th>

            <th class="text-center">Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php if (!empty($medicos)): ?>
            <?php foreach ($medicos as $m): ?>
              <tr>
                <td><?= htmlspecialchars($m['ID_MEDICO'] ?? '') ?></td>
                <td><?= htmlspecialchars($m['NOMBRE_MEDICO'] ?? '') ?></td>
                <td><?= htmlspecialchars($m['ESPECIALIDAD'] ?? '') ?></td>
                <td><?= htmlspecialchars($m['HORARIO'] ?? 'Sin horario') ?></td>

                <td class="text-center">
                  <button class="btn btn-sm btn-warning me-1"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEditar"
                          data-id="<?= htmlspecialchars($m['ID_MEDICO']) ?>"
                          data-nombre="<?= htmlspecialchars($m['NOMBRE_MEDICO']) ?>"
                          data-especialidad-id="<?= htmlspecialchars($m['ID_ESPECIALIDAD']) ?>"
                          data-id-horario="<?= htmlspecialchars($m['ID_HORARIO'] ?? '') ?>">
                    <i class="fa-solid fa-pen"></i>
                  </button>

                  <button class="btn btn-sm btn-danger"
                          data-bs-toggle="modal"
                          data-bs-target="#modalEliminarMedico"
                          data-id="<?= htmlspecialchars($m['ID_MEDICO']) ?>">
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

  <!-- MODAL: CREAR MÉDICO -->
  <div class="modal fade" id="modalCrearMedico" tabindex="-1" aria-labelledby="lblCrearMedico" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" method="post" action="/medicos.php?action=create">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="lblCrearMedico">
            <i class="fa-solid fa-user-plus me-2"></i>Nuevo médico
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- PERSONAL DISPONIBLE -->
          <div class="mb-3">
            <label class="form-label">Personal (Doctor/Doctora)</label>
            <select class="form-select" id="ID_PERSONAL" name="ID_PERSONAL" required>
              <option value="">Selecciona...</option>
              <?php foreach ($personalDisponible as $p): ?>
                <option value="<?= htmlspecialchars($p['ID_PERSONAL']) ?>">
                  <?= htmlspecialchars($p['NOMBRE_COMPLETO']) ?> (<?= htmlspecialchars($p['PUESTO']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>


          <div class="mb-3">
            <label class="form-label">Especialidad</label>
            <select name="ID_ESPECIALIDAD" class="form-select" required>
              <option value="">Selecciona...</option>
              <?php foreach ($especialidades as $esp): ?>
                <option value="<?= htmlspecialchars($esp['ID_ESPECIALIDAD']) ?>">
                  <?= htmlspecialchars($esp['NOMBRE']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cerrar</button>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>


  <!-- MODAL: EDITAR MÉDICO -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="lblEditar" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" method="post" action="/medicos.php?action=update">

        <div class="modal-header bg-warning">
          <h5 class="modal-title text-dark" id="lblEditar">
            <i class="fa-solid fa-pen me-2"></i>Editar médico
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="editIdMedico" name="ID_MEDICO" value="">

          <div class="mb-3">
            <label class="form-label">Nombre completo</label>
            <input type="text" id="editNombre" name="NOMBRE" class="form-control" required>
          </div>

            <div class="mb-3">
            <label class="form-label">Especialidad</label>
            <select id="editEspecialidad" name="ID_ESPECIALIDAD" class="form-select" required>
              <option value="">Selecciona...</option>
              <?php foreach ($especialidades as $esp): ?>
                <option value="<?= htmlspecialchars($esp['ID_ESPECIALIDAD']) ?>">
                  <?= htmlspecialchars($esp['NOMBRE']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Horario de Trabajo</label>
            <select id="editHorario" name="ID_HORARIO" class="form-select">
              <option value="">Sin horario asignado</option>
              <?php foreach ($horarios as $h): ?>
                <option value="<?= htmlspecialchars($h['ID_HORARIO']) ?>">
                  <?= htmlspecialchars($h['HORARIO']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cerrar</button>
          <button class="btn btn-warning" type="submit">Actualizar</button>
        </div>

      </form>
    </div>
  </div>


  <!-- MODAL: ELIMINAR MÉDICO -->
  <div class="modal fade" id="modalEliminarMedico" tabindex="-1" aria-labelledby="lblEliminarMedico" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" method="post" action="/medicos.php?action=delete">

        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="lblEliminarMedico">
            <i class="fa-solid fa-trash me-2"></i>Eliminar médico
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" id="deleteIdMedico" name="ID_MEDICO" value="">
          <p class="mb-0">¿Seguro que deseas eliminar este registro?</p>
        </div>

        <div class="modal-footer">
          <button class="btn btn-light" data-bs-dismiss="modal" type="button">Cancelar</button>
          <button class="btn btn-danger" type="submit">Eliminar</button>
        </div>

      </form>
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
        // --- EDITAR (ya lo tienes) ---
        const editarModal = document.getElementById('modalEditar');
        if (editarModal) {
            editarModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                document.getElementById('editIdMedico').value     = button.getAttribute('data-id') || '';
                document.getElementById('editNombre').value       = button.getAttribute('data-nombre') || '';
                document.getElementById('editEspecialidad').value = button.getAttribute('data-especialidad-id') || '';
                document.getElementById('editHorario').value      = button.getAttribute('data-id-horario') || '';
            });
        }

        // --- ELIMINAR ---
        const eliminarModal = document.getElementById('modalEliminarMedico');
        if (eliminarModal) {
            eliminarModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const idMedico = button.getAttribute('data-id') || '';

                const inputDelete = document.getElementById('deleteIdMedico');
                if (inputDelete) {
                    inputDelete.value = idMedico;
                }
            });
        }
    });
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
            const alert = document.querySelector('.alert');
            if (alert) alert.remove();
        }, 3000);
    </script>
</body>

</html>
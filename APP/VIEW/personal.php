<?php
require_once __DIR__ . '/../CONTROLLER/PersonalController.php';

$controller = new PersonalController();
$Personals = $controller->listarPersonals();
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
  <main class="container-xxl py-5 px-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-primary"><i class="fa-solid fa-user me-2"></i>Personal</h2>

      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
        <i class="fa-solid fa-plus me-1"></i> Nuevo Personal
      </button>
    </div>

    <!-- TABLA -->
    <div class="table-responsive shadow-sm">
      <table class="table table-sm table-hover align-middle">
        <thead class="table-primary">
          <tr>
            <th>ID</th>
            <th>Primer Nombre</th>
            <th>Segundo Nombre</th>
            <th>Puesto</th>
            <th>Activo</th>
            <th>Correo Electrónico</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Provincia</th>
            <th>Canton</th>
            <th>Distrito</th>



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
                <td><?= htmlspecialchars($p['PUESTO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['ACTIVO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['TELEFONO'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['DIRECCION'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['PROVINCIA'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['CANTON'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['DISTRITO'] ?? '') ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning me-1 btn-editar"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditar"
                    data-id="<?= htmlspecialchars($p['ID_PERSONAL'] ?? '') ?>"
                    data-pnombre="<?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?>"
                    data-snombre="<?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?>"
                    data-puesto="<?= htmlspecialchars($p['PUESTO'] ?? '') ?>"
                    data-activo="<?= htmlspecialchars($p['ACTIVO'] ?? '') ?>"
                    data-correo="<?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?>"
                    data-telefono="<?= htmlspecialchars($p['TELEFONO'] ?? '') ?>"
                    data-direccion="<?= htmlspecialchars($p['DIRECCION'] ?? '') ?>"
                    data-provincia="<?= htmlspecialchars($p['PROVINCIA'] ?? '') ?>"
                    data-canton="<?= htmlspecialchars($p['CANTON'] ?? '') ?>"
                    data-distrito="<?= htmlspecialchars($p['DISTRITO'] ?? '') ?>">
                    <i class="fa-solid fa-pen"></i>
                  </button>


                  <button
                    class="btn btn-sm btn-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#modalDesactivarPersonal"
                    data-id="<?= htmlspecialchars($p['ID_PERSONAL'] ?? '') ?>"
                    data-nombre="<?= htmlspecialchars($p['PRIMER_NOMBRE'] . ' ' . ($p['SEGUNDO_NOMBRE'] ?? '')) ?>"
                    data-puesto="<?= htmlspecialchars($p['PUESTO'] ?? '') ?>">
                    <i class="fa-solid fa-user-slash"></i>
                  </button>


                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="13" class="text-center text-muted">No hay Personals registrados</td>
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
        <form method="post" action="app/controllers/PersonalController.php" autocomplete="off">
          <div class="modal-header">
            <h5 class="modal-title" id="modalAgregarLabel">Nuevo Personal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <input type="hidden" name="action" value="create">

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
                <label class="form-label">Puesto</label>
                <input type="text" class="form-control" name="PUESTO" required>
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
        <form id="formEditarPersonal" method="post" action="app/controllers/PersonalController.php">

          <div class="modal-header">
            <h5 class="modal-title" id="modalEditarLabel">Editar Personal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>

          <div class="modal-body">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="ID_PERSONAL" id="edit-id">

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
                <label for="edit-puesto" class="form-label">Puesto</label>
                <input type="text" class="form-control" id="edit-puesto" name="PUESTO" required>
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


  <!-- MODAL: DESACTIVAR PERSONAL -->
  <div class="modal fade" id="modalDesactivarPersonal" tabindex="-1" aria-labelledby="lblDesactivarPersonal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="lblDesactivarPersonal">
            <i class="fa-solid fa-user-slash me-2"></i>Desactivar empleado
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <form id="formDesactivarPersonal" method="post" action="app/controllers/PersonalController.php">
          <div class="modal-body">
            <input type="hidden" name="action" value="deactivate">
            <input type="hidden" name="ID_PERSONAL" id="deact-id">
            <p class="mb-3">
              Confirmá que querés desactivar a <strong id="deact-nombre"></strong>.
            </p>


          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-danger">Desactivar</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>




  <!-- FOOTER -->
  <footer class="py-5 mt-auto" style="color:#fff;">
    <div class="container">
      <div class="text-center small">
        <p>© 2025 Clinica Biblica. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- SCRIPT PARA RELLENAR EL MODAL -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const modalEl = document.getElementById('modalEditar');
      modalEl.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;

        // Lee todos los data-* del botón
        const id = button.getAttribute('data-id') || '';
        const pnombre = button.getAttribute('data-pnombre') || '';
        const snombre = button.getAttribute('data-snombre') || '';
        const puesto = button.getAttribute('data-puesto') || '';
        const activo = button.getAttribute('data-activo') || '';
        const correo = button.getAttribute('data-correo') || '';
        const telefono = button.getAttribute('data-telefono') || '';
        const direccion = button.getAttribute('data-direccion') || '';
        const provincia = button.getAttribute('data-provincia') || '';
        const canton = button.getAttribute('data-canton') || '';
        const distrito = button.getAttribute('data-distrito') || '';

        // Asigna a los campos del formulario
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-pnombre').value = pnombre;
        document.getElementById('edit-snombre').value = snombre;
        document.getElementById('edit-puesto').value = puesto;
        document.getElementById('edit-activo').value = activo;
        document.getElementById('edit-correo').value = correo;
        document.getElementById('edit-telefono').value = telefono;
        document.getElementById('edit-direccion').value = direccion;
        document.getElementById('edit-provincia').value = provincia;
        document.getElementById('edit-canton').value = canton;
        document.getElementById('edit-distrito').value = distrito;
      });
    });
    document.addEventListener('DOMContentLoaded', () => {
      const modal = document.getElementById('modalDesactivarPersonal');
      if (!modal) return;

      modal.addEventListener('show.bs.modal', e => {
        const b = e.relatedTarget;
        const id = b.getAttribute('data-id') || '';
        const nombre = b.getAttribute('data-nombre') || 'Empleado';
        const puesto = b.getAttribute('data-puesto') || '';

        document.getElementById('deact-id').value = id;
        document.getElementById('deact-nombre').textContent = puesto ?
          `${nombre} | ${puesto}` :
          nombre;
      });

      modal.addEventListener('hidden.bs.modal', () => {
        document.getElementById('deact-nombre').textContent = '';
        document.getElementById('deact-id').value = '';
      });
    });
  </script>

</body>

</html>
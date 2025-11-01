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




<main class="container py-5">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-chart-line me-2"></i>Reportes</h2>
  </div>

  <!-- FILTROS -->
  <form class="row g-2 mb-4" method="get" action="/reportes.php">
    <div class="col-md-3">
      <label class="form-label small mb-1">Fecha desde</label>
      <input type="date" class="form-control" name="desde" value="">
    </div>
    <div class="col-md-3">
      <label class="form-label small mb-1">Fecha hasta</label>
      <input type="date" class="form-control" name="hasta" value="">
    </div>
    <div class="col-md-3">
      <label class="form-label small mb-1">Estado de cita</label>
      <select class="form-select" name="estado">
        <option value="">Todos</option>
        <option>Pendiente</option>
        <option>Confirmada</option>
        <option>Atendida</option>
        <option>Cancelada</option>
        <option>No asistió</option>
      </select>
    </div>
    <div class="col-md-3 d-grid align-self-end">
      <button class="btn btn-secondary"><i class="fa-solid fa-filter me-1"></i> Aplicar</button>
    </div>
  </form>

  <!-- KPIs -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1 small">Pacientes activos</p>
              <h3 class="mb-0 fw-bold"><?= htmlspecialchars($kpi_pacientes ?? '—') ?></h3>
            </div>
            <i class="fa-solid fa-users fa-2x text-primary"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1 small">Citas en rango</p>
              <h3 class="mb-0 fw-bold"><?= htmlspecialchars($kpi_citas ?? '—') ?></h3>
            </div>
            <i class="fa-solid fa-calendar-check fa-2x text-primary"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1 small">Atenciones registradas</p>
              <h3 class="mb-0 fw-bold"><?= htmlspecialchars($kpi_atenciones ?? '—') ?></h3>
            </div>
            <i class="fa-solid fa-stethoscope fa-2x text-primary"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TABLAS DE RESUMEN -->
  <div class="row g-4">
    <!-- Citas por estado -->
    <div class="col-12 col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-light">
          <strong>Citas por estado</strong> <?= isset($filtro_titulo) ? htmlspecialchars($filtro_titulo) : '' ?>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
              <thead class="table-primary">
                <tr>
                  <th>Estado</th>
                  <th class="text-end">Cantidad</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($res_citas_estado)): ?>
                  <?php foreach ($res_citas_estado as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['estado']) ?></td>
                    <td class="text-end"><?= htmlspecialchars($r['cantidad']) ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="2" class="text-center text-muted py-3">Sin datos</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Atenciones por médico -->
    <div class="col-12 col-lg-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-light">
          <strong>Atenciones por médico</strong>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
              <thead class="table-primary">
                <tr>
                  <th>Médico</th>
                  <th class="text-end">Atenciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($res_atenciones_medico)): ?>
                  <?php foreach ($res_atenciones_medico as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['medico']) ?></td>
                    <td class="text-end"><?= htmlspecialchars($r['atenciones']) ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="2" class="text-center text-muted py-3">Sin datos</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Pacientes nuevos por mes -->
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <strong>Pacientes nuevos por mes</strong>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
              <thead class="table-primary">
                <tr>
                  <th>Mes</th>
                  <th class="text-end">Nuevos</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($res_pacientes_mes)): ?>
                  <?php foreach ($res_pacientes_mes as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['mes']) ?></td>
                    <td class="text-end"><?= htmlspecialchars($r['nuevos']) ?></td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="2" class="text-center text-muted py-3">Sin datos</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>

</main>





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
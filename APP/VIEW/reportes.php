<?php
require_once __DIR__ . '/../CONTROLLER/DashboardController.php';

$ctrl = new DashboardController();

/* Tablas */
$data = $ctrl->getTablas();
$res_citas_estado       = $data['citas_por_estado']      ?? [];
$res_atenciones_medico  = $data['atenciones_por_medico'] ?? [];
$res_pacientes_mes      = $data['pacientes_nuevos_mes']  ?? [];

/* KPIs */
$kpis = $ctrl->getKpis();
$kpi_pacientes  = $kpis['pacientes']  ?? 0;
$kpi_citas      = $kpis['citas']      ?? 0;
$kpi_atenciones = $kpis['atenciones'] ?? 0;
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
    <!-- KPIs -->
    <div class="row g-4 mb-4">

      <!-- Pacientes -->
      <div class="col-12 col-md-4">
        <div class="card shadow-sm border">
          <div class="card-body text-center">
            <div class="d-flex justify-content-center mb-2">
              <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                <i class="fa-solid fa-users text-primary fs-4"></i>
              </div>
            </div>
            <h6 class="text-muted mb-1">Pacientes activos</h6>
            <h2 class="fw-bold mb-0"><?= number_format($kpi_pacientes) ?></h2>
          </div>
        </div>
      </div>

      <!-- Citas -->
      <div class="col-12 col-md-4">
        <div class="card shadow-sm border">
          <div class="card-body text-center">
            <div class="d-flex justify-content-center mb-2">
              <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                <i class="fa-solid fa-calendar-check text-primary fs-4"></i>
              </div>
            </div>
            <h6 class="text-muted mb-1">Citas en rango</h6>
            <h2 class="fw-bold mb-0"><?= number_format($kpi_citas) ?></h2>
          </div>
        </div>
      </div>

      <!-- Atenciones -->
      <div class="col-12 col-md-4">
        <div class="card shadow-sm border">
          <div class="card-body text-center">
            <div class="d-flex justify-content-center mb-2">
              <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                <i class="fa-solid fa-stethoscope text-primary fs-4"></i>
              </div>
            </div>
            <h6 class="text-muted mb-1">Atenciones registradas</h6>
            <h2 class="fw-bold mb-0"><?= number_format($kpi_atenciones) ?></h2>
          </div>
        </div>
      </div>

    </div>

    <!-- TABLAS -->
    <div class="row g-4">

      <!-- Citas por estado -->
      <div class="col-12 col-lg-6">
        <h6 class="text-muted fw-semibold mb-2">Citas por estado</h6>
        <div class="table-responsive shadow-sm table-wrap">
          <table class="table table-hover align-middle">
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
                    <td class="text-end"><?= number_format((int)$r['cantidad']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="2" class="text-center text-muted">Sin datos</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Atenciones por médico -->
      <div class="col-12 col-lg-6">
        <h6 class="text-muted fw-semibold mb-2">Atenciones por médico</h6>
        <div class="table-responsive shadow-sm table-wrap">
          <table class="table table-hover align-middle">
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
                    <td class="text-end"><?= number_format((int)$r['atenciones']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="2" class="text-center text-muted">Sin datos</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pacientes nuevos por mes -->
      <div class="col-12">
        <h6 class="text-muted fw-semibold mb-2">Pacientes nuevos por mes</h6>
        <div class="table-responsive shadow-sm table-wrap">
          <table class="table table-hover align-middle">
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
                    <td class="text-end"><?= number_format((int)$r['nuevos']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="2" class="text-center text-muted">Sin datos</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
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
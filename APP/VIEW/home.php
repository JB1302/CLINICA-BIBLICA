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

<main>

<section class="hero-section">
  <div class="container text-center">
    <h1 class="display-5 fw-bold text-white mb-3">Bienvenido al Sistema de Gestión Clínica</h1>

    <p class="lead text-hero mb-4">
      Acceso centralizado, rápido y seguro a la información médica para optimizar procesos y brindar una mejor atención en salud.
    </p>

    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-7">
        <div class="goals-card shadow-lg">
          <div class="goals-head">
            <span class="goals-chip"><i class="fa-solid fa-bullseye me-2"></i>Objetivos</span>
          </div>

          <ul class="goals-list">
            <li class="goals-item">
              <span class="goals-icon"><i class="fa-solid fa-database"></i></span>
              <span>Gestión centralizada</span>
            </li>
            <li class="goals-item">
              <span class="goals-icon"><i class="fa-solid fa-shield-halved"></i></span>
              <span>Reducción de errores</span>
            </li>
            <li class="goals-item">
              <span class="goals-icon"><i class="fa-solid fa-gauge-high"></i></span>
              <span>Optimización de procesos</span>
            </li>
            <li class="goals-item">
              <span class="goals-icon"><i class="fa-solid fa-chart-line"></i></span>
              <span>Reportes operativos</span>
            </li>
          </ul>
        </div>
      </div>
    </div>

  </div>
</section>

<section class="modules-section py-5">
  <div class="container text-center">
    <h2 class="h4 fw-bold mb-4 text-primary">Módulos principales</h2>

    <div class="row g-4 justify-content-center">

      <div class="col-12 col-md-3">
        <a href="/pacientes.php" class="text-decoration-none">
          <div class="card h-100 module-card text-center">
            <div class="card-body">
              <i class="fa-solid fa-user fa-2x mb-2 text-primary"></i>
              <h5 class="fw-bold">Pacientes</h5>
              <p class="small text-muted">Registro y gestión de pacientes.</p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-3">
        <a href="/citas.php" class="text-decoration-none">
          <div class="card h-100 module-card text-center">
            <div class="card-body">
              <i class="fa-solid fa-calendar-check fa-2x mb-2 text-primary"></i>
              <h5 class="fw-bold">Citas</h5>
              <p class="small text-muted">Programación y control de citas.</p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-3">
        <a href="/medicos.php" class="text-decoration-none">
          <div class="card h-100 module-card text-center">
            <div class="card-body">
              <i class="fa-solid fa-stethoscope fa-2x mb-2 text-primary"></i>
              <h5 class="fw-bold">Médicos</h5>
              <p class="small text-muted">Gestión del personal médico.</p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-md-3">
        <a href="/reportes.php" class="text-decoration-none">
          <div class="card h-100 module-card text-center">
            <div class="card-body">
              <i class="fa-solid fa-chart-line fa-2x mb-2 text-primary"></i>
              <h5 class="fw-bold">Reportes</h5>
              <p class="small text-muted">Indicadores y métricas clave.</p>
            </div>
          </div>
        </a>
      </div>

    </div>
  </div>
</section>

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

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
    <h2 class="fw-bold text-primary"><i class="fa-solid fa-user-doctor me-2"></i>Médicos</h2>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearMedico">
      <i class="fa-solid fa-plus me-1"></i> Nuevo médico
    </button>
  </div>

  <!-- BÚSQUEDA -->
  <form class="row g-2 mb-4" method="get" action="/medicos.php">
    <div class="col-md-4">
      <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o colegiado" value="">
    </div>
    <div class="col-md-3">
      <input type="text" name="colegiado" class="form-control" placeholder="N° colegiado" value="">
    </div>
    <div class="col-md-3">
      <select name="especialidad" class="form-select">
        <option value="">Todas las especialidades</option>
        <!-- Rellenar con PHP desde ESPECIALIDAD -->
        <option value="1">Medicina General</option>
        <option value="2">Pediatría</option>
        <option value="3">Cardiología</option>
      </select>
    </div>
    <div class="col-md-2 d-grid">
      <button class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
    </div>
  </form>

  <div class="table-responsive shadow-sm">
    <table class="table table-hover align-middle">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Colegiado</th>
          <th>Especialidad</th>
          <th class="text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>7</td>
          <td>Dr. Carlos Quesada</td>
          <td>MED-45123</td>
          <td data-especialidad-id="1">Medicina General</td>
          <td class="text-center">
            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditarMedico">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarMedico">
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>
        </tr>

        <tr>
          <td>3</td>
          <td>Dra. Laura Solano</td>
          <td>MED-33890</td>
          <td data-especialidad-id="3">Cardiología</td>
          <td class="text-center">
            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEditarMedico">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalEliminarMedico">
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

</main>

<div class="modal fade" id="modalCrearMedico" tabindex="-1" aria-labelledby="lblCrearMedico" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="/medicos.php?action=create">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="lblCrearMedico"><i class="fa-solid fa-user-plus me-2"></i>Nuevo médico</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        <div class="mb-3">
          <label class="form-label">Nombre completo</label>
          <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">N° Colegiado</label>
          <input type="text" name="colegiado" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Especialidad</label>
          <select name="especialidad_id" class="form-select" required>
            <option value="">Selecciona...</option>
            <option value="1">Medicina General</option>
            <option value="2">Pediatría</option>
            <option value="3">Cardiología</option>
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

<div class="modal fade" id="modalEditarMedico" tabindex="-1" aria-labelledby="lblEditarMedico" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="/medicos.php?action=update">
      <div class="modal-header bg-warning">
        <h5 class="modal-title text-dark" id="lblEditarMedico"><i class="fa-solid fa-pen me-2"></i>Editar médico</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" value="7">

        <div class="mb-3">
          <label class="form-label">Nombre completo</label>
          <input type="text" name="nombre" class="form-control" value="Dr. Carlos Quesada" required>
        </div>

        <div class="mb-3">
          <label class="form-label">N° Colegiado</label>
          <input type="text" name="colegiado" class="form-control" value="MED-45123" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Especialidad</label>
          <select name="especialidad_id" class="form-select" required>
            <option value="1" selected>Medicina General</option>
            <option value="2">Pediatría</option>
            <option value="3">Cardiología</option>
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

<div class="modal fade" id="modalEliminarMedico" tabindex="-1" aria-labelledby="lblEliminarMedico" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="/medicos.php?action=delete">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="lblEliminarMedico"><i class="fa-solid fa-trash me-2"></i>Eliminar médico</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" value="7">
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
</body>

</html>
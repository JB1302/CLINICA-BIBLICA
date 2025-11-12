<?php
require_once __DIR__ . '/../CONTROLLER/PacienteController.php';

$controller = new PacienteController();
$pacientes = $controller->listarPacientes();
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

        <!-- NAVBAR -->
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

        <!-- MAIN -->
        <main class="container-xxl py-5 px-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary"><i class="fa-solid fa-user me-2"></i>Pacientes</h2>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                    <i class="fa-solid fa-plus me-1"></i> Nuevo Paciente
                </button>
            </div>

            <!-- TABLA -->
            <div class="table-responsive shadow-sm">
                <table class="table table-sm table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Primer Nombre</th>
                            <th>Segundo Nombre</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Fecha Nacimiento</th>
                            <th>Sexo</th>
                            <th>Observaciones</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Correo Electrónico</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pacientes)): ?>
                            <?php foreach ($pacientes as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CEDULA'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['FECHA_NACIMIENTO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEXO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['OBSERVACIONES'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['TELEFONO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['DIRECCION'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning me-1 btn-editar"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditar"
                                                data-id="<?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?>"
                                                data-cedula="<?= htmlspecialchars($p['CEDULA'] ?? '') ?>"
                                                data-pnombre="<?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?>"
                                                data-snombre="<?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?>"
                                                data-papellido="<?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?>"
                                                data-sapellido="<?= htmlspecialchars($p['SEGUNDO_APELLIDO'] ?? '') ?>"
                                                data-fecha="<?= htmlspecialchars($p['FECHA_NACIMIENTO'] ?? '') ?>"
                                                data-sexo="<?= htmlspecialchars($p['SEXO'] ?? '') ?>"
                                                data-observaciones="<?= htmlspecialchars($p['OBSERVACIONES'] ?? '') ?>"
                                                data-telefono="<?= htmlspecialchars($p['TELEFONO'] ?? '') ?>"
                                                data-direccion="<?= htmlspecialchars($p['DIRECCION'] ?? '') ?>"
                                                data-correo="<?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?>"
                                                >
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13" class="text-center text-muted">No hay pacientes registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- MODAL EDITAR -->
        <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-dark">Editar Paciente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <form id="formEditar">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Cédula</label>
                                    <input type="text" class="form-control" id="editCedula">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Primer Nombre</label>
                                    <input type="text" class="form-control" id="editPrimerNombre">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Segundo Nombre</label>
                                    <input type="text" class="form-control" id="editSegundoNombre">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Primer Apellido</label>
                                    <input type="text" class="form-control" id="editPrimerApellido">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Segundo Apellido</label>
                                    <input type="text" class="form-control" id="editSegundoApellido">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="editFechaNacimiento">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Sexo</label>
                                    <select class="form-select" id="editSexo">
                                        <option value="">Seleccione</option>
                                        <option value="F">Femenino</option>
                                        <option value="M">Masculino</option>
                                    </select>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">Observaciones</label>
                                    <textarea class="form-control" rows="2" id="editObservaciones"></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="editTelefono">
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="editDireccion">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="editCorreo">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-warning w-100">Actualizar</button>
                            </div>
                        </form>
                    </div>

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
            document.addEventListener('DOMContentLoaded', () => {
                const editarModal = document.getElementById('modalEditar');

                editarModal.addEventListener('show.bs.modal', event => {
                    const button = event.relatedTarget;

                    document.getElementById('editCedula').value = button.getAttribute('data-cedula') || '';
                    document.getElementById('editPrimerNombre').value = button.getAttribute('data-pnombre') || '';
                    document.getElementById('editSegundoNombre').value = button.getAttribute('data-snombre') || '';
                    document.getElementById('editPrimerApellido').value = button.getAttribute('data-papellido') || '';
                    document.getElementById('editSegundoApellido').value = button.getAttribute('data-sapellido') || '';
                    document.getElementById('editFechaNacimiento').value = button.getAttribute('data-fecha') || '';
                    document.getElementById('editSexo').value = button.getAttribute('data-sexo') || '';
                    document.getElementById('editObservaciones').value = button.getAttribute('data-observaciones') || '';
                    document.getElementById('editTelefono').value = button.getAttribute('data-telefono') || '';
                    document.getElementById('editDireccion').value = button.getAttribute('data-direccion') || '';
                    document.getElementById('editCorreo').value = button.getAttribute('data-correo') || '';
                });
            });
        </script>
    </body>
</html>

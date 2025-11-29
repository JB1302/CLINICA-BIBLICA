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

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                switch($_GET['success']) {
                    case '1': echo 'Paciente creado exitosamente'; break;
                    case '2': echo 'Paciente actualizado exitosamente'; break;
                    case '3': echo 'Paciente eliminado exitosamente'; break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Error al procesar la solicitud. Intente nuevamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

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
                                        data-correo="<?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?>">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-eliminar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEliminar"
                                        data-id="<?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?>">
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

    <!-- MODAL AGREGAR PACIENTE -->
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <form method="post" action="/pacientes.php" autocomplete="off">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAgregarLabel">Nuevo Paciente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Cédula</label>
                                <input type="text" class="form-control" name="CEDULA" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" name="PRIMER_NOMBRE" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" name="SEGUNDO_NOMBRE">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" name="PRIMER_APELLIDO" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" name="SEGUNDO_APELLIDO">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" name="FECHA_NACIMIENTO" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sexo</label>
                                <select class="form-select" name="SEXO" required>
                                    <option value="">Seleccione</option>
                                    <option value="F">Femenino</option>
                                    <option value="M">Masculino</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control" rows="2" name="OBSERVACIONES"></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="TELEFONO">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control" name="DIRECCION">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" name="CORREO_ELECTRONICO">
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
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" action="/pacientes.php" autocomplete="off">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="ID_PACIENTE" id="editIdPaciente">

                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-dark">Editar Paciente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Cédula</label>
                                <input type="text" class="form-control" name="CEDULA" id="editCedula" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" name="PRIMER_NOMBRE" id="editPrimerNombre" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" name="SEGUNDO_NOMBRE" id="editSegundoNombre">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" name="PRIMER_APELLIDO" id="editPrimerApellido" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" name="SEGUNDO_APELLIDO" id="editSegundoApellido">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" name="FECHA_NACIMIENTO" id="editFechaNacimiento" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sexo</label>
                                <select class="form-select" name="SEXO" id="editSexo" required>
                                    <option value="">Seleccione</option>
                                    <option value="F">Femenino</option>
                                    <option value="M">Masculino</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control" rows="2" name="OBSERVACIONES" id="editObservaciones"></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" name="TELEFONO" id="editTelefono">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control" name="DIRECCION" id="editDireccion">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" name="CORREO_ELECTRONICO" id="editCorreo">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL ELIMINAR -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="/pacientes.php">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="ID_PACIENTE" id="deleteIdPaciente">

                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Eliminar Paciente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>¿Está seguro que desea eliminar este paciente?</p>
                        <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
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
        document.addEventListener('DOMContentLoaded', () => {
            const editarModal = document.getElementById('modalEditar');

            editarModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;

                document.getElementById('editIdPaciente').value = button.getAttribute('data-id') || '';
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

            const eliminarModal = document.getElementById('modalEliminar');

            eliminarModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                document.getElementById('deleteIdPaciente').value = button.getAttribute('data-id') || '';
            });
        });
    </script>
</body>

</html>
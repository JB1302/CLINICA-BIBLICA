<?php
require_once __DIR__ . '/../CONTROLLER/PacienteController.php';

$controller = new PacienteController();

// Verifica si la solicitud enviada por el usuario es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'create':
            $res = $controller->crear($_POST);
            break;

        case 'update':
            $res = $controller->actualizar($_POST);
            break;

        case 'delete':
            $id = isset($_POST['ID_PACIENTE']) ? (int)$_POST['ID_PACIENTE'] : 0;
            $res = $controller->eliminar($id);
            break;

        default:
            $res = ['resultado' => 0, 'mensaje' => 'Acción no válida'];
            break;
    }

    // POST-Redirect-GET para no repetir envíos al refrescar
    $ok  = $res['resultado'] ?? 0;
    $msg = $res['mensaje']   ?? '';
    header('Location: pacientes.php?msg=' . urlencode($res['mensaje'])
                               . '&ok=' . (int)($res['resultado'] ?? 0));
    exit;
}
$filtro = $_GET['filter'] ?? 'todos';

switch ($filtro) {
    case 'gmail':
        $pacientes = $controller->listarPacientesGmail();
        break;

    case 'ia':
        $pacientes = $controller->listarPacientesIa();
        break;
    
    case 'provincia':
        $pacientes = $controller->listarPacientesProvincia();
        break;

    case 'telefono':
        $pacientes = $controller->listarPacientesTelefono();
        break;

    default: // 'todos' u otro valor
        $pacientes = $controller->listarPacientes();
        break;
}
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
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                    <div>
                        <h3 class="fw-bold text-primary mb-0">
                            <i class="fa-solid fa-user me-2"></i>Gestión de Pacientes
                        </h3>

                        <!-- Filtros -->
                        <div class="mt-3">
                            <div class="btn-group" role="group" aria-label="Filtros de pacientes">
                                <a href="pacientes.php?filter=todos"
                                class="btn btn-sm <?= ($filtro === 'todos' ? 'btn-secondary' : 'btn-outline-secondary') ?>">
                                    Todos
                                </a>

                                <a href="pacientes.php?filter=gmail"
                                class="btn btn-sm <?= ($filtro === 'gmail' ? 'btn-primary' : 'btn-outline-primary') ?>">
                                    Gmail
                                </a>
                                <a href="pacientes.php?filter=ia"
                                class="btn btn-sm <?= ($filtro === 'ia' ? 'btn-primary' : 'btn-outline-primary') ?>">
                                    Nombres en "ía"
                                </a>
                                <!-- Los otros filtros por ahora solo de diseño -->
                                 <a href="pacientes.php?filter=telefono"
                                class="btn btn-sm <?= ($filtro === 'telefono' ? 'btn-primary' : 'btn-outline-primary') ?>">
                                    Teléfonos
                                </a>
                                <a href="pacientes.php?filter=provincia"
                                class="btn btn-sm <?= ($filtro === 'provincia' ? 'btn-primary' : 'btn-outline-primary') ?>">
                                    Alajuela/Heredia
                                </a>
                            </div>
                        </div>
                        <!-- Fin filtros -->
                    </div>
                    <?php if ($filtro === 'todos'): ?>
                        <button class="btn btn-primary align-self-center" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                            <i class="fa-solid fa-plus me-1"></i> Nuevo Paciente
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- TABLA -->
        <div class="table-responsive shadow-sm">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <?php if ($filtro === 'gmail'): ?>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Primer Nombre</th>
                            <th>Segundo Nombre</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Correo Electrónico</th>
                        <?php elseif ($filtro === 'ia'): ?>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Primer Nombre</th>
                            <th>Segundo Nombre</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Correo Electrónico</th>
                        <?php elseif ($filtro === 'provincia'): ?>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Primer Nombre</th>
                            <th>Segundo Nombre</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Dirección</th>
                        <?php elseif ($filtro === 'telefono'): ?>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Primer Nombre</th>
                            <th>Segundo Nombre</th>
                            <th>Primer Apellido</th>
                            <th>Segundo Apellido</th>
                            <th>Teléfono formato +506</th>
                        <?php else: ?>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Primer Nombre</th>
                            <th>Primer Apellido</th>
                            <th>Teléfono</th>
                            <th>Correo Electrónico</th>
                            <th class="text-center">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pacientes)): ?>
                        <?php foreach ($pacientes as $p): ?>
                            <tr>
                                <?php if ($filtro === 'gmail'): ?>
                                    <td><?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CEDULA'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?></td>
                                <?php elseif ($filtro === 'ia'): ?>
                                    <td><?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CEDULA'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?></td>
                                <?php elseif ($filtro === 'provincia'): ?>
                                    <td><?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CEDULA'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['DIRECCION'] ?? '') ?></td>
                                <?php elseif ($filtro === 'telefono'): ?>
                                    <td><?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CEDULA'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['SEGUNDO_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['TELEFONO_FORMATO_506'] ?? '') ?></td>
                                <?php else: ?>
                                    <td><?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CEDULA'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_NOMBRE'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['PRIMER_APELLIDO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['TELEFONO'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['CORREO_ELECTRONICO'] ?? '') ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-1" title="Ver detalle"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalVer"
                                        data-id="<?= $p['ID_PACIENTE'] ?>"
                                        data-cedula="<?= $p['CEDULA'] ?>"
                                        data-pnombre="<?= $p['PRIMER_NOMBRE'] ?>"
                                        data-snombre="<?= $p['SEGUNDO_NOMBRE'] ?>"
                                        data-papellido="<?= $p['PRIMER_APELLIDO'] ?>"
                                        data-sapellido="<?= $p['SEGUNDO_APELLIDO'] ?>"
                                        data-fecha="<?= $p['FECHA_NACIMIENTO'] ?>"
                                        data-sexo="<?= $p['SEXO'] ?>"
                                        data-observaciones="<?= $p['OBSERVACIONES'] ?>"
                                        data-telefono="<?= $p['TELEFONO'] ?>"
                                        data-direccion="<?= $p['DIRECCION'] ?>"
                                        data-correo="<?= $p['CORREO_ELECTRONICO'] ?>">
                                        <i class="fa-solid fa-eye"></i>
                                        </button>
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

                                        <form method="post" action="pacientes.php" class="d-inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="ID_PACIENTE" value="<?= htmlspecialchars($p['ID_PACIENTE'] ?? '') ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Está seguro que desea eliminar este paciente?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
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
                <form id="formNuevoPaciente" method="post" action="pacientes.php" autocomplete="off">
                    <input type="hidden" name="action" value="create">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAgregarLabel">Nuevo Paciente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="alertaNuevoPaciente" class="alert alert-danger d-none" role="alert"></div>
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


    <!-- Modal ver detalle paciente -->
    <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                <i class="fa-solid fa-eye me-2"></i>Ver Paciente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Cédula</label>
                    <input type="text" class="form-control" id="viewCedula" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Primer Nombre</label>
                    <input type="text" class="form-control" id="viewPrimerNombre" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Segundo Nombre</label>
                    <input type="text" class="form-control" id="viewSegundoNombre" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Primer Apellido</label>
                    <input type="text" class="form-control" id="viewPrimerApellido" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Segundo Apellido</label>
                    <input type="text" class="form-control" id="viewSegundoApellido" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="viewFechaNacimiento" readonly>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sexo</label>
                    <input type="text" class="form-control" id="viewSexo" readonly>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Observaciones</label>
                    <textarea class="form-control" rows="2" id="viewObservaciones" readonly></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="viewTelefono" readonly>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="viewDireccion" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="viewCorreo" readonly>
                </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary w-100" data-bs-dismiss="modal">
                Cerrar
                </button>
            </div>

            </div>
        </div>
    </div>


    <!-- MODAL EDITAR -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark">Editar Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <form id="formEditarPaciente" method="post" action="pacientes.php" autocomplete="off">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="ID_PACIENTE" id="editIdPaciente">
                        <div id="alertaEditarPaciente" class="alert alert-danger d-none" role="alert"></div>        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Cédula</label>
                                <input type="text" class="form-control" id="editCedula" name="CEDULA">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" id="editPrimerNombre" name="PRIMER_NOMBRE">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" id="editSegundoNombre"name="SEGUNDO_NOMBRE">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" id="editPrimerApellido" name="PRIMER_APELLIDO">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="editSegundoApellido" name="SEGUNDO_APELLIDO">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="editFechaNacimiento" name="FECHA_NACIMIENTO">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sexo</label>
                                <select class="form-select" id="editSexo" name="SEXO">
                                    <option value="">Seleccione</option>
                                    <option value="F">Femenino</option>
                                    <option value="M">Masculino</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control" rows="2" id="editObservaciones" name="OBSERVACIONES"></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="editTelefono" name="TELEFONO">
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="editDireccion" name="DIRECCION">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="editCorreo" name="CORREO_ELECTRONICO">
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
        document.addEventListener('DOMContentLoaded', () => {
        const editarModal = document.getElementById('modalEditar');

        if (editarModal) {
            // Al abrir: rellenar campos y limpiar mensaje de error viejo
            editarModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;

                document.getElementById('editIdPaciente').value        = button.getAttribute('data-id') || '';
                document.getElementById('editCedula').value            = button.getAttribute('data-cedula') || '';
                document.getElementById('editPrimerNombre').value      = button.getAttribute('data-pnombre') || '';
                document.getElementById('editSegundoNombre').value     = button.getAttribute('data-snombre') || '';
                document.getElementById('editPrimerApellido').value    = button.getAttribute('data-papellido') || '';
                document.getElementById('editSegundoApellido').value   = button.getAttribute('data-sapellido') || '';
                document.getElementById('editFechaNacimiento').value   = button.getAttribute('data-fecha') || '';
                document.getElementById('editSexo').value              = button.getAttribute('data-sexo') || '';
                document.getElementById('editObservaciones').value     = button.getAttribute('data-observaciones') || '';
                document.getElementById('editTelefono').value          = button.getAttribute('data-telefono') || '';
                document.getElementById('editDireccion').value         = button.getAttribute('data-direccion') || '';
                document.getElementById('editCorreo').value            = button.getAttribute('data-correo') || '';

                const alertaEditar = document.getElementById('alertaEditarPaciente');
                if (alertaEditar) {
                    alertaEditar.classList.add('d-none');
                    alertaEditar.textContent = '';
                }
            });

            // Al cerrar: limpiar errores y (opcional) resetear el form
            editarModal.addEventListener('hidden.bs.modal', () => {
                const formEditar = document.getElementById('formEditarPaciente');
                if (formEditar) {
                    // Si NO quieres resetear los valores, comenta esta línea:
                    formEditar.reset();
                }

                const alertaEditar = document.getElementById('alertaEditarPaciente');
                if (alertaEditar) {
                    alertaEditar.classList.add('d-none');
                    alertaEditar.textContent = '';
                }
            });
        }

        // Modal AGREGAR – limpiar errores al abrir y cerrar
        const modalAgregar = document.getElementById('modalAgregar');
        if (modalAgregar) {
            modalAgregar.addEventListener('show.bs.modal', () => {
                const alertaNuevo = document.getElementById('alertaNuevoPaciente');
                if (alertaNuevo) {
                    alertaNuevo.classList.add('d-none');
                    alertaNuevo.textContent = '';
                }
            });

            modalAgregar.addEventListener('hidden.bs.modal', () => {
                const formNuevo = document.getElementById('formNuevoPaciente');
                if (formNuevo) {
                    formNuevo.reset(); // si no quieres resetear, comenta esta línea
                }

                const alertaNuevo = document.getElementById('alertaNuevoPaciente');
                if (alertaNuevo) {
                    alertaNuevo.classList.add('d-none');
                    alertaNuevo.textContent = '';
                }
            });
        }
    });
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

                fetch('pacientes.php', {
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
                            const mensaje = msg || 'No se pudo guardar el paciente.';
                            alerta.textContent = mensaje;
                            alerta.classList.remove('d-none');

                            const modalContent = form.closest('.modal-content');
                            if (modalContent) modalContent.scrollTop = 0;
                        }
                    } else {
                        // Por si acaso no hubo redirect, recargo
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
            enviarFormularioAjax('formNuevoPaciente',   'alertaNuevoPaciente');
            enviarFormularioAjax('formEditarPaciente',  'alertaEditarPaciente');
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
            const alertGlobal = document.getElementById('alertGlobal');
            if (alertGlobal) alertGlobal.remove();
        }, 3000);
    </script>

    <script>
        document.getElementById('modalVer').addEventListener('show.bs.modal', event => {
        const btn = event.relatedTarget;

        document.getElementById('viewCedula').value          = btn.getAttribute('data-cedula') || '';
        document.getElementById('viewPrimerNombre').value    = btn.getAttribute('data-pnombre') || '';
        document.getElementById('viewSegundoNombre').value   = btn.getAttribute('data-snombre') || '';
        document.getElementById('viewPrimerApellido').value  = btn.getAttribute('data-papellido') || '';
        document.getElementById('viewSegundoApellido').value = btn.getAttribute('data-sapellido') || '';
        document.getElementById('viewFechaNacimiento').value = btn.getAttribute('data-fecha') || '';
        document.getElementById('viewSexo').value            = btn.getAttribute('data-sexo') || '';
        document.getElementById('viewObservaciones').value   = btn.getAttribute('data-observaciones') || '';
        document.getElementById('viewTelefono').value        = btn.getAttribute('data-telefono') || '';
        document.getElementById('viewDireccion').value       = btn.getAttribute('data-direccion') || '';
        document.getElementById('viewCorreo').value          = btn.getAttribute('data-correo') || '';
        });
    </script>


</body>

</html>
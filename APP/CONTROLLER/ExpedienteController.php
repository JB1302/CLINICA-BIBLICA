<?php
require_once __DIR__ . '/../MODEL/Expediente.php';

// Controlador para gestión de expedientes
class ExpedienteController
{
    private $model;

    public function __construct()
    {
        $this->model = new Expediente();
    }
    //  Obtener todos los expedientes
    public function obtenerTodos(): array
    {
        return $this->model->obtenerTodos();
    }
    //  Obtener expediente por ID
    public function obtenerPorId(int $idExpediente): ?array
    {
        return $this->model->obtenerPorId($idExpediente);
    }

    // Obtener citas del expediente para vista de detalle
    public function obtenerCitasPorExpediente(int $idExpediente): array
    {
        return $this->model->obtenerCitasPorExpediente($idExpediente);
    }

    //  Crear expediente desde formulario
    public function crear(): void
    {
        $idPaciente = (int) ($_POST['id_paciente'] ?? 0);
        $notas = trim($_POST['notas'] ?? '');
        
        // Validar que las notas no estén vacías
        if (empty($notas)) {
            $notas = 'Expediente creado';
        }

        $resultado = $this->model->crearExpediente($idPaciente, $notas);

        if ($resultado['resultado'] == 1) {
            header('Location: expediente.php?msg=' . urlencode($resultado['mensaje']) . '&ok=1');
        } else {
            header('Location: expediente.php?msg=' . urlencode($resultado['mensaje']) . '&ok=0');
        }
        exit;
    }

    // Actualizar solo las NOTAS del expediente
    public function actualizar(): void
    {
        $idExpediente = (int) ($_POST['id_expediente'] ?? 0);
        $notas = $_POST['notas'] ?? null;

        $resultado = $this->model->actualizarExpediente($idExpediente, $notas);

        if ($resultado['resultado'] == 1) {
            header('Location: expediente.php?msg=' . urlencode($resultado['mensaje']) . '&ok=1');
        } else {
            header('Location: expediente.php?msg=' . urlencode($resultado['mensaje']) . '&ok=0');
        }
        exit;
    }
    //  Metodo para obtener pacientes sin expediente
    public function obtenerPacientesSinExpediente(): array
    {
        return $this->model->obtenerPacientesSinExpediente();
    }

    //  Metodo para obtener detalle en JSON
    public function obtenerDetalleJSON(int $idExpediente): void
    {
        header('Content-Type: application/json');
        if ($idExpediente <= 0) {
            echo json_encode(['error' => 'ID de expediente inválido']);
            exit;
        }
        $expediente = $this->obtenerPorId($idExpediente);

        if (!$expediente) {
            echo json_encode(['error' => 'Expediente no encontrado']);
            exit;
        }

        $citas = $this->obtenerCitasPorExpediente($idExpediente);

        echo json_encode([
            'expediente' => $expediente,
            'citas' => $citas
        ]);
        exit;
    }
}

// Manejo de peticiones GET (para AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
    $controller = new ExpedienteController();
    $accion = $_GET['ajax'] ?? '';

    if ($accion === 'detalle') {
        $idExpediente = (int) ($_GET['id'] ?? 0);
        $controller->obtenerDetalleJSON($idExpediente);
    }
}

// Manejo de peticiones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ExpedienteController();
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'crear':
            $controller->crear();
            break;
        case 'actualizar':
            $controller->actualizar();
            break;
        default:
            header('Location: expediente.php?msg=Acción no válida&ok=0');
            exit;
    }
}
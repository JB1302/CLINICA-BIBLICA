<?php
ob_start();
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../config/database.php';
    
    $id_medico = isset($_GET['id_medico']) ? (int)$_GET['id_medico'] : 1;
    
    $conn = Database::get();
    
    $sql = "SELECT ah.horario, ah.dia_semana, ah.turno, ah.hora_inicio, ah.hora_fin
            FROM medico m
            JOIN agenda_horario ah ON ah.id_horario = m.id_horario
            WHERE m.id_medico = :id_medico";
    
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id_medico', $id_medico);
    oci_execute($stmt);
    
    $horarios = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $horarios[] = $row;
    }
    
    oci_free_statement($stmt);
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'horarios' => $horarios
    ]);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
exit;

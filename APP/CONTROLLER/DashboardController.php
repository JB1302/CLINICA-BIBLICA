<?php
require_once __DIR__ . '/../MODEL/Dashboard.php';

class DashboardController {
  public function getTablas(): array {
    try {
      $m = new Dashboard();
      return [
        'citas_por_estado'      => $m->citasPorEstado(),
        'atenciones_por_medico' => $m->atencionesPorMedico(),
        'pacientes_nuevos_mes'  => $m->pacientesNuevosPorMes(),
      ];
    } catch (Throwable $e) {
      error_log('DashboardController getTablas: '.$e->getMessage());
      return [
        'citas_por_estado' => [],
        'atenciones_por_medico' => [],
        'pacientes_nuevos_mes' => [],
      ];
    }
  }

  public function getKpis(): array {
    try {
      return (new Dashboard())->getkpis();
    } catch (Throwable $e) {
      error_log('DashboardController getKpis: '.$e->getMessage());
      return ['pacientes'=>0,'citas'=>0,'atenciones'=>0];
    }
  }
}
<?php require 'db.php';

$tablas = [
  'PACIENTE','PERSONAL','MEDICO','CLINICA',
  'CONSULTORIO','ESPECIALIDAD','ESTADO_CITA','CITA'
];

echo "<h2>Smoke test (conteos)</h2><ul>";
foreach ($tablas as $t) {
  $s = oci_parse($conn, "SELECT COUNT(*) C FROM $t");
  oci_execute($s);
  $r = oci_fetch_assoc($s);
  echo "<li>$t: <b>{$r['C']}</b></li>";
  oci_free_statement($s);
}
echo "</ul>";
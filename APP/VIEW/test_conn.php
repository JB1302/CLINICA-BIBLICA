<?php
// Importa la conexión
require 'db.php';

// Si la conexión está activa, muestra mensaje
if ($conn) {
  echo "<h2 style='color:green;'>✅ Conexión OK a Oracle</h2>";
} else {
  echo "<h2 style='color:red;'>❌ Error de conexión</h2>";
}
?>

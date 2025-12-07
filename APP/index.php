<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Clínica Bíblica</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #2c3e50; }
        .info { background: #ecf0f1; padding: 20px; border-radius: 5px; margin: 20px 0; }
        pre { background: #34495e; color: #ecf0f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>✅ ¡Servidor Apache funcionando!</h1>
    
    <div class="info">
        <h2>Información del Sistema:</h2>
        <p><strong>Versión de PHP:</strong> <?php echo phpversion(); ?></p>
        <p><strong>Servidor:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
        <p><strong>Fecha/Hora:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    </div>

    <div class="info">
        <h2>Extensiones PHP Cargadas:</h2>
        <pre><?php 
            $extensions = get_loaded_extensions();
            sort($extensions);
            foreach($extensions as $ext) {
                echo $ext . "\n";
            }
        ?></pre>
    </div>

    <div class="info">
        <h2>¿Tiene OCI8 instalado?</h2>
        <p><?php 
            if (extension_loaded('oci8')) {
                echo '✅ <strong style="color: green;">SÍ - OCI8 está instalado</strong>';
            } else {
                echo '❌ <strong style="color: red;">NO - OCI8 NO está instalado</strong>';
                echo '<br><small>Necesitarás instalar la extensión OCI8 para conectarte a Oracle</small>';
            }
        ?></p>
    </div>

    <div class="info">
        <h2>Archivos en el proyecto:</h2>
        <pre><?php
            $dir = __DIR__;
            echo "Contenido de: $dir\n\n";
            $files = scandir($dir);
            foreach($files as $file) {
                if ($file != '.' && $file != '..') {
                    $path = $dir . '/' . $file;
                    $type = is_dir($path) ? '[DIR]' : '[FILE]';
                    echo "$type $file\n";
                }
            }
        ?></pre>
    </div>

    <hr>
    <p><a href="VIEW/index.php">→ Ir a la aplicación (VIEW/index.php)</a></p>
</body>
</html>

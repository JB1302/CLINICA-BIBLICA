<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - Clínica Bíblica</title>
</head>
<body>
    <h1>¡Servidor Apache funcionando!</h1>
    <p>Si ves este mensaje, el servidor está corriendo correctamente.</p>
    
    <h2>Información de PHP:</h2>
    <p><strong>Versión de PHP:</strong> <?php echo phpversion(); ?></p>
    <p><strong>Fecha/Hora:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    
    <h2>Extensiones PHP cargadas:</h2>
    <pre><?php print_r(get_loaded_extensions()); ?></pre>
</body>
</html>

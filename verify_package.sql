-- Cambio de Adry: Script para verificar si el package PKG_CITA está actualizado con los nuevos parámetros

-- Ver la firma del procedimiento editar_cita
SELECT 
    object_name,
    procedure_name,
    argument_name,
    position,
    data_type,
    in_out
FROM 
    all_arguments
WHERE 
    owner = 'CLINICA'
    AND package_name = 'PKG_CITA'
    AND object_name = 'EDITAR_CITA'
ORDER BY 
    position;

-- Verificar que existe el parámetro PIN_ID_CONSULTORIO en la posición 8
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN 'El package esta actualizado con PIN_ID_CONSULTORIO'
        ELSE 'FALTA ejecutar create_packages.sql - PIN_ID_CONSULTORIO no existe'
    END AS resultado
FROM 
    all_arguments
WHERE 
    owner = 'CLINICA'
    AND package_name = 'PKG_CITA'
    AND object_name = 'EDITAR_CITA'
    AND argument_name = 'PIN_ID_CONSULTORIO'
    AND position = 8;

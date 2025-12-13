-- Vista de pacientes donde el correo termina en @gmail.com
CREATE OR REPLACE VIEW v_pacientes_correo_gmail AS
    SELECT
        id_paciente,
        cedula,
        primer_nombre,
        segundo_nombre,
        primer_apellido,
        segundo_apellido,
        correo_electronico
    FROM
        paciente
    WHERE
        REGEXP_LIKE ( correo_electronico,
                      '^[a-zA-Z0-9._%+-]+@gmail\.com$' );

-- Vista de pacientes donde el primer nombre termina en ía--
CREATE OR REPLACE VIEW v_pacientes_nombre_termina_ia AS
    SELECT
        id_paciente,
        cedula,
        primer_nombre,
        segundo_nombre,
        primer_apellido,
        segundo_apellido,
        correo_electronico
    FROM
        paciente
    WHERE
        REGEXP_LIKE ( primer_nombre,
                      '^[^ ]*ía$' );

-- Vista de pacientes con formato (+506)
CREATE OR REPLACE VIEW v_pacientes_telefono_formato_506 AS
    SELECT
        id_paciente,
        cedula,
        primer_nombre,
        segundo_nombre,
        primer_apellido,
        segundo_apellido,
        telefono,
        regexp_replace(telefono, '^([0-9]{4})-([0-9]{4})$', '(+506) (\1-\2)') AS telefono_formato_506
    FROM
        paciente
    WHERE
        REGEXP_LIKE ( telefono,
                      '^[0-9]{4}-[0-9]{4}$' );

-- Vista de pacientes donde la dirección sea igual a Alajuela o Heredia
CREATE OR REPLACE VIEW v_pacientes_direccion_alajuela_heredia AS
    SELECT
        id_paciente,
        cedula,
        primer_nombre,
        segundo_nombre,
        primer_apellido,
        segundo_apellido,
        direccion
    FROM
        paciente
    WHERE
        REGEXP_LIKE ( direccion,
                      '(Alajuela|Heredia)' );
--------------------------
-- TRIGGERS
--------------------------

--Validar el rango de la cita
CREATE OR REPLACE TRIGGER validar_rango_cita
    BEFORE INSERT OR UPDATE OF fecha, hora_agendada_inicio, hora_agendada_fin
    ON cita
    FOR EACH ROW
DECLARE
    var_citas NUMBER;
BEGIN
-- Validar que la cita no sea en el pasado
    IF :new.hora_agendada_inicio < systimestamp THEN
        raise_application_error(-20010, 'La cita no puede ser en una fecha/hora anterior a la actual');
    END IF;

-- validar que inicio < fin
    IF :new.hora_agendada_inicio >= :new.hora_agendada_fin THEN
        raise_application_error(-20011, 'La hora de inicio debe ser menor que la hora de fin');
    END IF;

END;
/
----1# TRIGGER DE PACIENTES
CREATE OR REPLACE TRIGGER trg_paciente_validacion BEFORE
    INSERT OR UPDATE ON paciente
    FOR EACH ROW
BEGIN
    IF :new.cedula IS NULL THEN
        raise_application_error(-20010, 'La cedula es obligatoria');
    END IF;

    IF NOT regexp_like(:new.cedula,
                       '^[0-9]{9}$') THEN
        raise_application_error(-20011, 'La cedula debe contener exactamente 9 dígitos numéricos.');
    END IF;

    IF :new.fecha_nacimiento > sysdate THEN
        raise_application_error(-20012, 'La fecha de nacimiento no puede ser futura');
    END IF;

    IF
        :new.telefono IS NOT NULL
        AND NOT regexp_like(:new.telefono,
                            '^[0-9]{4}-[0-9]{4}$|^[0-9]{8}$')
    THEN
        raise_application_error(-20013, 'El teléfono debe estar en formato ####-#### o ######## (solo números y el guión opcional).')
        ;
    END IF;

    IF
        :new.correo_electronico IS NOT NULL
        AND NOT regexp_like(:new.correo_electronico,
                            '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$')
    THEN
        raise_application_error(-20014, 'El correo electrónico debe tener un formato válido (ej. nombre@dominio.com).');
    END IF;

END;
/
--- 2# TRIGGER PARA CITA
CREATE OR REPLACE TRIGGER trg_cita_validacion BEFORE
    INSERT OR UPDATE ON cita
    FOR EACH ROW
DECLARE
    var_id_cancelada estado_cita.id_estado%TYPE;
BEGIN
    IF :new.fecha < trunc(sysdate) THEN
        raise_application_error(-20020, 'La fecha de la cita no puede estar en el pasado');
    END IF;

    IF :new.observaciones IS NULL THEN
        raise_application_error(-20022, 'Coloque una observacion');
    END IF;

END;

/
---3# TRIGGER DE PERSONAL
CREATE OR REPLACE TRIGGER trg_personal_validaciones BEFORE
    INSERT OR UPDATE ON personal
    FOR EACH ROW
BEGIN
    IF :new.telefono IS NULL THEN
        raise_application_error(-20030, 'El telefono es obligatorio o poner donde contactarlo');
    END IF;

    IF NOT regexp_like(:new.telefono,
                       '^[0-9]{4}-[0-9]{4}$|^[0-9]{8}$') THEN
        raise_application_error(-20031, 'El teléfono debe estar en formato ####-#### o ######## (solo números y el guión opcional).')
        ;
    END IF;

    IF :new.puesto IS NULL THEN
        raise_application_error(-20032, 'Debe colocar su puesto');
    END IF;

    IF
        :new.correo_electronico IS NOT NULL
        AND NOT regexp_like(:new.correo_electronico,
                            '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$')
    THEN
        raise_application_error(-20033, 'El correo electrónico debe tener un formato válido (ej. nombre@dominio.com).');
    END IF;

END;
/

---4# TRIGGER DE CONSULTORIO
CREATE OR REPLACE TRIGGER trg_consultorio_validacion BEFORE
    INSERT OR UPDATE ON consultorio
    FOR EACH ROW
BEGIN
    IF :new.nombre IS NULL THEN
        raise_application_error(-20050, 'El nombre del consultorio es obligatorio');
    END IF;

    IF :new.tipo IS NULL THEN
        raise_application_error(-20052, 'Debe especificar el tipo de consultorio');
    END IF;

END;
/

---5# TRIGGER DE EXPEDIENTE
CREATE OR REPLACE TRIGGER trg_expediente_validacion BEFORE
    INSERT OR UPDATE ON expediente
    FOR EACH ROW
BEGIN
    IF :new.creado_en IS NULL THEN
        raise_application_error(-20060, 'Debe poner la fecha de creación');
    END IF;

    IF :new.notas IS NULL THEN
        raise_application_error(-20062, 'Debe poner notas');
    END IF;

END;
/

----6# TRIGGER DE CLINICA
CREATE OR REPLACE TRIGGER trg_clinica_validacion BEFORE
    INSERT OR UPDATE ON clinica
    FOR EACH ROW
BEGIN
    IF :new.nombre IS NULL THEN
        raise_application_error(-20070, 'Debe poner su nombre');
    END IF;

    IF :new.codigo_clinica IS NULL THEN
        raise_application_error(-20072, 'Debe colocar el codigo de la clinica');
    END IF;

END;
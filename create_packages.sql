-- PAQUETES 

-- PAQUETES INCLUIDOS:
-- 1. PKG_PERSONAL 
-- 2. PKG_MEDICO
-- 3. PKG_PACIENTE
-- 4. PKG_CITAs
-- 5. PKG_EXPEDIENTE
--
-- ============================================================================
-- CAMBIOS  DE ADRY:
-- ============================================================================
--
-- PKG_PERSONAL:
--   - Agregados campos: PRIMER_APELLIDO, SEGUNDO_APELLIDO
--   - Agregado campo: HORARIO_TRABAJO (FK a AGENDA_HORARIO)
--   - Validación de correo electrónico único
--   - Validación de citas asociadas antes de eliminar
--
-- PKG_MEDICO:
--   - Sin campo FECHA_CONTRATACION (no existe en tabla MEDICO)
--   - Campo ID_HORARIO se copia automáticamente desde PERSONAL.HORARIO_TRABAJO
--   - Validación de especialidad y personal existente
--   - Validación de citas asociadas antes de eliminar
--
-- PKG_PACIENTE:
--   - Agregados campos: PRIMER_APELLIDO, SEGUNDO_APELLIDO
--   - Sin campos PROVINCIA, CANTON, DISTRITO (no existen en tabla PACIENTE)
--   - Validación de cédula única
--   - Validación de citas asociadas antes de eliminar
--
-- PKG_CITA:
--   - Usar columnas: ID_ESTADO, FECHA, HORA_AGENDADA_INICIO, HORA_AGENDADA_FIN
--   - Agregado parámetro: PIN_ID_ESTADO (obligatorio)
--   - Agregado parámetro: PIN_ID_CONSULTORIO (obligatorio)
--   - Validación de disponibilidad del médico según AGENDA_HORARIO
--   - Validación de día de semana (solo Lunes-Viernes)
--   - Validación de turno (Mañana 07:00-13:00, Tarde 13:00-19:00)
--   - Función privada: CONVERTIR_DIA_SEMANA (Oracle format → AGENDA_HORARIO format)
--   - Procedimiento privado: VALIDAR_DISPONIBILIDAD_MEDICO
--   - Procedimiento público: CANCELAR_CITA (cambiar estado y motivo)
--
-- PKG_EXPEDIENTE:
--   - Operaciones: CREAR y ACTUALIZAR (sin borrado)
--   - Campo CREADO_EN se establece automáticamente con SYSDATE
--   - Validación de paciente único (un expediente por paciente)
--   - Solo se pueden actualizar las NOTAS del expediente
--
-- ============================================================================

--------------------------
-- PERSONAL
--------------------------
-----------
-- CABECERA
-----------
CREATE OR REPLACE PACKAGE pkg_personal AS

-- INSERTAR PERSONAL
-- Cambio de Adry: Agregados primer_apellido, segundo_apellido, horario_trabajo
    PROCEDURE agregar_personal (
        pin_primer_nombre      IN VARCHAR2,
        pin_segundo_nombre     IN VARCHAR2,
        pin_primer_apellido    IN VARCHAR2,
        pin_segundo_apellido   IN VARCHAR2,
        pin_puesto             IN VARCHAR2,
        pin_activo             IN VARCHAR2,
        pin_correo_electronico IN VARCHAR2,
        pin_telefono           IN VARCHAR2,
        pin_direccion          IN VARCHAR2,
        pin_provincia          IN VARCHAR2,
        pin_canton             IN VARCHAR2,
        pin_distrito           IN VARCHAR2,
        pin_horario_trabajo    IN VARCHAR2,
        pout_resultado         OUT NUMBER,
        pout_mensaje           OUT VARCHAR2
    );

-- EDITAR PERSONAL
-- Cambio de Adry: Agregados primer_apellido, segundo_apellido, horario_trabajo
    PROCEDURE editar_personal (
        pin_id_personal        IN NUMBER,
        pin_primer_nombre      IN VARCHAR2,
        pin_segundo_nombre     IN VARCHAR2,
        pin_primer_apellido    IN VARCHAR2,
        pin_segundo_apellido   IN VARCHAR2,
        pin_puesto             IN VARCHAR2,
        pin_activo             IN VARCHAR2,
        pin_correo_electronico IN VARCHAR2,
        pin_telefono           IN VARCHAR2,
        pin_direccion          IN VARCHAR2,
        pin_provincia          IN VARCHAR2,
        pin_canton             IN VARCHAR2,
        pin_distrito           IN VARCHAR2,
        pin_horario_trabajo    IN VARCHAR2,
        pout_resultado         OUT NUMBER,
        pout_mensaje           OUT VARCHAR2
    );

-- ELIMINAR PERSONAL
    PROCEDURE eliminar_personal (
        pin_id_personal IN NUMBER,
        pout_resultado  OUT NUMBER,
        pout_mensaje    OUT VARCHAR2
    );

END pkg_personal;
/

------
--BODY
------
CREATE OR REPLACE PACKAGE BODY pkg_personal AS
--INSERT PERSONAL
-- Cambio de Adry: Agregados primer_apellido, segundo_apellido, horario_trabajo
    PROCEDURE agregar_personal (
        pin_primer_nombre      IN VARCHAR2,
        pin_segundo_nombre     IN VARCHAR2,
        pin_primer_apellido    IN VARCHAR2,
        pin_segundo_apellido   IN VARCHAR2,
        pin_puesto             IN VARCHAR2,
        pin_activo             IN VARCHAR2,
        pin_correo_electronico IN VARCHAR2,
        pin_telefono           IN VARCHAR2,
        pin_direccion          IN VARCHAR2,
        pin_provincia          IN VARCHAR2,
        pin_canton             IN VARCHAR2,
        pin_distrito           IN VARCHAR2,
        pin_horario_trabajo    IN VARCHAR2,
        pout_resultado         OUT NUMBER,
        pout_mensaje           OUT VARCHAR2
    ) AS

        existe               NUMBER;
        var_campos_faltantes VARCHAR2(4000) := '';
        con_salto            CONSTANT VARCHAR2(2) := chr(10);

-- Cursor para validar correo existente
        CURSOR c_personal_correo (
            p_correo VARCHAR2
        ) IS
        SELECT
            1
        FROM
            personal
        WHERE
            correo_electronico = p_correo;

        v_dummy              NUMBER;
    BEGIN
--Validaciones
--NOMBRE
        IF TRIM(pin_primer_nombre) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Primer nombre'
                                    || con_salto;
        END IF;
-- Cambio de Adry: Validación de primer apellido
        IF TRIM(pin_primer_apellido) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Primer apellido'
                                    || con_salto;
        END IF;
--PUESTO
        IF TRIM(pin_puesto) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Puesto'
                                    || con_salto;
        END IF;
--CORREO
        IF TRIM(pin_correo_electronico) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Correo electrónico'
                                    || con_salto;
        END IF;
--TELEFONO
        IF TRIM(pin_telefono) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Teléfono'
                                    || con_salto;
        END IF;
--DIRECCION
        IF TRIM(pin_direccion) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Dirección'
                                    || con_salto;
        END IF;
--PROVINCIA
        IF TRIM(pin_provincia) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Provincia'
                                    || con_salto;
        END IF;
--CANTON
        IF TRIM(pin_canton) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Cantón'
                                    || con_salto;
        END IF;
--DISTRITO
        IF TRIM(pin_distrito) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Distrito'
                                    || con_salto;
        END IF;	
--HORARIO_TRABAJO
        IF TRIM(pin_horario_trabajo) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Horario de trabajo'
                                    || con_salto;
        END IF;	
--VALIDAR SI HAY ERRORES
        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;

-- CORREO YA EXISTENTE
        existe := 0;
        OPEN c_personal_correo(pin_correo_electronico);
        FETCH c_personal_correo INTO v_dummy;
        IF c_personal_correo%found THEN
            existe := 1;
        END IF;
        CLOSE c_personal_correo;
        IF existe > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'Ya existe un personal con el correo ingresado';
            RETURN;
        END IF;
--INSERT
-- Cambio de Adry: Agregados primer_apellido, segundo_apellido, horario_trabajo
        INSERT INTO personal (
            primer_nombre,
            segundo_nombre,
            primer_apellido,
            segundo_apellido,
            puesto,
            activo,
            correo_electronico,
            telefono,
            direccion,
            provincia,
            canton,
            distrito,
            horario_trabajo
        ) VALUES ( pin_primer_nombre,
                   pin_segundo_nombre,
                   pin_primer_apellido,
                   pin_segundo_apellido,
                   pin_puesto,
                   pin_activo,
                   pin_correo_electronico,
                   pin_telefono,
                   pin_direccion,
                   pin_provincia,
                   pin_canton,
                   pin_distrito,
                   pin_horario_trabajo );

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo crear el personal';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'Personal ha sido creado';
        RETURN;
    END;

--EDITAR
-- Cambio de Adry: Agregados primer_apellido, segundo_apellido, horario_trabajo
    PROCEDURE editar_personal (
        pin_id_personal        IN NUMBER,
        pin_primer_nombre      IN VARCHAR2,
        pin_segundo_nombre     IN VARCHAR2,
        pin_primer_apellido    IN VARCHAR2,
        pin_segundo_apellido   IN VARCHAR2,
        pin_puesto             IN VARCHAR2,
        pin_activo             IN VARCHAR2,
        pin_correo_electronico IN VARCHAR2,
        pin_telefono           IN VARCHAR2,
        pin_direccion          IN VARCHAR2,
        pin_provincia          IN VARCHAR2,
        pin_canton             IN VARCHAR2,
        pin_distrito           IN VARCHAR2,
        pin_horario_trabajo    IN VARCHAR2,
        pout_resultado         OUT NUMBER,
        pout_mensaje           OUT VARCHAR2
    ) AS

        existe               NUMBER;
        var_campos_faltantes VARCHAR2(4000) := '';
        con_salto            CONSTANT VARCHAR2(2) := chr(10);
    BEGIN
-- VALIDAR ID
        IF pin_id_personal IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Debe seleccionar un usuario de personal';
            RETURN;
        END IF;
--

-- VALIDACIONES
        IF TRIM(pin_primer_nombre) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Primer nombre'
                                    || con_salto;
        END IF;

-- Cambio de Adry: Validación de primer apellido
        IF TRIM(pin_primer_apellido) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Primer apellido'
                                    || con_salto;
        END IF;

        IF TRIM(pin_puesto) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Puesto'
                                    || con_salto;
        END IF;

        IF TRIM(pin_correo_electronico) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Correo electrónico'
                                    || con_salto;
        END IF;

        IF TRIM(pin_telefono) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Teléfono'
                                    || con_salto;
        END IF;

        IF TRIM(pin_direccion) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Dirección'
                                    || con_salto;
        END IF;

        IF TRIM(pin_provincia) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Provincia'
                                    || con_salto;
        END IF;

        IF TRIM(pin_canton) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Cantón'
                                    || con_salto;
        END IF;

        IF TRIM(pin_distrito) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Distrito'
                                    || con_salto;
        END IF;

        IF TRIM(pin_horario_trabajo) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Horario de trabajo'
                                    || con_salto;
        END IF;
--VALIDAR SI HAY ERRORES
        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;

-- Validar que el ID exista
        SELECT
            COUNT(*)
        INTO existe
        FROM
            personal
        WHERE
            id_personal = pin_id_personal;

        IF existe = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe personal con el ID ingresado';
            RETURN;
        END IF;

-- Validar correo duplicado (excluyendo el mismo registro)
        SELECT
            COUNT(*)
        INTO existe
        FROM
            personal
        WHERE
                correo_electronico = pin_correo_electronico
            AND id_personal <> pin_id_personal;

        IF existe > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'Ya existe un personal con el correo ingresado';
            RETURN;
        END IF;

-- UPDATE
-- Cambio de Adry: Agregados primer_apellido, segundo_apellido, horario_trabajo
        UPDATE personal
        SET
            primer_nombre = pin_primer_nombre,
            segundo_nombre = pin_segundo_nombre,
            primer_apellido = pin_primer_apellido,
            segundo_apellido = pin_segundo_apellido,
            puesto = pin_puesto,
            activo = pin_activo,
            correo_electronico = pin_correo_electronico,
            telefono = pin_telefono,
            direccion = pin_direccion,
            provincia = pin_provincia,
            canton = pin_canton,
            distrito = pin_distrito,
            horario_trabajo = pin_horario_trabajo
        WHERE
            id_personal = pin_id_personal;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo actualizar el personal seleccionado';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'El personal ha sido actualizado';
        RETURN;
    END;

--ELIMINAR
    PROCEDURE eliminar_personal (
        pin_id_personal IN NUMBER,
        pout_resultado  OUT NUMBER,
        pout_mensaje    OUT VARCHAR2
    ) AS
        existe       NUMBER;
        tiene_citas  NUMBER;
        tiene_agenda NUMBER;
    BEGIN
-- Validar ID
        IF pin_id_personal IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'El ID del personal es obligatorio';
            RETURN;
        END IF;

-- Validar que el ID exista
        SELECT
            COUNT(*)
        INTO existe
        FROM
            personal
        WHERE
            id_personal = pin_id_personal;

        IF existe = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe personal con el ID ingresado';
            RETURN;
        END IF;

        SELECT
            COUNT(*)
        INTO tiene_citas
        FROM
            cita c
        WHERE
            c.id_medico IN (
                SELECT
                    m.id_medico
                FROM
                    medico m
                WHERE
                    m.id_personal = pin_id_personal
            );

        IF tiene_citas > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se puede eliminar el personal porque tiene citas asociadas.';
            RETURN;
        END IF;

        SELECT
            COUNT(*)
        INTO tiene_agenda
        FROM
            cita c
        WHERE
            c.id_agenda IN (
                SELECT
                    a.id_agenda
                FROM
                    agenda_medica a
                WHERE
                    a.id_medico IN (
                        SELECT
                            m.id_medico
                        FROM
                            medico m
                        WHERE
                            m.id_personal = pin_id_personal
                    )
            );

        IF tiene_agenda > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se puede eliminar el personal porque esta registrado como Doctor.';
            RETURN;
        END IF;

-- DELETE
--Borrrar agenda_medica
        DELETE FROM agenda_medica
        WHERE
            id_medico IN (
                SELECT
                    id_medico
                FROM
                    medico
                WHERE
                    id_personal = pin_id_personal
            );
--BORRAR RELACIONES
        DELETE FROM medico_especialidad
        WHERE
            id_medico IN (
                SELECT
                    id_medico
                FROM
                    medico
                WHERE
                    id_personal = pin_id_personal
            );

        DELETE FROM medico
        WHERE
            id_personal = pin_id_personal;

        DELETE FROM contrato
        WHERE
            id_personal = pin_id_personal;
--BORRAR PERSONAL
        DELETE FROM personal
        WHERE
            id_personal = pin_id_personal;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo eliminar el personal';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'El personal ha sido eliminado';
        RETURN;
    END;

END;
/

--------------------------
-- MEDICO
--------------------------
-----------
-- CABECERA
-----------
CREATE OR REPLACE PACKAGE pkg_medico AS

-- AGREGAR MÉDICO
    PROCEDURE agregar_medico (
        pin_id_personal     IN NUMBER,
        pin_id_especialidad IN NUMBER,
        pout_resultado      OUT NUMBER,
        pout_mensaje        OUT VARCHAR2
    );

-- EDITAR MÉDICO
    PROCEDURE editar_medico (
        pin_id_medico       IN NUMBER,
        pin_nombre          IN VARCHAR2,
        pin_id_especialidad IN NUMBER,
        pout_resultado      OUT NUMBER,
        pout_mensaje        OUT VARCHAR2
    );

-- ELIMINAR MÉDICO
    PROCEDURE eliminar_medico (
        pin_id_medico  IN NUMBER,
        pout_resultado OUT NUMBER,
        pout_mensaje   OUT VARCHAR2
    );

END pkg_medico;
/

------
--BODY
------
CREATE OR REPLACE PACKAGE BODY pkg_medico AS

-- AGREGAR MÉDICO
    PROCEDURE agregar_medico (
        pin_id_personal     IN NUMBER,
        pin_id_especialidad IN NUMBER,
        pout_resultado      OUT NUMBER,
        pout_mensaje        OUT VARCHAR2
    ) AS

        var_existe_personal     NUMBER;
        var_existe_medico       NUMBER;
        var_existe_especialidad NUMBER;
        var_id_medico           medico.id_medico%TYPE;
        var_campos_faltantes    VARCHAR2(4000) := '';
        con_salto               CONSTANT VARCHAR2(2) := chr(10);

-- Cursor para validar que el personal exista
        CURSOR c_personal (
            p_id_personal NUMBER
        ) IS
        SELECT
            id_personal
        FROM
            personal
        WHERE
            id_personal = p_id_personal;

        v_id_personal           personal.id_personal%TYPE;
    BEGIN
-- Validaciones de obligatorios
        IF pin_id_personal IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Personal'
                                    || con_salto;
        END IF;

        IF pin_id_especialidad IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Especialidad'
                                    || con_salto;
        END IF;

--VALIDAR SI HAY ERRORES
        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;

-- Validar que el personal exista
        var_existe_personal := 0;
        OPEN c_personal(pin_id_personal);
        FETCH c_personal INTO v_id_personal;
        IF c_personal%found THEN
            var_existe_personal := 1;
        END IF;
        CLOSE c_personal;
        IF var_existe_personal = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe personal con el ID indicado';
            RETURN;
        END IF;

-- Validar que el personal no sea ya médico
        SELECT
            COUNT(*)
        INTO var_existe_medico
        FROM
            medico
        WHERE
            id_personal = pin_id_personal;

        IF var_existe_medico > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'El personal seleccionado ya está registrado como médico';
            RETURN;
        END IF;

-- Validar que la especialidad exista
        SELECT
            COUNT(*)
        INTO var_existe_especialidad
        FROM
            especialidad
        WHERE
            id_especialidad = pin_id_especialidad;

        IF var_existe_especialidad = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe la especialidad seleccionada';
            RETURN;
        END IF;

-- INSERT en MEDICO
-- Cambio de Adry: Sin FECHA_CONTRATACION (no existe en tabla MEDICO)
-- Cambio de Adry: Copiar HORARIO_TRABAJO del PERSONAL a MEDICO.ID_HORARIO automáticamente
        DECLARE
            var_horario_personal NUMBER;
        BEGIN
-- Obtener el HORARIO_TRABAJO del personal
            BEGIN
                SELECT
                    horario_trabajo
                INTO var_horario_personal
                FROM
                    personal
                WHERE
                    id_personal = pin_id_personal;

            EXCEPTION
                WHEN no_data_found THEN
                    var_horario_personal := NULL;
                WHEN OTHERS THEN
                    var_horario_personal := NULL;
            END;

-- Insertar médico con el horario del personal (puede ser NULL)
            INSERT INTO medico (
                id_personal,
                id_horario
            ) VALUES ( pin_id_personal,
                       var_horario_personal ) RETURNING id_medico INTO var_id_medico;

        END;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo crear el médico';
            ROLLBACK;
            RETURN;
        END IF;

-- INSERT en MEDICO_ESPECIALIDAD
        INSERT INTO medico_especialidad (
            id_medico,
            id_especialidad,
            desde,
            hasta
        ) VALUES ( var_id_medico,
                   pin_id_especialidad,
                   sysdate,
                   NULL );

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo asociar la especialidad al médico';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'Médico ha sido creado correctamente';
        RETURN;
    END agregar_medico;

-- EDITAR MÉDICO
    PROCEDURE editar_medico (
        pin_id_medico       IN NUMBER,
        pin_nombre          IN VARCHAR2,
        pin_id_especialidad IN NUMBER,
        pout_resultado      OUT NUMBER,
        pout_mensaje        OUT VARCHAR2
    ) AS

        var_id_personal         personal.id_personal%TYPE;
        var_existe_medico       NUMBER;
        var_existe_especialidad NUMBER;
        var_campos_faltantes    VARCHAR2(4000) := '';
        con_salto               CONSTANT VARCHAR2(2) := chr(10);
        var_primer_nombre       VARCHAR2(100);
        var_segundo_nombre      VARCHAR2(200);
        var_pos                 NUMBER;
    BEGIN
-- Validar ID médico
        IF pin_id_medico IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Debe seleccionar un médico';
            RETURN;
        END IF;
--Validar nombre
        var_pos := instr(
            trim(pin_nombre),
            ' '
        );
        IF var_pos = 0 THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Nombre y Apellido'
                                    || con_salto;
        ELSE
            var_primer_nombre := substr(
                trim(pin_nombre),
                1,
                var_pos - 1
            );
            var_segundo_nombre := substr(
                trim(pin_nombre),
                var_pos + 1
            );
        END IF;

-- Validaciones de campos obligatorios

        IF pin_id_especialidad IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Especialidad'
                                    || con_salto;
        END IF;

--VALIDAR SI HAY ERRORES
        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;

-- Validar que el médico exista y obtener ID_PERSONAL
        SELECT
            COUNT(*)
        INTO var_existe_medico
        FROM
            medico
        WHERE
            id_medico = pin_id_medico;

        IF var_existe_medico = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe médico con el ID ingresado';
            RETURN;
        END IF;

        SELECT
            id_personal
        INTO var_id_personal
        FROM
            medico
        WHERE
            id_medico = pin_id_medico;

-- Validar que la especialidad exista
        SELECT
            COUNT(*)
        INTO var_existe_especialidad
        FROM
            especialidad
        WHERE
            id_especialidad = pin_id_especialidad;

        IF var_existe_especialidad = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe la especialidad seleccionada';
            RETURN;
        END IF;

-- Actualizar datos del PERSONAL	
        UPDATE personal
        SET
            primer_nombre = var_primer_nombre,
            segundo_nombre = var_segundo_nombre
        WHERE
            id_personal = var_id_personal;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo actualizar los datos del personal';
            ROLLBACK;
            RETURN;
        END IF;

-- Actualizar especialidad del médico
-- Cambio de Adry: Actualizar usando la fecha DESDE, no FECHA_CONTRATACION
        UPDATE medico_especialidad
        SET
            id_especialidad = pin_id_especialidad
        WHERE
            id_medico = pin_id_medico;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo actualizar la especialidad del médico';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'El médico ha sido actualizado';
        RETURN;
    END editar_medico;

-- ELIMINAR MÉDICO
    PROCEDURE eliminar_medico (
        pin_id_medico  IN NUMBER,
        pout_resultado OUT NUMBER,
        pout_mensaje   OUT VARCHAR2
    ) AS
        v_existe_medico NUMBER;
        tiene_citas     NUMBER;
    BEGIN
-- Validar ID
        IF pin_id_medico IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'El ID del médico es obligatorio';
            RETURN;
        END IF;

-- Validar que el médico exista
        SELECT
            COUNT(*)
        INTO v_existe_medico
        FROM
            medico
        WHERE
            id_medico = pin_id_medico;

        IF v_existe_medico = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe médico con el ID ingresado';
            RETURN;
        END IF;

        SELECT
            COUNT(*)
        INTO tiene_citas
        FROM
            cita
        WHERE
            id_medico = pin_id_medico;

        IF tiene_citas > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se puede eliminar el médico porque tiene citas asociadas.';
            RETURN;
        END IF;

-- Borrar atenciones de las citas de este médico
        DELETE FROM atencion at
        WHERE
            at.id_cita IN (
                SELECT
                    c.id_cita
                FROM
                    cita c
                WHERE
                    c.id_medico = pin_id_medico
                    OR c.id_agenda IN (
                        SELECT
                            a.id_agenda
                        FROM
                            agenda_medica a
                        WHERE
                            a.id_medico = pin_id_medico
                    )
            );

-- Borrar citas del médico o de su agenda
        DELETE FROM cita
        WHERE
            id_medico = pin_id_medico
            OR id_agenda IN (
                SELECT
                    a.id_agenda
                FROM
                    agenda_medica a
                WHERE
                    a.id_medico = pin_id_medico
            );

-- Borrar agenda médica
        DELETE FROM agenda_medica
        WHERE
            id_medico = pin_id_medico;

-- Borrar especialidades
        DELETE FROM medico_especialidad
        WHERE
            id_medico = pin_id_medico;

-- Borrar médico
        DELETE FROM medico
        WHERE
            id_medico = pin_id_medico;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo eliminar el médico';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'El médico ha sido eliminado correctamente';
        RETURN;
    END eliminar_medico;

END pkg_medico;
/

--------------------------
-- PACIENTE
--------------------------
------------
--CABECERA
------------
CREATE OR REPLACE PACKAGE pkg_paciente AS
    PROCEDURE agregar_paciente (
        pin_cedula             IN VARCHAR2,
        pin_primer_nombre      IN VARCHAR2,
        pin_segundo_nombre     IN VARCHAR2,
        pin_primer_apellido    IN VARCHAR2,
        pin_segundo_apellido   IN VARCHAR2,
        pin_fecha_nacimiento   IN VARCHAR2,
        pin_sexo               IN VARCHAR2,
        pin_observaciones      IN VARCHAR2,
        pin_telefono           IN VARCHAR2,
        pin_direccion          IN VARCHAR2,
        pin_correo_electronico IN VARCHAR2,
        pout_resultado         OUT NUMBER,
        pout_mensaje           OUT VARCHAR2
    );

    PROCEDURE editar_paciente (
        pin_id                 IN NUMBER,
        pin_cedula             IN VARCHAR2,
        pin_primer_nombre      IN VARCHAR2,
        pin_segundo_nombre     IN VARCHAR2,
        pin_primer_apellido    IN VARCHAR2,
        pin_segundo_apellido   IN VARCHAR2,
        pin_fecha_nacimiento   IN VARCHAR2,
        pin_sexo               IN VARCHAR2,
        pin_observaciones      IN VARCHAR2,
        pin_telefono           IN VARCHAR2,
        pin_direccion          IN VARCHAR2,
        pin_correo_electronico IN VARCHAR2,
        pout_resultado         OUT NUMBER,
        pout_mensaje           OUT VARCHAR2
    );

    PROCEDURE eliminar_paciente (
        pin_id         IN NUMBER,
        pout_resultado OUT NUMBER,
        pout_mensaje   OUT VARCHAR2
    );

END pkg_paciente;
/

------
--BODY
------
CREATE OR REPLACE PACKAGE BODY pkg_paciente AS
--INSERT PACIENTE
-- Cambio de Adry: Sin provincia, canton, distrito (no existen en tabla PACIENTE)
    PROCEDURE agregar_paciente (
        pin_cedula             IN VARCHAR2,
        pin_primer_nombre      IN VARCHAR2,
        pin_segundo_nombre     IN VARCHAR2,
        pin_primer_apellido    IN VARCHAR2,
        pin_segundo_apellido   IN VARCHAR2,
        pin_fecha_nacimiento   IN VARCHAR2,
        pin_sexo               IN VARCHAR2,
        pin_observaciones      IN VARCHAR2,
        pin_telefono           IN VARCHAR2,
        pin_direccion          IN VARCHAR2,
        pin_correo_electronico IN VARCHAR2,
        pout_resultado         OUT NUMBER,
        pout_mensaje           OUT VARCHAR2
    ) AS

        fecha                DATE;
        existe               NUMBER;
        var_campos_faltantes VARCHAR2(4000) := '';
        con_salto            CONSTANT VARCHAR2(2) := chr(10);

-- Cursor para validar cédula existente
        CURSOR c_paciente_cedula (
            p_cedula VARCHAR2
        ) IS
        SELECT
            1
        FROM
            paciente
        WHERE
            cedula = p_cedula;

        v_dummy              NUMBER;
    BEGIN
--Validaciones
--CEDULA
        IF TRIM(pin_cedula) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Cédula'
                                    || con_salto;
        END IF;
--NOMBRE
        IF TRIM(pin_primer_nombre) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Primer nombre'
                                    || con_salto;
        END IF;
--APELLIDO
        IF TRIM(pin_primer_apellido) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Primer apellido'
                                    || con_salto;
        END IF;
--FECHA
        IF TRIM(pin_fecha_nacimiento) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Fecha de nacimiento'
                                    || con_salto;
        END IF;

        BEGIN
            fecha := TO_DATE ( pin_fecha_nacimiento, 'YYYY-MM-DD' );
        EXCEPTION
            WHEN OTHERS THEN
                pout_resultado := 0;
                pout_mensaje := 'Formato de fecha inválido. Use YYYY-MM-DD';
                RETURN;
        END;
--SEXO
        IF TRIM(pin_sexo) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Sexo'
                                    || con_salto;
        END IF;
--TELEFONO
        IF TRIM(pin_telefono) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Teléfono'
                                    || con_salto;
        END IF;
--DIRECCION
        IF TRIM(pin_direccion) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Dirección'
                                    || con_salto;
        END IF;
--CORREO
        IF TRIM(pin_correo_electronico) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Correo electrónico'
                                    || con_salto;
        END IF;
--VALIDAR SI HAY ERRORES
        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;
-- CÉDULA YA EXISTENTE
        existe := 0;
        OPEN c_paciente_cedula(pin_cedula);
        FETCH c_paciente_cedula INTO v_dummy;
        IF c_paciente_cedula%found THEN
            existe := 1;
        END IF;
        CLOSE c_paciente_cedula;
        IF existe > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'Ya existe un paciente con la cedula ingresada';
            RETURN;
        END IF;
--INSERT
-- Cambio de Adry: Sin provincia, canton, distrito (no existen en tabla PACIENTE)
        INSERT INTO paciente (
            cedula,
            primer_nombre,
            segundo_nombre,
            primer_apellido,
            segundo_apellido,
            fecha_nacimiento,
            sexo,
            observaciones,
            telefono,
            direccion,
            correo_electronico
        ) VALUES ( pin_cedula,
                   pin_primer_nombre,
                   pin_segundo_nombre,
                   pin_primer_apellido,
                   pin_segundo_apellido,
                   fecha,
                   pin_sexo,
                   pin_observaciones,
                   pin_telefono,
                   pin_direccion,
                   pin_correo_electronico );

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo crear el paciente';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'Paciente ha sido creado';
        RETURN;
    END;

--MODIFICAR PACIENTE
-- Cambio de Adry: Sin provincia, canton, distrito (no existen en tabla PACIENTE)
    PROCEDURE editar_paciente (
        pin_id                 IN NUMBER,
        pin_cedula             IN VARCHAR2,
        pin_primer_nombre      IN VARCHAR2,
        pin_segundo_nombre     IN VARCHAR2,
        pin_primer_apellido    IN VARCHAR2,
        pin_segundo_apellido   IN VARCHAR2,
        pin_fecha_nacimiento   IN VARCHAR2,
        pin_sexo               IN VARCHAR2,
        pin_observaciones      IN VARCHAR2,
        pin_telefono           IN VARCHAR2,
        pin_direccion          IN VARCHAR2,
        pin_correo_electronico IN VARCHAR2,
        pout_resultado         OUT NUMBER,
        pout_mensaje           OUT VARCHAR2
    ) AS

        fecha                DATE;
        existe               NUMBER;
        var_campos_faltantes VARCHAR2(4000) := '';
        con_salto            CONSTANT VARCHAR2(2) := chr(10);
    BEGIN
--Validaciones
--ID
        IF pin_id IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'El ID del paciente es obligatorio';
            RETURN;
        END IF;
--CEDULA
        IF TRIM(pin_cedula) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Cédula'
                                    || con_salto;
        END IF;
--NOMBRE
        IF TRIM(pin_primer_nombre) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Primer nombre'
                                    || con_salto;
        END IF;
--APELLIDO
        IF TRIM(pin_primer_apellido) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Primer apellido'
                                    || con_salto;
        END IF;
--FECHA
        IF TRIM(pin_fecha_nacimiento) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Fecha de nacimiento'
                                    || con_salto;
        END IF;

        BEGIN
            fecha := TO_DATE ( pin_fecha_nacimiento, 'YYYY-MM-DD' );
        EXCEPTION
            WHEN OTHERS THEN
                pout_resultado := 0;
                pout_mensaje := 'Formato de fecha inválido. Use YYYY-MM-DD';
                RETURN;
        END;
--SEXO
        IF TRIM(pin_sexo) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Sexo'
                                    || con_salto;
        END IF;
--TELEFONO
        IF TRIM(pin_telefono) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Teléfono'
                                    || con_salto;
        END IF;
--DIRECCION
        IF TRIM(pin_direccion) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Dirección'
                                    || con_salto;
        END IF;
--CORREO
        IF TRIM(pin_correo_electronico) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Correo electrónico'
                                    || con_salto;
        END IF;
--VALIDAR SI HAY ERRORES
        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;
--ID NO EXISTE
        SELECT
            COUNT(*)
        INTO existe
        FROM
            paciente
        WHERE
            id_paciente = pin_id;

        IF existe = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe paciente con el ID ingresado';
            RETURN;
        END IF;
--CEDULA YA EXISTENTE
        SELECT
            COUNT(*)
        INTO existe
        FROM
            paciente
        WHERE
                cedula = pin_cedula
            AND id_paciente <> pin_id;

        IF existe > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'Ya existe un paciente con la cedula ingresada';
            RETURN;
        END IF;

--UPDATE
-- Cambio de Adry: Sin provincia, canton, distrito (no existen en tabla PACIENTE)
        UPDATE paciente
        SET
            cedula = pin_cedula,
            primer_nombre = pin_primer_nombre,
            segundo_nombre = pin_segundo_nombre,
            primer_apellido = pin_primer_apellido,
            segundo_apellido = pin_segundo_apellido,
            fecha_nacimiento = fecha,
            sexo = pin_sexo,
            observaciones = pin_observaciones,
            telefono = pin_telefono,
            direccion = pin_direccion,
            correo_electronico = pin_correo_electronico
        WHERE
            id_paciente = pin_id;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo actualizar el paciente';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'Paciente actualizado';
        RETURN;
    END;

--ELIMINAR PACIENTE
    PROCEDURE eliminar_paciente (
        pin_id         IN NUMBER,
        pout_resultado OUT NUMBER,
        pout_mensaje   OUT VARCHAR2
    ) AS
        existe            NUMBER;
        tiene_citas       NUMBER;
        tiene_expedientes NUMBER;
    BEGIN
--Validaciones
--ID
        IF pin_id IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'El ID del paciente es obligatorio';
            RETURN;
        END IF;
--ID NO EXISTE
        SELECT
            COUNT(*)
        INTO existe
        FROM
            paciente
        WHERE
            id_paciente = pin_id;

        IF existe = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe paciente con el ID ingresado';
            RETURN;
        END IF;

--TIENE CITAS ASOCIADAS
        SELECT
            COUNT(*)
        INTO tiene_citas
        FROM
            cita
        WHERE
            id_paciente = pin_id;

        IF tiene_citas > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se puede eliminar el paciente porque tiene citas asociadas';
            RETURN;
        END IF;

--TIENE EPEDIENTES ASOCIADOS
        SELECT
            COUNT(*)
        INTO tiene_expedientes
        FROM
            expediente
        WHERE
            id_paciente = pin_id;

        IF tiene_expedientes > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se puede eliminar el paciente porque tiene expedientes asociadas';
            RETURN;
        END IF;

--DELETE
        DELETE FROM paciente
        WHERE
            id_paciente = pin_id;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo eliminar el paciente';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'Paciente eliminado';
        RETURN;
    END;

END;
/

--------------------------
-- CITA
--------------------------
-----------------
-- CABECERA
-----------------
CREATE OR REPLACE PACKAGE pkg_cita AS

-- INSERTAR CITA
-- Cambio de Adry: Agregados pin_id_estado y pin_id_consultorio
    PROCEDURE agregar_cita (
        pin_id_paciente    IN NUMBER,
        pin_id_medico      IN NUMBER,
        pin_fecha          IN VARCHAR2,
        pin_hora_inicio    IN VARCHAR2,
        pin_hora_fin       IN VARCHAR2,
        pin_id_estado      IN NUMBER,
        pin_id_consultorio IN NUMBER,
        pin_observaciones  IN VARCHAR2,
        pout_resultado     OUT NUMBER,
        pout_mensaje       OUT VARCHAR2
    );

-- EDITAR CITA
-- Cambio de Adry: Agregado pin_id_consultorio
    PROCEDURE editar_cita (
        pin_id_cita               IN NUMBER,
        pin_id_paciente           IN NUMBER,
        pin_id_medico             IN NUMBER,
        pin_fecha                 IN VARCHAR2,
        pin_hora_inicio           IN VARCHAR2,
        pin_hora_fin              IN VARCHAR2,
        pin_id_estado             IN NUMBER,
        pin_id_consultorio        IN NUMBER,
        pin_id_motivo_cancelacion IN NUMBER,
        pin_observaciones         IN VARCHAR2,
        pout_resultado            OUT NUMBER,
        pout_mensaje              OUT VARCHAR2
    );

-- ELIMINAR CITA
    PROCEDURE eliminar_cita (
        pin_id_cita    IN NUMBER,
        pout_resultado OUT NUMBER,
        pout_mensaje   OUT VARCHAR2
    );

    PROCEDURE cancelar_cita (
        pin_id_cita               IN NUMBER,
        pin_id_motivo_cancelacion IN NUMBER,
        pin_observaciones         IN VARCHAR2,
        pout_resultado            OUT NUMBER,
        pout_mensaje              OUT VARCHAR2
    );

END;
/

-- BODY
CREATE OR REPLACE PACKAGE BODY pkg_cita AS

-- FUNCION PRIVADA
-- Cambio de Adry: Convertir día de Oracle (1=Domingo) a formato AGENDA_HORARIO (1=Lunes)
    FUNCTION convertir_dia_semana (
        pin_fecha DATE
    ) RETURN NUMBER IS
        var_dia_oracle VARCHAR2(1);
        var_dia_agenda NUMBER;
    BEGIN
        SELECT
            to_char(pin_fecha, 'D')
        INTO var_dia_oracle
        FROM
            dual;

-- Convertir formato Oracle a AGENDA_HORARIO
        var_dia_agenda :=
            CASE var_dia_oracle
                WHEN '1' THEN
                    7  -- Domingo
                WHEN '2' THEN
                    1  -- Lunes
                WHEN '3' THEN
                    2  -- Martes
                WHEN '4' THEN
                    3  -- Miércoles
                WHEN '5' THEN
                    4  -- Jueves
                WHEN '6' THEN
                    5  -- Viernes
                WHEN '7' THEN
                    6  -- Sábado
            END;

        RETURN var_dia_agenda;
    END convertir_dia_semana;

-- PROCEDIMIENTO PRIVADO
-- Cambio de Adry: Validar disponibilidad del médico según AGENDA_HORARIO
-- Verifica día de semana, horario y disponibilidad
    PROCEDURE validar_disponibilidad_medico (
        pin_id_medico       IN NUMBER,
        pin_fecha           IN DATE,
        pin_hora_ini        IN TIMESTAMP,
        pin_hora_fin        IN TIMESTAMP,
        pout_horario_valido OUT NUMBER,
        pout_mensaje        OUT VARCHAR2
    ) IS

        var_dia_semana_num  NUMBER;
        var_hora_inicio_str VARCHAR2(5);
        var_hora_fin_str    VARCHAR2(5);
        var_horario_texto   VARCHAR2(500);
        var_tiene_horario   NUMBER := 0;
    BEGIN
-- Obtener día de la semana convertido
        var_dia_semana_num := convertir_dia_semana(pin_fecha);

-- Verificar si es fin de semana (Sábado=6, Domingo=7)
        IF var_dia_semana_num >= 6 THEN
            pout_horario_valido := 0;
            pout_mensaje := 'No se pueden agendar citas los fines de semana. Por favor seleccione un día entre Lunes y Viernes.';
            RETURN;
        END IF;

-- Verificar si el médico tiene horario configurado
        SELECT
            COUNT(*)
        INTO var_tiene_horario
        FROM
            medico m
        WHERE
                m.id_medico = pin_id_medico
            AND m.id_horario IS NOT NULL;

        IF var_tiene_horario = 0 THEN
            pout_horario_valido := 0;
            pout_mensaje := 'El médico seleccionado no tiene horarios configurados. Por favor contacte al administrador.';
            RETURN;
        END IF;

        var_hora_inicio_str := to_char(pin_hora_ini, 'HH24:MI');
        var_hora_fin_str := to_char(pin_hora_fin, 'HH24:MI');

-- Verificar si existe una agenda para este médico en este día y turno
        SELECT
            COUNT(*)
        INTO pout_horario_valido
        FROM
                 medico m
            JOIN agenda_horario ah ON ah.id_horario = m.id_horario
        WHERE
                m.id_medico = pin_id_medico
            AND ah.dia_semana = var_dia_semana_num
            AND var_hora_inicio_str >= ah.hora_inicio
            AND var_hora_fin_str <= ah.hora_fin;

        IF pout_horario_valido = 0 THEN
-- Obtener horarios disponibles del médico para mostrar en el mensaje
            BEGIN
                SELECT
                    LISTAGG(ah.horario
                            || ' ('
                            || ah.hora_inicio
                            || '-'
                            || ah.hora_fin
                            || ')', ', ') WITHIN GROUP(
                    ORDER BY
                        ah.dia_semana, ah.turno
                    )
                INTO var_horario_texto
                FROM
                         medico m
                    JOIN agenda_horario ah ON ah.id_horario = m.id_horario
                WHERE
                    m.id_medico = pin_id_medico;

            EXCEPTION
                WHEN OTHERS THEN
                    var_horario_texto := NULL;
            END;

            IF var_horario_texto IS NOT NULL THEN
                pout_mensaje := 'La fecha/hora seleccionada no coincide con el horario del médico. Horarios disponibles: '
                                || var_horario_texto
                                || '. Por favor seleccione un día y horario correcto.';
            ELSE
                pout_mensaje := 'El médico no tiene horarios configurados. Por favor contacte al administrador.';
            END IF;

        END IF;

    END validar_disponibilidad_medico;

-- INSERTAR CITA
-- Cambio de Adry: Ajustado para usar columnas correctas de CITA (ID_ESTADO, FECHA, HORA_AGENDADA_INICIO, HORA_AGENDADA_FIN, OBSERVACIONES)
-- Cambio de Adry: Agregados pin_id_estado y pin_id_consultorio como parámetros
    PROCEDURE agregar_cita (
        pin_id_paciente    IN NUMBER,
        pin_id_medico      IN NUMBER,
        pin_fecha          IN VARCHAR2,
        pin_hora_inicio    IN VARCHAR2,
        pin_hora_fin       IN VARCHAR2,
        pin_id_estado      IN NUMBER,
        pin_id_consultorio IN NUMBER,
        pin_observaciones  IN VARCHAR2,
        pout_resultado     OUT NUMBER,
        pout_mensaje       OUT VARCHAR2
    ) AS

        var_fecha            DATE;
        var_hora_ini         TIMESTAMP;
        var_hora_fin         TIMESTAMP;
        var_id_estado_prog   NUMBER;
        var_count            NUMBER;
        var_citas            NUMBER;
        var_campos_faltantes VARCHAR2(4000) := '';
        con_salto            CONSTANT VARCHAR2(2) := chr(10);
    BEGIN

-- Validaciones de campos obligatorios
        IF pin_id_paciente IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Paciente'
                                    || con_salto;
        END IF;

        IF pin_id_medico IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Médico'
                                    || con_salto;
        END IF;

        IF TRIM(pin_fecha) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Fecha'
                                    || con_salto;
        END IF;

        IF TRIM(pin_hora_inicio) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Hora de inicio'
                                    || con_salto;
        END IF;

        IF TRIM(pin_hora_fin) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Hora de fin'
                                    || con_salto;
        END IF;

-- Cambio de Adry: Validación de estado y consultorio
        IF pin_id_estado IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Estado'
                                    || con_salto;
        END IF;

        IF pin_id_consultorio IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Consultorio'
                                    || con_salto;
        END IF;

        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;

-- Conversión de fecha y horas
        BEGIN
            var_fecha := TO_DATE ( pin_fecha, 'DD/MM/YYYY' );
        EXCEPTION
            WHEN OTHERS THEN
                pout_resultado := 0;
                pout_mensaje := 'Formato de fecha inválido. Use DD/MM/YYYY';
                RETURN;
        END;

        BEGIN
            var_hora_ini := TO_TIMESTAMP ( pin_fecha
                                           || ' '
                                           || pin_hora_inicio, 'DD/MM/YYYY HH24:MI' );
        EXCEPTION
            WHEN OTHERS THEN
                pout_resultado := 0;
                pout_mensaje := 'Formato de hora de inicio inválido. Use HH24:MI';
                RETURN;
        END;

        BEGIN
            var_hora_fin := TO_TIMESTAMP ( pin_fecha
                                           || ' '
                                           || pin_hora_fin, 'DD/MM/YYYY HH24:MI' );
        EXCEPTION
            WHEN OTHERS THEN
                pout_resultado := 0;
                pout_mensaje := 'Formato de hora de fin inválido. Use HH24:MI';
                RETURN;
        END;

        IF var_hora_ini >= var_hora_fin THEN
            pout_resultado := 0;
            pout_mensaje := 'La hora de inicio debe ser menor que la hora de fin';
            RETURN;
        END IF;

-- Validar existencia de Paciente y Médico
        SELECT
            COUNT(*)
        INTO var_count
        FROM
            paciente
        WHERE
            id_paciente = pin_id_paciente;

        IF var_count = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe el paciente seleccionado';
            RETURN;
        END IF;

        SELECT
            COUNT(*)
        INTO var_count
        FROM
            medico
        WHERE
            id_medico = pin_id_medico;

        IF var_count = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe el médico seleccionado';
            RETURN;
        END IF;

-- Cambio de Adry: Validar disponibilidad del médico usando procedimiento privado
        DECLARE
            var_horario_valido  NUMBER := 0;
            var_mensaje_horario VARCHAR2(500);
        BEGIN
            validar_disponibilidad_medico(
                pin_id_medico       => pin_id_medico,
                pin_fecha           => var_fecha,
                pin_hora_ini        => var_hora_ini,
                pin_hora_fin        => var_hora_fin,
                pout_horario_valido => var_horario_valido,
                pout_mensaje        => var_mensaje_horario
            );

            IF var_horario_valido = 0 THEN
                pout_resultado := 0;
                pout_mensaje := var_mensaje_horario;
                RETURN;
            END IF;

        END;
-- Validar que no exista otra cita en el mismo rango
        SELECT
            COUNT(*)
        INTO var_citas
        FROM
            cita
        WHERE
                id_medico = pin_id_medico
            AND fecha = var_fecha
            AND var_hora_ini < hora_agendada_fin
            AND var_hora_fin > hora_agendada_inicio;

        IF var_citas > 0 THEN
            raise_application_error(-20012, 'Ya existe otra cita en ese rango de horas para ese médico/consultorio');
        END IF;

-- INSERT en tabla CITA
-- Cambio de Adry: Usar columnas correctas con id_estado y id_consultorio como parámetros
        INSERT INTO cita (
            id_paciente,
            id_medico,
            id_consultorio,
            id_estado,
            fecha,
            hora_agendada_inicio,
            hora_agendada_fin,
            observaciones
        ) VALUES ( pin_id_paciente,
                   pin_id_medico,
                   pin_id_consultorio,
                   pin_id_estado,
                   var_fecha,
                   var_hora_ini,
                   var_hora_fin,
                   pin_observaciones );

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo crear la cita';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'La cita ha sido creada correctamente';
        RETURN;
    END;

-- EDITAR CITA
-- Cambio de Adry: Ajustado para usar columnas correctas de CITA con ID_ESTADO y HORA_AGENDADA_INICIO/FIN
-- Cambio de Adry: Agregado pin_id_consultorio como parámetro
    PROCEDURE editar_cita (
        pin_id_cita               IN NUMBER,
        pin_id_paciente           IN NUMBER,
        pin_id_medico             IN NUMBER,
        pin_fecha                 IN VARCHAR2,
        pin_hora_inicio           IN VARCHAR2,
        pin_hora_fin              IN VARCHAR2,
        pin_id_estado             IN NUMBER,
        pin_id_consultorio        IN NUMBER,
        pin_id_motivo_cancelacion IN NUMBER,
        pin_observaciones         IN VARCHAR2,
        pout_resultado            OUT NUMBER,
        pout_mensaje              OUT VARCHAR2
    ) AS

        var_fecha            DATE;
        var_hora_ini         TIMESTAMP;
        var_hora_fin         TIMESTAMP;
        var_count            NUMBER;
        var_citas            NUMBER;
        var_campos_faltantes VARCHAR2(4000) := '';
        con_salto            CONSTANT VARCHAR2(2) := chr(10);
    BEGIN
-- Validar ID de cita
        IF pin_id_cita IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'El ID de la cita es obligatorio';
            RETURN;
        END IF;

        SELECT
            COUNT(*)
        INTO var_count
        FROM
            cita
        WHERE
            id_cita = pin_id_cita;

        IF var_count = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe la cita seleccionada';
            RETURN;
        END IF;

-- Validaciones básicas
        IF pin_id_paciente IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Paciente'
                                    || con_salto;
        END IF;

        IF pin_id_medico IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Médico'
                                    || con_salto;
        END IF;

        IF TRIM(pin_fecha) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Fecha'
                                    || con_salto;
        END IF;

        IF TRIM(pin_hora_inicio) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Hora de inicio'
                                    || con_salto;
        END IF;

        IF TRIM(pin_hora_fin) IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Hora de fin'
                                    || con_salto;
        END IF;

        IF pin_id_estado IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Estado'
                                    || con_salto;
        END IF;

-- Cambio de Adry: Validación de consultorio
        IF pin_id_consultorio IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Consultorio'
                                    || con_salto;
        END IF;

        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;

-- Conversión de fecha y horas
        BEGIN
            var_fecha := TO_DATE ( pin_fecha, 'DD/MM/YYYY' );
        EXCEPTION
            WHEN OTHERS THEN
                pout_resultado := 0;
                pout_mensaje := 'Formato de fecha inválido. Use DD/MM/YYYY';
                RETURN;
        END;

        BEGIN
            var_hora_ini := TO_TIMESTAMP ( pin_fecha
                                           || ' '
                                           || pin_hora_inicio, 'DD/MM/YYYY HH24:MI' );
        EXCEPTION
            WHEN OTHERS THEN
                pout_resultado := 0;
                pout_mensaje := 'Formato de hora de inicio inválido. Use HH24:MI';
                RETURN;
        END;

        BEGIN
            var_hora_fin := TO_TIMESTAMP ( pin_fecha
                                           || ' '
                                           || pin_hora_fin, 'DD/MM/YYYY HH24:MI' );
        EXCEPTION
            WHEN OTHERS THEN
                pout_resultado := 0;
                pout_mensaje := 'Formato de hora de fin inválido. Use HH24:MI';
                RETURN;
        END;

        IF var_hora_ini >= var_hora_fin THEN
            pout_resultado := 0;
            pout_mensaje := 'La hora de inicio debe ser menor que la hora de fin';
            RETURN;
        END IF;

-- Validar existencia de Paciente, Médico y Estado
        SELECT
            COUNT(*)
        INTO var_count
        FROM
            paciente
        WHERE
            id_paciente = pin_id_paciente;

        IF var_count = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe el paciente seleccionado';
            RETURN;
        END IF;

        SELECT
            COUNT(*)
        INTO var_count
        FROM
            medico
        WHERE
            id_medico = pin_id_medico;

        IF var_count = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe el médico seleccionado';
            RETURN;
        END IF;

        SELECT
            COUNT(*)
        INTO var_count
        FROM
            estado_cita
        WHERE
            id_estado = pin_id_estado;

        IF var_count = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe el estado seleccionado';
            RETURN;
        END IF;

-- Cambio de Adry: Validar disponibilidad del médico usando procedimiento privado
        DECLARE
            var_horario_valido  NUMBER := 0;
            var_mensaje_horario VARCHAR2(500);
        BEGIN
            validar_disponibilidad_medico(
                pin_id_medico       => pin_id_medico,
                pin_fecha           => var_fecha,
                pin_hora_ini        => var_hora_ini,
                pin_hora_fin        => var_hora_fin,
                pout_horario_valido => var_horario_valido,
                pout_mensaje        => var_mensaje_horario
            );

            IF var_horario_valido = 0 THEN
                pout_resultado := 0;
                pout_mensaje := var_mensaje_horario;
                RETURN;
            END IF;

        END;

-- Validar que no exista otra cita en el mismo rango
        SELECT
            COUNT(*)
        INTO var_citas
        FROM
            cita
        WHERE
                id_medico = pin_id_medico
            AND fecha = var_fecha
            AND var_hora_ini < hora_agendada_fin
            AND var_hora_fin > hora_agendada_inicio;

        IF var_citas > 0 THEN
            raise_application_error(-20012, 'Ya existe otra cita en ese rango de horas para ese médico/consultorio');
        END IF;

-- UPDATE de la cita
-- Cambio de Adry: Usar columnas correctas (ID_ESTADO, FECHA, HORA_AGENDADA_INICIO, HORA_AGENDADA_FIN, ID_MOTIVO_CANCELACION, OBSERVACIONES)
-- Cambio de Adry: Agregado ID_CONSULTORIO en el UPDATE
        UPDATE cita
        SET
            id_paciente = pin_id_paciente,
            id_medico = pin_id_medico,
            id_consultorio = pin_id_consultorio,
            fecha = var_fecha,
            hora_agendada_inicio = var_hora_ini,
            hora_agendada_fin = var_hora_fin,
            id_estado = pin_id_estado,
            id_motivo_cancelacion = pin_id_motivo_cancelacion,
            observaciones = pin_observaciones
        WHERE
            id_cita = pin_id_cita;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo actualizar la cita';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'La cita ha sido actualizada correctamente';
        RETURN;
    EXCEPTION
        WHEN OTHERS THEN
            pout_resultado := 0;
            -- Coloque una observación 
            IF SQLCODE = -20022 THEN
                pout_mensaje := 'Coloque una observacion';
            -- Si el error viene del trigger por fecha en el pasado
            ELSIF SQLCODE = -20020 THEN
                pout_mensaje := 'La fecha de la cita no puede estar en el pasado';
            
            -- Opcional: si querés manejar también el del trigger validar_rango_cita
            ELSIF SQLCODE = -20010 THEN
                pout_mensaje := 'La cita no puede ser en una fecha/hora anterior a la actual';
            
            ELSE
                -- Cualquier otro error: limpiamos el ORA-xxxxx
                pout_mensaje := REGEXP_REPLACE(SQLERRM, '^ORA-[0-9]+: *', '');
            END IF;
            RETURN;
    END;

-- ELIMINAR CITA
    PROCEDURE eliminar_cita (
        pin_id_cita    IN NUMBER,
        pout_resultado OUT NUMBER,
        pout_mensaje   OUT VARCHAR2
    ) AS

        var_count NUMBER;

-- Cursor para verificar atenciones asociadas a la cita
        CURSOR c_atencion_cita (
            p_id_cita NUMBER
        ) IS
        SELECT
            1
        FROM
            atencion
        WHERE
            id_cita = p_id_cita;

        v_dummy   NUMBER;
    BEGIN
        IF pin_id_cita IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'El ID de la cita es obligatorio';
            RETURN;
        END IF;

        SELECT
            COUNT(*)
        INTO var_count
        FROM
            cita
        WHERE
            id_cita = pin_id_cita;

        IF var_count = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe la cita seleccionada';
            RETURN;
        END IF;

-- Validar que no tenga atenciones asociadas
        var_count := 0;
        OPEN c_atencion_cita(pin_id_cita);
        FETCH c_atencion_cita INTO v_dummy;
        IF c_atencion_cita%found THEN
            var_count := 1;
        END IF;
        CLOSE c_atencion_cita;
        IF var_count > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se puede eliminar la cita porque tiene atenciones asociadas';
            RETURN;
        END IF;

        DELETE FROM cita
        WHERE
            id_cita = pin_id_cita;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo eliminar la cita';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'La cita ha sido eliminada correctamente';
        RETURN;
    END;

    PROCEDURE cancelar_cita (
        pin_id_cita               IN NUMBER,
        pin_id_motivo_cancelacion IN NUMBER,
        pin_observaciones         IN VARCHAR2,
        pout_resultado            OUT NUMBER,
        pout_mensaje              OUT VARCHAR2
    ) AS
        var_count          NUMBER;
        var_id_estado_canc NUMBER;
        var_observaciones  VARCHAR2(4000);
    BEGIN
        IF pin_id_cita IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'El ID de la cita es obligatorio';
            RETURN;
        END IF;

-- Validar que la cita exista
        SELECT
            COUNT(*)
        INTO var_count
        FROM
            cita
        WHERE
            id_cita = pin_id_cita;

        IF var_count = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe la cita seleccionada';
            RETURN;
        END IF;

-- Obtener ID del estado "Cancelada"
-- Cambio de Adry: Buscar dinámicamente en vez de usar ID=2 hardcoded
        BEGIN
            SELECT
                id_estado
            INTO var_id_estado_canc
            FROM
                estado_cita
            WHERE
                nombre_estado = 'Cancelada';

        EXCEPTION
            WHEN no_data_found THEN
                pout_resultado := 0;
                pout_mensaje := 'No existe el estado "Cancelada" en la base de datos';
                RETURN;
        END;
    -- Asignar observaciones por defecto si no se proporcionan
        var_observaciones := TRIM(pin_observaciones);
        IF var_observaciones IS NULL THEN
            var_observaciones := 'Cancelada';
        END IF;

-- Actualizar la cita a estado Cancelada
        UPDATE cita
        SET
            id_estado = var_id_estado_canc,
            id_motivo_cancelacion = pin_id_motivo_cancelacion,
            observaciones = var_observaciones
        WHERE
            id_cita = pin_id_cita;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo cancelar la cita';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'La cita ha sido cancelada correctamente';
        RETURN;
    END cancelar_cita;

END;
/

--------------------------
-- EXPEDIENTE
--------------------------
-----------
-- CABECERA
-----------
-- Cambio de Adry: Nuevo paquete para gestión de expedientes médicos
CREATE OR REPLACE PACKAGE pkg_expediente AS

-- CREAR EXPEDIENTE
-- Cambio de Adry: Crear expediente con fecha automática SYSDATE
    PROCEDURE crear_expediente (
        pin_id_paciente IN NUMBER,
        pin_notas       IN VARCHAR2,
        pout_resultado  OUT NUMBER,
        pout_mensaje    OUT VARCHAR2
    );

-- ACTUALIZAR EXPEDIENTE
-- Cambio de Adry: Solo permite actualizar NOTAS, no se puede borrar
    PROCEDURE actualizar_expediente (
        pin_id_expediente IN NUMBER,
        pin_notas         IN VARCHAR2,
        pout_resultado    OUT NUMBER,
        pout_mensaje      OUT VARCHAR2
    );

END pkg_expediente;
/

------
--BODY
------
CREATE OR REPLACE PACKAGE BODY pkg_expediente AS

-- CREAR EXPEDIENTE
-- Cambio de Adry: Validar que paciente exista y no tenga expediente previo
    PROCEDURE crear_expediente (
        pin_id_paciente IN NUMBER,
        pin_notas       IN VARCHAR2,
        pout_resultado  OUT NUMBER,
        pout_mensaje    OUT VARCHAR2
    ) AS

        var_existe_paciente   NUMBER;
        var_existe_expediente NUMBER;
        var_campos_faltantes  VARCHAR2(4000) := '';
        con_salto             CONSTANT VARCHAR2(2) := chr(10);
    BEGIN
-- Validación de campo obligatorio
        IF pin_id_paciente IS NULL THEN
            var_campos_faltantes := var_campos_faltantes
                                    || '- Paciente'
                                    || con_salto;
        END IF;

        IF var_campos_faltantes IS NOT NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'Los siguientes campos son obligatorios:'
                            || con_salto
                            || var_campos_faltantes;
            RETURN;
        END IF;

-- Validar que el paciente exista
        SELECT
            COUNT(*)
        INTO var_existe_paciente
        FROM
            paciente
        WHERE
            id_paciente = pin_id_paciente;

        IF var_existe_paciente = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe el paciente seleccionado';
            RETURN;
        END IF;

-- Validar que el paciente no tenga ya un expediente
        SELECT
            COUNT(*)
        INTO var_existe_expediente
        FROM
            expediente
        WHERE
            id_paciente = pin_id_paciente;

        IF var_existe_expediente > 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'El paciente ya tiene un expediente registrado';
            RETURN;
        END IF;

-- INSERT en EXPEDIENTE
-- Cambio de Adry: CREADO_EN se establece automáticamente con SYSDATE
        INSERT INTO expediente (
            id_paciente,
            creado_en,
            notas
        ) VALUES ( pin_id_paciente,
                   sysdate,
                   pin_notas );

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo crear el expediente';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'Expediente ha sido creado correctamente';
        RETURN;
    END crear_expediente;

-- ACTUALIZAR EXPEDIENTE
-- Cambio de Adry: Solo se pueden actualizar las NOTAS, no la fecha ni el paciente
    PROCEDURE actualizar_expediente (
        pin_id_expediente IN NUMBER,
        pin_notas         IN VARCHAR2,
        pout_resultado    OUT NUMBER,
        pout_mensaje      OUT VARCHAR2
    ) AS
        var_existe_expediente NUMBER;
    BEGIN
-- Validar ID de expediente
        IF pin_id_expediente IS NULL THEN
            pout_resultado := 0;
            pout_mensaje := 'El ID del expediente es obligatorio';
            RETURN;
        END IF;

-- Validar que el expediente exista
        SELECT
            COUNT(*)
        INTO var_existe_expediente
        FROM
            expediente
        WHERE
            id_expediente = pin_id_expediente;

        IF var_existe_expediente = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No existe el expediente seleccionado';
            RETURN;
        END IF;

-- UPDATE del expediente (solo se pueden actualizar las notas)
        UPDATE expediente
        SET
            notas = pin_notas
        WHERE
            id_expediente = pin_id_expediente;

        IF SQL%rowcount = 0 THEN
            pout_resultado := 0;
            pout_mensaje := 'No se pudo actualizar el expediente';
            ROLLBACK;
            RETURN;
        END IF;

        COMMIT;
        pout_resultado := 1;
        pout_mensaje := 'El expediente ha sido actualizado correctamente';
        RETURN;
    END actualizar_expediente;

END pkg_expediente;
/

-- RESUMEN DE PACKAGES:
--   - PKG_PERSONAL:   3 procedimientos (agregar, editar, eliminar)
--   - PKG_MEDICO:     3 procedimientos (agregar, editar, eliminar)
--   - PKG_PACIENTE:   3 procedimientos (agregar, editar, eliminar)
--   - PKG_CITA:       4 procedimientos (agregar, editar, eliminar, cancelar)
--                     1 función privada (convertir_dia_semana)
--                     1 procedimiento privado (validar_disponibilidad_medico)
--   - PKG_EXPEDIENTE: 2 procedimientos (crear, actualizar)
-- ============================================================================
-- NOTA: Ejecutar este script después de create_tables.sql
-- ============================================================================
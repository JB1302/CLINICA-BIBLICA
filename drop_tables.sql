-- Script para eliminar todas las tablas del usuario CLINICA

BEGIN
  FOR rec IN (
    SELECT sid, serial# 
    FROM v$session 
    WHERE username = 'CLINICA'
      AND sid != (SELECT sid FROM v$mystat WHERE rownum = 1)
  ) LOOP
    EXECUTE IMMEDIATE 'ALTER SYSTEM KILL SESSION ''' || rec.sid || ',' || rec.serial# || ''' IMMEDIATE';
  END LOOP;
END;
/

-- Eliminar todas las tablas del usuario CLINICA
BEGIN
  FOR rec IN (
    SELECT table_name 
    FROM dba_tables 
    WHERE owner = 'CLINICA'
    ORDER BY table_name
  ) LOOP
    BEGIN
      EXECUTE IMMEDIATE 'DROP TABLE CLINICA.' || rec.table_name || ' CASCADE CONSTRAINTS PURGE';
      DBMS_OUTPUT.PUT_LINE('Eliminada: ' || rec.table_name);
    EXCEPTION
      WHEN OTHERS THEN
        DBMS_OUTPUT.PUT_LINE('Error eliminando ' || rec.table_name || ': ' || SQLERRM);
    END;
  END LOOP;
END;
/

-- Verificar que no quedan tablas
SELECT COUNT(*) as TABLAS_RESTANTES FROM dba_tables WHERE owner = 'CLINICA';

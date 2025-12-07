#!/usr/bin/env python3
"""
Script para ejecutar create_tables.sql e insert_data.sql en el servidor Oracle remoto
"""
import cx_Oracle

# Configuración de conexión
username = "CLINICA"
password = "1234"
dsn = "4.156.223.214:1521/XEPDB1"

try:
    print("Conectando a Oracle Database...")
    connection = cx_Oracle.connect(username, password, dsn, encoding="UTF-8")
    cursor = connection.cursor()
    
    print("✓ Conexión exitosa")
    
    # Leer y ejecutar create_tables.sql
    print("\n=== EJECUTANDO create_tables.sql ===")
    with open('create_tables.sql', 'r', encoding='utf-8') as f:
        sql_script = f.read()
    
    # Dividir por punto y coma y ejecutar cada sentencia
    statements = [stmt.strip() for stmt in sql_script.split(';') if stmt.strip()]
    
    for i, stmt in enumerate(statements, 1):
        if stmt.strip().upper().startswith('--') or not stmt.strip():
            continue
        try:
            cursor.execute(stmt)
            print(f"  ✓ Sentencia {i} ejecutada")
        except cx_Oracle.DatabaseError as e:
            error, = e.args
            # Ignorar error "tabla ya existe"
            if error.code == 955:
                print(f"  ⚠ Sentencia {i}: tabla ya existe (ignorado)")
            else:
                print(f"  ✗ Error en sentencia {i}: {error.message}")
    
    connection.commit()
    print("✓ Tablas creadas exitosamente\n")
    
    # Leer y ejecutar insert_data.sql
    print("=== EJECUTANDO insert_data.sql ===")
    with open('insert_data.sql', 'r', encoding='utf-8') as f:
        sql_script = f.read()
    
    statements = [stmt.strip() for stmt in sql_script.split(';') if stmt.strip()]
    
    insert_count = 0
    for i, stmt in enumerate(statements, 1):
        if stmt.strip().upper().startswith('--') or not stmt.strip():
            continue
        try:
            cursor.execute(stmt)
            insert_count += 1
            if insert_count % 10 == 0:
                print(f"  ✓ {insert_count} inserts ejecutados...")
        except cx_Oracle.DatabaseError as e:
            error, = e.args
            print(f"  ✗ Error en insert {i}: {error.message}")
    
    connection.commit()
    print(f"✓ Total: {insert_count} registros insertados exitosamente\n")
    
    # Verificar datos
    print("=== VERIFICACIÓN DE DATOS ===")
    tables = ['PACIENTE', 'PERSONAL', 'MEDICO', 'CITA', 'ESPECIALIDAD', 'CLINICA']
    for table in tables:
        cursor.execute(f"SELECT COUNT(*) FROM {table}")
        count = cursor.fetchone()[0]
        print(f"  {table}: {count} registros")
    
    cursor.close()
    connection.close()
    print("\n✓ Script completado exitosamente")
    
except cx_Oracle.DatabaseError as e:
    error, = e.args
    print(f"✗ Error de base de datos: {error.message}")
except FileNotFoundError as e:
    print(f"✗ Archivo no encontrado: {e}")
except Exception as e:
    print(f"✗ Error: {str(e)}")

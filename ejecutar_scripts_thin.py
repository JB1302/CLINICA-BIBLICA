#!/usr/bin/env python3
"""
Script para ejecutar create_tables.sql e insert_data.sql usando python-oracledb (thin mode - no requiere Instant Client)
"""
import oracledb

# Configuración de conexión
username = "CLINICA"
password = "1234"
host = "4.156.223.214"
port = 1521
service_name = "XEPDB1"

try:
    print("Conectando a Oracle Database (thin mode)...")
    connection = oracledb.connect(
        user=username,
        password=password,
        host=host,
        port=port,
        service_name=service_name
    )
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
        except oracledb.DatabaseError as e:
            error, = e.args
            # Ignorar error "tabla ya existe" (ORA-00955)
            if 'ORA-00955' in str(error):
                print(f"  ⚠ Sentencia {i}: tabla ya existe (ignorado)")
            else:
                print(f"  ✗ Error en sentencia {i}: {error}")
    
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
        except oracledb.DatabaseError as e:
            error, = e.args
            print(f"  ✗ Error en insert {i}: {error}")
    
    connection.commit()
    print(f"✓ Total: {insert_count} registros insertados exitosamente\n")
    
    # Verificar datos
    print("=== VERIFICACIÓN DE DATOS ===")
    tables = ['PACIENTE', 'PERSONAL', 'MEDICO', 'CITA', 'ESPECIALIDAD', 'CLINICA', 'AGENDA_MEDICA', 'ATENCION']
    for table in tables:
        try:
            cursor.execute(f"SELECT COUNT(*) FROM {table}")
            count = cursor.fetchone()[0]
            print(f"  {table}: {count} registros")
        except:
            print(f"  {table}: no existe o sin acceso")
    
    cursor.close()
    connection.close()
    print("\n✓ Script completado exitosamente")
    
except oracledb.DatabaseError as e:
    print(f"✗ Error de base de datos: {e}")
except FileNotFoundError as e:
    print(f"✗ Archivo no encontrado: {e}")
except Exception as e:
    print(f"✗ Error: {str(e)}")

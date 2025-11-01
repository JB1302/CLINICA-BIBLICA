# Proyecto: Sistema de Gestión Clínica 

Este proyecto consiste en un sistema web sencillo para una **Clínica Privada de Medicina General**. 
Su propósito es **organizar información médica y administrativa**, permitiendo gestionar pacientes, citas y médicos desde una interfaz simple.

El objetivo principal es ofrecer una forma clara y centralizada de manejar la información clínica, facilitando el trabajo del personal y reduciendo errores administrativos.  
El enfoque está en mostrar datos en tablas y ejecutar operaciones CRUD básicas.

## Objetivos del sistema
- Registrar y administrar pacientes, citas y médicos.
- Proveer acceso rápido y seguro a la información clínica.
- Reducir errores mediante estructura organizada y normalización.
- Mostrar reportes simples del sistema (sin CRUD).

---

## Vistas del proyecto

### `views/home.php`
- Bienvenida breve.
- Enlaces a Pacientes, Citas, Médicos y Reportes.

### `views/pacientes.php` *(CRUD)*
- Tabla: ID, Nombre, Identificación, Teléfono.
- Crear: formulario de alta.
- Editar: formulario de edición.
- Eliminar: botón por fila.
- Búsqueda y filtros básicos.

### `views/citas.php` *(CRUD)*
- Tabla: ID, Paciente, Médico, Fecha, Hora, Estado.
- Crear cita.
- Editar y reprogramar.
- Cancelar cita.
- Filtros por estado y fechas.
- Motivo de cancelación cuando aplique.

### `views/medicos.php` *(CRUD)*
- Tabla: ID, Nombre, Colegiado, Especialidad.
- Crear médico.
- Editar información.
- Eliminar registro.
- Búsqueda por nombre o colegiado.

### `views/reportes.php`
- Resúmenes de pacientes, citas y atenciones.
- Filtros por fecha y estado.
- Tablas de resumen.

---

Este documento sirve como guía rápida para estructurar las vistas y funcionalidades principales del sistema mientras se desarrolla.

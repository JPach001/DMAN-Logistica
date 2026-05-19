-- =================================================================================
-- SCRIPT 07: POBLADO DE DATOS DE PRUEBA (SEED DATA)
-- =================================================================================
USE logistica_dman;

-- Desactivamos temporalmente las llaves foráneas para asegurar un vaciado limpio si se vuelve a ejecutar
-- 1. Desactivamos las alertas de llaves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Borramos los datos (DELETE FROM) y reiniciamos los IDs (AUTO_INCREMENT = 1)
DELETE FROM BITACORA;
ALTER TABLE BITACORA AUTO_INCREMENT = 1;

DELETE FROM PAGOS;
ALTER TABLE PAGOS AUTO_INCREMENT = 1;

-- Estas tablas son intermedias y no usan AUTO_INCREMENT, solo se vacían
DELETE FROM VIAJE_EMPLEADOS;
DELETE FROM VIAJE_CAMIONETAS;

DELETE FROM VIAJES;
ALTER TABLE VIAJES AUTO_INCREMENT = 1;

-- Esta tabla usa el mismo ID de USUARIOS, no lleva AUTO_INCREMENT
DELETE FROM PERFIL_EMPLEADO;

DELETE FROM USUARIOS;
ALTER TABLE USUARIOS AUTO_INCREMENT = 1;

DELETE FROM CONFIG_COTIZADOR;
ALTER TABLE CONFIG_COTIZADOR AUTO_INCREMENT = 1;

DELETE FROM CAMIONETAS;
ALTER TABLE CAMIONETAS AUTO_INCREMENT = 1;

DELETE FROM TIPO_USUARIO;
ALTER TABLE TIPO_USUARIO AUTO_INCREMENT = 1;

-- 3. Volvemos a encender la seguridad del motor
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------------
-- 1. INSERTAR CATÁLOGOS DE ROLES (TIPO_USUARIO)
-- ---------------------------------------------------------------------------------
-- Esto generará id_tipo_usuario: 1 (Admin), 2 (Empleado), 3 (Cliente)
INSERT INTO TIPO_USUARIO (nombre_rol) VALUES 
('Admin'),
('Empleado'),
('Cliente');

-- ---------------------------------------------------------------------------------
-- 2. INSERTAR CONFIGURACIÓN INICIAL DEL COTIZADOR
-- ---------------------------------------------------------------------------------
INSERT INTO CONFIG_COTIZADOR (
    precio_km, precio_por_piso, precio_por_emplaye, 
    precio_volumen_bajo, precio_volumen_medio, precio_volumen_alto, 
    sueldo_base_cargador, sueldo_base_chofer
) VALUES 
(18.50, 120.00, 75.00, 450.00, 850.00, 1600.00, 350.00, 550.00);

-- ---------------------------------------------------------------------------------
-- 3. INSERTAR UNIDADES DE TRANSPORTE (CAMIONETAS)
-- ---------------------------------------------------------------------------------
INSERT INTO CAMIONETAS (marca, modelo, placa, capacidad_m3, estado) VALUES 
('Nissan', 'Urvan Cargo', 'LMX-45-12', 10.50, 'Disponible'),
('Ford', 'Transit Custom', 'VNZ-89-76', 15.00, 'Disponible'),
('Chevrolet', 'Silverado 3500', 'XTR-23-44', 8.00, 'Mantenimiento'),
('Mercedes-Benz', 'Sprinter', 'KPL-67-89', 17.20, 'Disponible');

-- ---------------------------------------------------------------------------------
-- 4. INSERTAR USUARIOS (ADMINS, EMPLEADOS Y CLIENTES)
-- ---------------------------------------------------------------------------------
-- id_usuario: 1 (Admin)
INSERT INTO USUARIOS (id_tipo_usuario, nombre, apellidos, correo, celular, password_hash) VALUES 
(1, 'Carlos', 'Mendoza Ruiz', 'carlos.admin@dman.com', '4421234567', '1234');

-- id_usuario: 2 y 3 (Empleados)
INSERT INTO USUARIOS (id_tipo_usuario, nombre, apellidos, correo, celular, password_hash) VALUES 
(2, 'Juan Alberto', 'Pérez Gómez', 'juan.chofer@dman.com', '4429876543', '1234'),
(2, 'Pedro Luis', 'Rodríguez Solís', 'pedro.cargador@dman.com', '4425556677', '1234');

-- id_usuario: 4 y 5 (Clientes)
INSERT INTO USUARIOS (id_tipo_usuario, nombre, apellidos, correo, celular, password_hash) VALUES 
(3, 'María Elena', 'Delgado Cruz', 'maria.cliente@gmail.com', '5511223344', '1234'),
(3, 'Jorge', 'Hernández Marín', 'jorge.cliente@outlook.com', '5599887766', '1234');

-- ---------------------------------------------------------------------------------
-- 5. INSERTAR PERFILES EXTENDIDOS DE EMPLEADOS
-- ---------------------------------------------------------------------------------
-- Vinculamos los usuarios 2 y 3 como empleados operativos reales
INSERT INTO PERFIL_EMPLEADO (id_usuario, habilidad, numero_licencia, estado) VALUES 
(2, 'Chofer', 'LIC-FED-A-9988', 'Activo'),
(3, 'Cargador', NULL, 'Activo');

-- ---------------------------------------------------------------------------------
-- 6. INSERTAR REGISTROS DE VIAJES (MÓDULO OPERATIVO)
-- ---------------------------------------------------------------------------------
-- Viaje 1: Cotizado (Fase de borrador, sin recursos asignados)
INSERT INTO VIAJES (
    id_cliente, origen, destino, fecha_hora_inicio, duracion_estimada_min, 
    distancia_km, requiere_factura, anticipo_pagado, estado, volumen_articulos, 
    costo_total_estimado
) VALUES 
(4, 'Av. Constituyentes 45, Querétaro', 'Calle Corregidora 12, Querétaro', '2026-06-01 09:00:00', 45, 8.50, FALSE, FALSE, 'Cotizado', 'Bajo', 650.00);

-- Viaje 2: Confirmado (Listo para ejecutarse, recursos asignados)
INSERT INTO VIAJES (
    id_cliente, origen, destino, fecha_hora_inicio, duracion_estimada_min, 
    distancia_km, requiere_factura, anticipo_pagado, estado, volumen_articulos, 
    costo_total_estimado
) VALUES 
(4, 'Juriquilla Santa Fe, Qro', 'El Refugio, Querétaro', '2026-06-05 08:30:00', 90, 24.00, TRUE, TRUE, 'Confirmado', 'Medio', 1850.00);

-- Viaje 3: Terminado (Servicio cerrado con costos reales calculados)
INSERT INTO VIAJES (
    id_cliente, origen, destino, fecha_hora_inicio, duracion_estimada_min, 
    distancia_km, requiere_factura, anticipo_pagado, estado, volumen_articulos, 
    costo_gasolina, costo_casetas, costos_adicionales, costo_empleados, costo_total_estimado, costo_total_real
) VALUES 
(5, 'Colonia Centro, San Juan del Río', 'Polanco, CDMX', '2026-05-10 06:00:00', 240, 165.00, TRUE, TRUE, 'Terminado', 'Alto', 850.00, 360.00, 150.00, 900.00, 4800.00, 5060.00);

-- ---------------------------------------------------------------------------------
-- 7. ASIGNACIÓN DE RECURSOS A LOS VIAJES (TABLAS INTERMEDIAS)
-- ---------------------------------------------------------------------------------
-- Asignar Camioneta al Viaje 2 y Viaje 3
INSERT INTO VIAJE_CAMIONETAS (id_viaje, id_camioneta) VALUES 
(2, 1), -- Viaje 2 usa la Nissan Urvan
(3, 2); -- Viaje 3 usa la Ford Transit

-- Asignar Personal Operativo al Viaje 2 y Viaje 3
INSERT INTO VIAJE_EMPLEADOS (id_viaje, id_empleado, rol_en_este_viaje) VALUES 
(2, 2, 'Chofer'), -- Viaje 2 solo necesita chofer (volumen bajo/medio)
(3, 2, 'Chofer'), -- Viaje 3 necesita equipo completo
(3, 3, 'Cargador');

-- ---------------------------------------------------------------------------------
-- 8. REGISTRO DE PAGOS (ADMINISTRATIVO Y FINANCIERO)
-- ---------------------------------------------------------------------------------
INSERT INTO PAGOS (id_viaje, monto, metodo_pago, referencia_bancaria, fecha_pago, estatus_pago) VALUES 
(2, 500.00, 'Transferencia', 'TRX-998877', '2026-05-18 14:22:00', 'Liquidado'), -- Anticipo del viaje 2
(3, 5060.00, 'Transferencia', 'TRX-112233', '2026-05-10 11:30:00', 'Liquidado'); -- Liquidación completa viaje 3

-- ---------------------------------------------------------------------------------
-- 9. REGISTRO INICIAL EN BITÁCORA (Simulación de logs del sistema)
-- ---------------------------------------------------------------------------------
INSERT INTO BITACORA (id_usuario, accion, tabla_afectada) VALUES 
(1, 'Poblado inicial de la base de datos ejecutado con éxito.', 'SISTEMA'),
(4, 'Cliente registrado mediante formulario web corporativo.', 'USUARIOS');
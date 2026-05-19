-- =================================================================================
-- SCRIPT 02: CREACIÓN DE ÍNDICES - DMAN
-- =================================================================================
USE logistica_dman;

-- 1. ÍNDICES PARA CONSULTAS Y CALENDARIO (Operación)
-- Acelera la carga de la vista del calendario filtrando por fecha
CREATE INDEX idx_viajes_fecha ON VIAJES(fecha_hora_inicio);

-- 2. ÍNDICES COMPUESTOS PARA REPORTES (Historial por cliente)
-- Muy útil para el reporte de "Mis Viajes" en el perfil del cliente
CREATE INDEX idx_viajes_cliente_fecha ON VIAJES(id_cliente, fecha_hora_inicio);

-- 3. ÍNDICES PARA FILTRADO RÁPIDO (Disponibilidad de Flota y Finanzas)
-- Para encontrar rápido qué camionetas están 'Disponibles'
CREATE INDEX idx_camionetas_estado ON CAMIONETAS(estado);

-- Para reportes financieros de cuentas por cobrar (Pagos Pendientes)
CREATE INDEX idx_pagos_estatus ON PAGOS(estatus_pago);

-- 4. ÍNDICES PARA ARCHIVOS LOG / BITÁCORA
-- Para buscar movimientos específicos en un rango de fechas en la auditoría
CREATE INDEX idx_bitacora_fecha ON BITACORA(fecha_hora);
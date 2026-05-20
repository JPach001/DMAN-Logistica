-- =================================================================================
-- SCRIPT 06: VISTAS Y REPORTES GERENCIALES (Consultas complejas con JOINs)
-- =================================================================================
USE logistica_dman;

-- ---------------------------------------------------------------------------------
-- REPORTE 1: HISTORIAL DE VIAJES DETALLADO (Operación)
-- ---------------------------------------------------------------------------------
-- Muestra la información del viaje, el nombre del cliente y la camioneta asignada.
CREATE OR REPLACE VIEW VW_Reporte_Viajes_Completos AS
SELECT 
    v.id_viaje,
    v.fecha_hora_inicio AS fecha_viaje,
    u.nombre AS cliente,
    u.celular AS contacto_cliente,
    v.origen,
    v.destino,
    v.estado,
    c.placa AS camioneta_asignada,
    v.costo_total_estimado AS costo
FROM VIAJES v
INNER JOIN USUARIOS u ON v.id_cliente = u.id_usuario
LEFT JOIN VIAJE_CAMIONETAS vc ON v.id_viaje = vc.id_viaje
LEFT JOIN CAMIONETAS c ON vc.id_camioneta = c.id_camioneta
ORDER BY v.fecha_hora_inicio DESC;

-- ---------------------------------------------------------------------------------
-- REPORTE 2: CARTERA VENCIDA / PAGOS PENDIENTES (Finanzas)
-- ---------------------------------------------------------------------------------
-- Cruza los pagos pendientes con los datos del cliente para saber a quién cobrarle.
CREATE OR REPLACE VIEW VW_Reporte_Pagos_Pendientes AS
SELECT 
    p.id_pago,
    u.nombre AS cliente,
    u.correo,
    p.monto AS deuda,
    v.origen,
    v.destino,
    p.fecha_pago AS fecha_limite
FROM PAGOS p
INNER JOIN VIAJES v ON p.id_viaje = v.id_viaje
INNER JOIN USUARIOS u ON v.id_cliente = u.id_usuario
WHERE p.estatus_pago = 'Pendiente'
ORDER BY p.monto DESC;

-- ---------------------------------------------------------------------------------
-- REPORTE 3: DISPONIBILIDAD DE FLOTA (Logística)
-- ---------------------------------------------------------------------------------
-- Un resumen rápido para el despachador sobre qué camionetas pueden usarse hoy.
CREATE OR REPLACE VIEW VW_Reporte_Disponibilidad_Flota AS
SELECT 
    id_camioneta,
    placa,
    capacidad_m3,
    estado AS estatus_actual
FROM CAMIONETAS
ORDER BY estado ASC, capacidad_m3 DESC;

-- ---------------------------------------------------------------------------------
-- REPORTE 4: REPORTE DE AUDITORÍA Y TRAZABILIDAD (Seguridad)
-- ---------------------------------------------------------------------------------
-- Traduce los IDs de la bitácora a nombres reales de usuario para que 
-- el Administrador pueda leer el log fácilmente.
CREATE OR REPLACE VIEW VW_Reporte_Auditoria AS
SELECT 
    b.id_bitacora,
    b.fecha_hora,
    COALESCE(CONCAT(u.nombre, ' ', u.apellidos), 'Sistema/Anónimo') AS usuario_responsable,
    COALESCE(tu.nombre_rol, 'N/A') AS rol,
    b.tabla_afectada,
    b.accion AS detalle_movimiento
FROM BITACORA b
LEFT JOIN USUARIOS u ON b.id_usuario = u.id_usuario
LEFT JOIN TIPO_USUARIO tu ON u.id_tipo_usuario = tu.id_tipo_usuario
ORDER BY b.fecha_hora DESC;
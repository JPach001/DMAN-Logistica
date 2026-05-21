-- =================================================================================
-- SCRIPT 05: TRIGGERS Y AUDITORÍA AUTOMÁTICA (Bitácora Incorruptible)
-- =================================================================================
USE logistica_dman;

DELIMITER $$

-- ---------------------------------------------------------------------------------
-- 1. AUDITORÍA DE FLOTA (TABLA CAMIONETAS)
-- ---------------------------------------------------------------------------------
-- Detecta si alguien cambia el estado de una unidad (Ej. de 'Disponible' a 'Taller')
CREATE TRIGGER TRG_Auditoria_Update_Camionetas
AFTER UPDATE ON CAMIONETAS
FOR EACH ROW
BEGIN
    IF OLD.estado != NEW.estado THEN
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
        VALUES (
            1, 
            CONCAT('Alerta de Flota: Camioneta ID ', NEW.id_camioneta, ' cambió de estado [', OLD.estado, '] a [', NEW.estado, ']'), 
            'CAMIONETAS'
        );
    END IF;
END$$

-- ---------------------------------------------------------------------------------
-- 2. AUDITORÍA FINANCIERA (TABLA PAGOS)
-- ---------------------------------------------------------------------------------
CREATE TRIGGER TRG_Auditoria_Update_Pagos
AFTER UPDATE ON PAGOS
FOR EACH ROW
BEGIN
    -- 1. Declaramos una variable para guardar el ID del cliente
    DECLARE v_id_cliente INT;
    
    -- 2. Buscamos al dueño del viaje y lo guardamos en la variable
    SELECT id_cliente INTO v_id_cliente FROM VIAJES WHERE id_viaje = NEW.id_viaje;

    -- Cambio de estatus
    IF OLD.estatus_pago != NEW.estatus_pago THEN
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
        VALUES (
            v_id_cliente,
            CONCAT('Auditoría Financiera: El pago ID ', NEW.id_pago, ' cambió a estado: ', NEW.estatus_pago), 
            'PAGOS'
        );
    END IF;

    -- Alteración de monto (Posible fraude)
    IF OLD.monto != NEW.monto THEN
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
        VALUES (
            v_id_cliente, 
            CONCAT('ALERTA CRÍTICA: Se modificó el monto del pago ID ', NEW.id_pago, '. Original: $', OLD.monto, ' - Nuevo: $', NEW.monto), 
            'PAGOS'
        );
    END IF;
END$$

-- ---------------------------------------------------------------------------------
-- 3. AUDITORÍA DE SEGURIDAD (TABLA USUARIOS)
-- ---------------------------------------------------------------------------------
-- Detecta si alguien es promovido a Administrador o si le cambian la contraseña.
CREATE TRIGGER TRG_Auditoria_Update_Usuarios
AFTER UPDATE ON USUARIOS
FOR EACH ROW
BEGIN
    -- Cambio de Rol (Ej. un empleado se pone rol de admin)
    IF OLD.id_tipo_usuario != NEW.id_tipo_usuario THEN
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
        VALUES (
            NEW.id_usuario, 
            CONCAT('Alerta de Seguridad: El usuario ', NEW.correo, ' cambió de rol de [', OLD.id_tipo_usuario, '] a [', NEW.id_tipo_usuario, ']'), 
            'USUARIOS'
        );
    END IF;
END$$

-- ---------------------------------------------------------------------------------
-- 4. AUDITORÍA DE ELIMINACIONES FÍSICAS (TABLA PAGOS)
-- ---------------------------------------------------------------------------------
CREATE TRIGGER TRG_Auditoria_Delete_Pagos
BEFORE DELETE ON PAGOS
FOR EACH ROW
BEGIN
    -- Hacemos lo mismo: buscar de quién era el pago antes de que se borre
    DECLARE v_id_cliente INT;
    SELECT id_cliente INTO v_id_cliente FROM VIAJES WHERE id_viaje = OLD.id_viaje;

    INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
    VALUES (
        v_id_cliente, 
        CONCAT('PELIGRO: Eliminación física del pago ID ', OLD.id_pago, ' por valor de $', OLD.monto), 
        'PAGOS'
    );
END$$

-- ---------------------------------------------------------------------------------
-- 5. TRIGGER: AUDITORÍA DE ACTUALIZACIONES EN VIAJES (AFTER UPDATE)
-- ---------------------------------------------------------------------------------
-- Este trigger se dispara DESPUÉS de que cualquier persona o sistema 
-- haga un UPDATE en la tabla VIAJES.
CREATE TRIGGER TRG_Auditoria_Update_Viajes
AFTER UPDATE ON VIAJES
FOR EACH ROW
BEGIN
    -- 1. Auditoría de cambio de estado
    IF OLD.estado != NEW.estado THEN
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
        VALUES (
            NEW.id_cliente,
            CONCAT('Cambio de estado automático en viaje ID: ', NEW.id_viaje, '. De [', OLD.estado, '] a [', NEW.estado, ']'), 
            'VIAJES'
        );
    END IF;

    -- 2. Auditoría financiera: Detección de alteraciones de costo
    IF OLD.costo_total_estimado != NEW.costo_total_estimado THEN
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
        VALUES (
            NEW.id_cliente, 
            CONCAT('ALERTA: Modificación de costo en viaje ID: ', NEW.id_viaje, '. Anterior: $', OLD.costo_total_estimado, ' Nuevo: $', NEW.costo_total_estimado), 
            'VIAJES'
        );
    END IF;

    -- 3. Auditoría operativa: Cambio de destino a última hora
    IF OLD.destino != NEW.destino THEN
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
        VALUES (
            NEW.id_cliente, 
            CONCAT('Modificación de destino en viaje ID: ', NEW.id_viaje, '. Nuevo destino: ', NEW.destino), 
            'VIAJES'
        );
    END IF;

END$$

DELIMITER ;
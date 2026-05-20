-- =================================================================================
-- SCRIPT 04: PROCEDIMIENTOS ALMACENADOS Y TRANSACCIONES
-- =================================================================================
USE logistica_dman;

DELIMITER $$

-- ---------------------------------------------------------------------------------
-- PROCEDIMIENTO 1: REGISTRO DE INFORMACIÓN 
-- ---------------------------------------------------------------------------------
CREATE PROCEDURE SP_Registrar_Viaje(
    IN p_id_cliente INT,
    IN p_origen TEXT,
    IN p_destino TEXT,
    IN p_fecha_inicio DATETIME,
    IN p_duracion INT,
    IN p_distancia DECIMAL(10,2),
    IN p_volumen ENUM('Bajo', 'Medio', 'Alto'),
    IN p_costo_estimado DECIMAL(10,2)
)
BEGIN
    -- Declaración del manejador de errores: Si CUALQUIER error SQL ocurre, entra aquí.
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        -- Registrar en la bitácora que hubo un fallo
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada) 
        VALUES (p_id_cliente, 'ERROR: Fallo de sistema al intentar registrar un viaje. Se aplicó ROLLBACK.', 'VIAJES');
    END;

    -- INICIO DE TRANSACCIÓN EXPLÍCITA
    START TRANSACTION;

    -- 1. Inserción del viaje
    INSERT INTO VIAJES (id_cliente, origen, destino, fecha_hora_inicio, duracion_estimada_min, distancia_km, volumen_articulos, costo_total_estimado)
    VALUES (p_id_cliente, p_origen, p_destino, p_fecha_inicio, p_duracion, p_distancia, p_volumen, p_costo_estimado);

    -- 2. Recuperar el ID del viaje recién insertado
    SET @nuevo_id_viaje = LAST_INSERT_ID();

    -- 3. Registro en la Bitácora de Movimientos
    INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
    VALUES (p_id_cliente, CONCAT('Registro exitoso de nuevo viaje. ID: ', @nuevo_id_viaje), 'VIAJES');

    COMMIT;
END$$

-- ---------------------------------------------------------------------------------
-- PROCEDIMIENTO 2: BORRADO LÓGICO / CANCELACIÓN
-- ---------------------------------------------------------------------------------
-- Cambiamos el estado a "Cancelado" y dejamos un registro del motivo.
CREATE PROCEDURE SP_Cancelar_Viaje(
    IN p_id_viaje INT,
    IN p_id_usuario INT, -- El usuario (cliente o empleado) que lo cancela
    IN p_motivo_cancelacion TEXT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        INSERT INTO BITACORA (id_usuario, accion, tabla_afectada) 
        VALUES (p_id_usuario, CONCAT('ERROR: Fallo al intentar cancelar el viaje ID: ', p_id_viaje, '. Se aplicó ROLLBACK.'), 'VIAJES');
    END;

    START TRANSACTION;

    -- 1. Actualización (Borrado Lógico) asumiendo que tienes un campo de estado
    UPDATE VIAJES 
    SET estado_viaje = 'Cancelado' 
    WHERE id_viaje = p_id_viaje;

    -- 2. Registro de la acción en la Bitácora incluyendo el motivo
    INSERT INTO BITACORA (id_usuario, accion, tabla_afectada)
    VALUES (p_id_usuario, CONCAT('Viaje ID: ', p_id_viaje, ' cancelado. Motivo: ', p_motivo_cancelacion), 'VIAJES');

    COMMIT;
END$$
-- Devolvemos el delimitador a su estado normal
DELIMITER ;
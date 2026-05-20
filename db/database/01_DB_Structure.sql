-- =================================================================================
-- SCRIPT DE CREACIÓN DE BASE DE DATOS: SISTEMA DE MUDANZAS
-- =================================================================================

CREATE DATABASE IF NOT EXISTS logistica_dman;
USE logistica_dman;

-- -----------------------------------------------------
-- 1. CATÁLOGOS BASE Y CONFIGURACIÓN
-- -----------------------------------------------------

CREATE TABLE TIPO_USUARIO (
    id_tipo_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol ENUM('Admin', 'Empleado', 'Cliente') NOT NULL
);

CREATE TABLE CAMIONETAS (
    id_camioneta INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    placa VARCHAR(20) NOT NULL UNIQUE,
    capacidad_m3 DECIMAL(5,2) NOT NULL,
    estado ENUM('Disponible', 'Mantenimiento', 'Baja') DEFAULT 'Disponible'
);

CREATE TABLE CONFIG_COTIZADOR (
    id_config INT AUTO_INCREMENT PRIMARY KEY,
    precio_km DECIMAL(10,2) NOT NULL,
    precio_por_piso DECIMAL(10,2) NOT NULL,
    precio_por_emplaye DECIMAL(10,2) NOT NULL,
    precio_volumen_bajo DECIMAL(10,2) NOT NULL,
    precio_volumen_medio DECIMAL(10,2) NOT NULL,
    precio_volumen_alto DECIMAL(10,2) NOT NULL,
    sueldo_base_cargador DECIMAL(10,2) NOT NULL,
    sueldo_base_chofer DECIMAL(10,2) NOT NULL,
    ultima_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- -----------------------------------------------------
-- 2. USUARIOS Y EMPLEADOS
-- -----------------------------------------------------

CREATE TABLE USUARIOS (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_tipo_usuario INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    celular VARCHAR(20) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_tipo_usuario) REFERENCES TIPO_USUARIO(id_tipo_usuario) ON DELETE RESTRICT
);

-- Tabla extendida (relación 1:1 con USUARIOS donde el rol sea Empleado)
CREATE TABLE PERFIL_EMPLEADO (
    id_usuario INT PRIMARY KEY,
    habilidad ENUM('Chofer', 'Cargador', 'Ambos') NOT NULL,
    numero_licencia VARCHAR(50),
    estado ENUM('Activo', 'Vacaciones', 'Baja') DEFAULT 'Activo',
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario) ON DELETE CASCADE
);

-- -----------------------------------------------------
-- 3. MÓDULO OPERATIVO: VIAJES
-- -----------------------------------------------------

CREATE TABLE VIAJES (
    id_viaje INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    origen TEXT NOT NULL,
    destino TEXT NOT NULL,
    fecha_hora_inicio DATETIME NOT NULL,
    duracion_estimada_min INT NOT NULL,
    distancia_km DECIMAL(10,2) NOT NULL,
    requiere_factura BOOLEAN DEFAULT FALSE,
    anticipo_pagado BOOLEAN DEFAULT FALSE,
    estado ENUM('Cotizado', 'Confirmado', 'En curso', 'Terminado', 'Cancelado') DEFAULT 'Cotizado',
    volumen_articulos ENUM('Bajo', 'Medio', 'Alto') NOT NULL,
    costo_gasolina DECIMAL(10,2) DEFAULT 0.00,
    costo_casetas DECIMAL(10,2) DEFAULT 0.00,
    costos_adicionales DECIMAL(10,2) DEFAULT 0.00,
    costo_empleados DECIMAL(10,2) DEFAULT 0.00,
    costo_total_estimado DECIMAL(10,2) NOT NULL,
    costo_total_real DECIMAL(10,2) DEFAULT 0.00,
    FOREIGN KEY (id_cliente) REFERENCES USUARIOS(id_usuario) ON DELETE RESTRICT
);

-- -----------------------------------------------------
-- 4. TABLAS INTERMEDIAS (MUCHOS A MUCHOS)
-- -----------------------------------------------------

CREATE TABLE VIAJE_CAMIONETAS (
    id_viaje INT NOT NULL,
    id_camioneta INT NOT NULL,
    PRIMARY KEY (id_viaje, id_camioneta),
    FOREIGN KEY (id_viaje) REFERENCES VIAJES(id_viaje) ON DELETE CASCADE,
    FOREIGN KEY (id_camioneta) REFERENCES CAMIONETAS(id_camioneta) ON DELETE RESTRICT
);

CREATE TABLE VIAJE_EMPLEADOS (
    id_viaje INT NOT NULL,
    id_empleado INT NOT NULL,
    rol_en_este_viaje ENUM('Chofer', 'Cargador', 'Ambos') NOT NULL,
    PRIMARY KEY (id_viaje, id_empleado),
    FOREIGN KEY (id_viaje) REFERENCES VIAJES(id_viaje) ON DELETE CASCADE,
    FOREIGN KEY (id_empleado) REFERENCES PERFIL_EMPLEADO(id_usuario) ON DELETE RESTRICT
);

-- -----------------------------------------------------
-- 5. MÓDULO ADMINISTRATIVO Y FINANCIERO
-- -----------------------------------------------------

CREATE TABLE PAGOS (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_viaje INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('Efectivo', 'Transferencia') NOT NULL,
    referencia_bancaria VARCHAR(150) NULL, -- Permitir NULL porque el Efectivo no lleva referencia
    fecha_pago DATETIME NOT NULL,
    estatus_pago ENUM('Pendiente', 'Liquidado') DEFAULT 'Pendiente',
    FOREIGN KEY (id_viaje) REFERENCES VIAJES(id_viaje) ON DELETE CASCADE
);

CREATE TABLE BITACORA (
    id_bitacora INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    accion VARCHAR(255) NOT NULL,
    tabla_afectada VARCHAR(100) NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES USUARIOS(id_usuario) ON DELETE SET NULL
);
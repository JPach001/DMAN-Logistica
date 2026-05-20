-- =================================================================================
-- SCRIPT 03: DCL - ROLES Y SEGURIDAD
-- =================================================================================
USE logistica_dman;

-- ---------------------------------------------------------------------------------
-- 1. CREACIÓN DE ROLES TÉCNICOS
-- ---------------------------------------------------------------------------------
CREATE ROLE IF NOT EXISTS 'rol_app_backend', 'rol_admin_db';

-- ---------------------------------------------------------------------------------
-- 2. ASIGNACIÓN DE PERMISOS (GRANTS)
-- ---------------------------------------------------------------------------------
-- ROL BACKEND: El que usará el sitio web público. 
-- Puede leer, insertar y actualizar, pero no puede hacer DELETE.
GRANT SELECT, INSERT, UPDATE ON logistica_dman.* TO 'rol_app_backend';

-- ROL ADMIN: Tiene control total.
GRANT ALL PRIVILEGES ON logistica_dman.* TO 'rol_admin_db';

-- ---------------------------------------------------------------------------------
-- 3. CREACIÓN DE USUARIOS DE CONEXIÓN
-- ---------------------------------------------------------------------------------
-- Estos son los que irán en las variables del código PHP para conectar a la base de datos
CREATE USER IF NOT EXISTS 'user_web'@'localhost' IDENTIFIED BY 'DMAN_public2026!';
CREATE USER IF NOT EXISTS 'user_admin'@'localhost' IDENTIFIED BY 'DMAN_admin2026!';

-- ---------------------------------------------------------------------------------
-- 4. ASIGNAR ROLES A LOS USUARIOS DE CONEXIÓN
-- ---------------------------------------------------------------------------------
GRANT 'rol_app_backend' TO 'user_web'@'localhost';
GRANT 'rol_admin_db' TO 'user_admin'@'localhost';

-- Activamos los roles por defecto
SET DEFAULT ROLE ALL TO 'user_web'@'localhost', 'user_admin'@'localhost';

-- ---------------------------------------------------------------------------------
-- 5. REFRESCAR PRIVILEGIOS
-- ---------------------------------------------------------------------------------
FLUSH PRIVILEGES;
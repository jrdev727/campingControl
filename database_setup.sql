-- ============================================
-- Script de Base de Datos: Camping Sonrisas
-- Versión actualizada con todos los cambios
-- ============================================

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS camping_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE camping_db;

-- ============================================
-- Tabla: usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL COMMENT 'Se usa también como rol: "administrador" o "empleado"',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla: entradas
-- ============================================
CREATE TABLE IF NOT EXISTS entradas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_entrada VARCHAR(50) NOT NULL COMMENT 'Valores: turista_adulto, turista_niño, local',
    precio DECIMAL(10,2) NOT NULL,
    dni_cliente VARCHAR(20),
    usuario_id INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) DEFAULT 'activo' COMMENT 'Valores: activo, anulado',
    INDEX idx_fecha (fecha_hora),
    INDEX idx_estado (estado),
    INDEX idx_tipo (tipo_entrada),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Datos iniciales: Usuarios
-- ============================================
-- Nota: Contraseñas hasheadas con password_hash()
-- admin/admin123 (hasheada)
-- empleado/emp123 (hasheada)

INSERT INTO usuarios (usuario, password, nombre) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador'),
('empleado', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'empleado')
ON DUPLICATE KEY UPDATE usuario=usuario;

-- ============================================
-- Vistas útiles (opcional)
-- ============================================

-- Vista de entradas activas
CREATE OR REPLACE VIEW entradas_activas AS
SELECT
    e.id,
    e.tipo_entrada,
    e.precio,
    e.dni_cliente,
    e.fecha_hora,
    u.usuario as registrado_por
FROM entradas e
INNER JOIN usuarios u ON e.usuario_id = u.id
WHERE e.estado = 'activo';

-- Vista de estadísticas diarias
CREATE OR REPLACE VIEW estadisticas_diarias AS
SELECT
    DATE(fecha_hora) as fecha,
    COUNT(*) as total_entradas,
    SUM(precio) as ingresos_totales,
    AVG(precio) as ticket_promedio,
    SUM(CASE WHEN tipo_entrada = 'turista_adulto' THEN 1 ELSE 0 END) as turistas_adultos,
    SUM(CASE WHEN tipo_entrada = 'turista_niño' THEN 1 ELSE 0 END) as turistas_ninos,
    SUM(CASE WHEN tipo_entrada = 'local' THEN 1 ELSE 0 END) as locales
FROM entradas
WHERE estado = 'activo'
GROUP BY DATE(fecha_hora)
ORDER BY fecha DESC;

-- ============================================
-- Información de credenciales por defecto
-- ============================================
-- Usuario Administrador:
--   Usuario: admin
--   Contraseña: admin123
--
-- Usuario Empleado:
--   Usuario: empleado
--   Contraseña: emp123
--
-- IMPORTANTE: Cambia estas contraseñas en producción
-- ============================================

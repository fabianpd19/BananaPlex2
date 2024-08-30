-- Crear tablas si no existen
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE CHECK (nombre IN ('cliente', 'admin'))
);

CREATE TABLE IF NOT EXISTS provincias (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(255) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL REFERENCES roles(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL
);

CREATE TABLE IF NOT EXISTS empleados (
    id SERIAL PRIMARY KEY,
    usuario_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    nombre1 VARCHAR(100) NOT NULL,
    nombre2 VARCHAR(100),
    apellido1 VARCHAR(100) NOT NULL,
    apellido2 VARCHAR(100) NOT NULL,
    cedula VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    direccion VARCHAR(200) NOT NULL,
    provincia_id INT REFERENCES provincias(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS empresas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(255) UNIQUE
);

CREATE TABLE IF NOT EXISTS clientes (
    id SERIAL PRIMARY KEY,
    nombre1 VARCHAR(100) NOT NULL,
    nombre2 VARCHAR(100),
    apellido1 VARCHAR(100) NOT NULL,
    apellido2 VARCHAR(100) NOT NULL,
    direccion VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    cedula VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    empresa_id INT REFERENCES empresas(id) ON DELETE SET NULL,
    provincia_id INT REFERENCES provincias(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS inventario (
    id SERIAL PRIMARY KEY,
    producto_id INT NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    cantidad INT NOT NULL CHECK (cantidad >= 0)
);

CREATE TABLE IF NOT EXISTS transacciones (
    id SERIAL PRIMARY KEY,
    cliente_id INT NOT NULL REFERENCES clientes(id) ON DELETE CASCADE,
    usuario_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    producto_id INT NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    cantidad INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('compra', 'venta'))
);

CREATE TABLE IF NOT EXISTS solicitudes (
    id SERIAL PRIMARY KEY,
    cliente_id INT NOT NULL REFERENCES clientes(id) ON DELETE CASCADE,
    producto_id INT NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    cantidad INT NOT NULL,
    precio_ofrecido DECIMAL(10, 2) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'aprobado', 'rechazado')),
    tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('compra', 'venta')),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    empleado_id INT REFERENCES empleados(id) ON DELETE SET NULL
);

-- Vistas Materializadas
CREATE MATERIALIZED VIEW IF NOT EXISTS vista_empleados AS
SELECT e.id, e.usuario_id, u.nombre AS nombre_usuario, u.correo, u.rol_id, e.nombre1, e.nombre2, e.apellido1, e.apellido2, e.direccion, e.cedula, e.fecha_registro, p.nombre AS nombre_provincia
FROM empleados e
LEFT JOIN usuarios u ON e.usuario_id = u.id
LEFT JOIN provincias p ON e.provincia_id = p.id;

CREATE MATERIALIZED VIEW IF NOT EXISTS clientes_info AS
SELECT c.id, c.nombre1, c.nombre2, c.apellido1, c.apellido2, c.direccion, c.telefono, c.cedula, c.fecha_registro,
       e.nombre AS nombre_empresa, p.nombre AS nombre_provincia
FROM clientes c
LEFT JOIN empresas e ON c.empresa_id = e.id
LEFT JOIN provincias p ON p.id = c.provincia_id;

CREATE MATERIALIZED VIEW IF NOT EXISTS empresas_info AS
SELECT e.id, e.nombre AS nombre_empresa, e.direccion, e.telefono, e.email
FROM empresas e;

CREATE MATERIALIZED VIEW IF NOT EXISTS productos_info AS
SELECT p.id, p.nombre, p.descripcion, p.precio
FROM productos p;

CREATE MATERIALIZED VIEW IF NOT EXISTS solicitudes_pendientes AS
SELECT s.id, s.cantidad, s.precio_ofrecido, s.tipo,
       c.nombre1 AS cliente_nombre,
       p.nombre AS producto_nombre
FROM solicitudes s
JOIN clientes c ON s.cliente_id = c.id
JOIN productos p ON s.producto_id = p.id
WHERE s.estado = 'pendiente';

-- Funciones
CREATE OR REPLACE FUNCTION obtener_empleados()
RETURNS TABLE (
    id INT,
    usuario_id INT,
    nombre_usuario VARCHAR,
    correo VARCHAR,
    rol_id INT,
    nombre1 VARCHAR,
    nombre2 VARCHAR,
    apellido1 VARCHAR,
    apellido2 VARCHAR,
    direccion VARCHAR,
    cedula VARCHAR,
    fecha_registro TIMESTAMP,
    nombre_provincia VARCHAR
) AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW vista_empleados;
    RETURN QUERY SELECT * FROM vista_empleados;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION obtener_clientes_info()
RETURNS TABLE (
    id INT,
    nombre1 VARCHAR,
    nombre2 VARCHAR,
    apellido1 VARCHAR,
    apellido2 VARCHAR,
    direccion VARCHAR,
    telefono VARCHAR,
    cedula VARCHAR,
    fecha_registro TIMESTAMP,
    nombre_empresa VARCHAR,
    nombre_provincia VARCHAR
) AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW clientes_info;
    RETURN QUERY
    SELECT * FROM clientes_info;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION obtener_empresas_info()
RETURNS TABLE (
    id INT,
    nombre_empresa VARCHAR,
    direccion VARCHAR,
    telefono VARCHAR,
    email VARCHAR
) AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW empresas_info;
    RETURN QUERY
    SELECT * FROM empresas_info;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION obtener_productos_info()
RETURNS TABLE (
    id INT,
    nombre VARCHAR,
    descripcion TEXT,
    precio DECIMAL(10, 2)
) AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW productos_info;
    RETURN QUERY
    SELECT * FROM productos_info;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION obtener_solicitudes_pendientes()
RETURNS TABLE (
    id INT,
    cantidad INT,
    precio_ofrecido DECIMAL(10, 2),
    tipo VARCHAR,
    cliente_nombre VARCHAR,
    producto_nombre VARCHAR
) AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW solicitudes_pendientes;
    RETURN QUERY
    SELECT * FROM solicitudes_pendientes;
END;
$$ LANGUAGE plpgsql;

-- Nuevas funciones
CREATE OR REPLACE FUNCTION aceptar_solicitud(solicitud_id INT) RETURNS VOID AS $$
DECLARE
    solicitud RECORD;
    total DECIMAL;
BEGIN
    -- Obtener la solicitud
    SELECT * INTO solicitud FROM solicitudes WHERE id = solicitud_id AND estado = 'pendiente';

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Solicitud no encontrada o ya procesada';
    END IF;

    -- Calcular el total
    total := solicitud.cantidad * solicitud.precio_ofrecido;

    -- Insertar en transacciones
    INSERT INTO transacciones (cliente_id, usuario_id, producto_id, cantidad, total, tipo)
    VALUES (solicitud.cliente_id, solicitud.empleado_id, solicitud.producto_id, solicitud.cantidad, total, solicitud.tipo);

    -- Actualizar estado de la solicitud
    UPDATE solicitudes SET estado = 'aprobado' WHERE id = solicitud_id;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION rechazar_solicitud(solicitud_id INT) RETURNS VOID AS $$
BEGIN
    -- Actualizar estado de la solicitud
    UPDATE solicitudes SET estado = 'rechazado' WHERE id = solicitud_id AND estado = 'pendiente';

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Solicitud no encontrada o ya procesada';
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Función para obtener solicitudes con filtrado por estado
CREATE OR REPLACE FUNCTION obtener_solicitudes(estado_param TEXT)
RETURNS TABLE (
    id INT,
    cliente_nombre VARCHAR,
    producto_nombre VARCHAR,
    cantidad INT,
    precio_ofrecido DECIMAL(10, 2),
    tipo VARCHAR,
    estado VARCHAR
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        s.id,
        CAST(c.nombre1 || ' ' || COALESCE(c.nombre2, '') || ' ' || c.apellido1 || ' ' || c.apellido2 AS VARCHAR) AS cliente_nombre,
        p.nombre AS producto_nombre,
        s.cantidad,
        s.precio_ofrecido,
        s.tipo,
        s.estado
    FROM solicitudes s
    JOIN clientes c ON s.cliente_id = c.id
    JOIN productos p ON s.producto_id = p.id
    WHERE 
        estado_param IS NULL OR 
        estado_param = '' OR 
        s.estado = estado_param;
END;
$$ LANGUAGE plpgsql;

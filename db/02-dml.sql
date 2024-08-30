DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM roles) THEN
        INSERT INTO roles (nombre) VALUES
        ('cliente'),
        ('admin');
    END IF;
END $$;

-- Insertar datos en provincias si está vacío
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM provincias) THEN
        INSERT INTO provincias (nombre) VALUES
        ('Azuay'),
        ('Bolívar'),
        ('Cañar'),
        ('Carchi'),
        ('Chimborazo'),
        ('Cotopaxi'),
        ('El Oro'),
        ('Esmeraldas'),
        ('Galápagos'),
        ('Guayas'),
        ('Imbabura'),
        ('Loja'),
        ('Los Ríos'),
        ('Manabí'),
        ('Morona Santiago'),
        ('Napo'),
        ('Orellana'),
        ('Pastaza'),
        ('Pichincha'),
        ('Santa Elena'),
        ('Santo Domingo de los Tsáchilas'),
        ('Sucumbíos'),
        ('Tungurahua'),
        ('Zamora Chinchipe');
    END IF;
END $$;

-- Insertar datos en productos si está vacío
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM productos) THEN
        INSERT INTO productos (nombre, descripcion, precio) VALUES
        ('PLÁTANO VERDE', 'Plátano en estado verde, ideal para exportación.', 1.50),
        ('PLÁTANO MADURO', 'Plátano en estado maduro listo para consumo.', 2.00),
        ('PLÁTANO ORGÁNICO', 'Plátano cultivado orgánicamente, sin pesticidas ni químicos.', 2.50),
        ('PLÁTANO BABY', 'Plátano pequeño, ideal para mercados específicos.', 1.00),
        ('PLÁTANO FRITO', 'Plátano maduro frito, típico en muchos países.', 3.00);
    END IF;
END $$;

-- Insertar datos en usuarios si está vacío
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM usuarios) THEN
        INSERT INTO usuarios (nombre, correo, contraseña, rol_id) VALUES
        ('ADMIN PRINCIPAL', 'admin@example.com', 'adminpass', 2),
        ('JUAN PEREZ', 'juan.perez@example.com', 'pass123', 1),
        ('ANA GOMEZ', 'ana.gomez@example.com', 'pass123', 2),
        ('CARLOS RUIZ', 'carlos.ruiz@example.com', 'pass123', 1),
        ('MARIA SANCHEZ', 'maria.sanchez@example.com', 'pass123', 2);
    END IF;
END $$;

-- Insertar datos en empleados si está vacío
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM empleados) THEN
        INSERT INTO empleados (usuario_id, nombre1, nombre2, apellido1, apellido2, cedula, direccion, provincia_id)
        VALUES
        (1, 'PRUEBA_NOSE', 'MARIA', 'GOMEZ', 'GARCIA', '0102030405', 'Av. Siempre Viva 123', 1),
        (2, 'MARIA', 'LUISA', 'SANCHEZ', 'RAMIREZ', '0607080910', 'Calle Falsa 456', 2);
    END IF;
END $$;

-- Insertar datos en empresas si está vacío
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM empresas) THEN
        INSERT INTO empresas (nombre, direccion, telefono, email) VALUES
        ('EMPRESA A', 'Av. Tecnología 789', '022345678', 'contacto@empresaA.com'),
        ('EMPRESA B', 'Calle Innovación 321', '042345678', 'contacto@empresaB.com');
    END IF;
END $$;

-- Insertar datos en clientes si está vacío
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM clientes) THEN
        INSERT INTO clientes (nombre1, nombre2, apellido1, apellido2, direccion, telefono, cedula, empresa_id, provincia_id)
        VALUES
        ('LUIS', 'MARTIN', 'PEREZ', 'GOMEZ', 'Calle Principal 123', '0991234567', '1111111111', 1, 1),
        ('CARLA', 'VERONICA', 'RAMIREZ', 'LOPEZ', 'Av. Central 456', '0997654321', '2222222222', 2, 2);
    END IF;
END $$;
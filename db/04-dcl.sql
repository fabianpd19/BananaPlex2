-- Crea el usuario administrador
CREATE USER admin2 WITH PASSWORD 'admin_password';

-- Concede todos los privilegios al usuario administrador
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE empleados TO admin2;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE roles TO admin2;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE usuarios TO admin2;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE clientes TO admin2;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE transacciones TO admin2;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE solicitudes TO admin2;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE provincias TO admin2;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE empresas TO admin2;
GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE productos TO admin2;

GRANT USAGE, SELECT ON SEQUENCE empresas_id_seq TO admin2;
GRANT USAGE, SELECT ON SEQUENCE empleados_id_seq TO admin2;
GRANT USAGE, SELECT ON SEQUENCE usuarios_id_seq TO admin2;
GRANT USAGE, SELECT ON SEQUENCE clientes_id_seq TO admin2;
GRANT USAGE, SELECT ON SEQUENCE productos_id_seq TO admin2;
GRANT USAGE, SELECT ON SEQUENCE solicitudes_id_seq TO admin2;
GRANT USAGE, SELECT ON SEQUENCE transacciones_id_seq TO admin2;

GRANT SELECT ON vista_empleados TO admin2;
GRANT SELECT ON clientes_info TO admin2;
GRANT SELECT ON solicitudes_pendientes TO admin2;
GRANT SELECT ON empresas_info TO admin2;
GRANT SELECT ON productos_info TO admin2;

-- Crea el usuario empleado
CREATE USER empleado1 WITH PASSWORD 'empleado_password';

-- Concede privilegios de solo lectura y escritura al usuario empleado
GRANT CONNECT ON DATABASE "BananaPlex" TO empleado1;
GRANT USAGE ON SCHEMA public TO empleado1;
GRANT SELECT, INSERT, UPDATE ON ALL TABLES IN SCHEMA public TO empleado1;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT, INSERT, UPDATE ON TABLES TO empleado1;

-- Evita que el usuario empleado elimine registros
REVOKE DELETE ON ALL TABLES IN SCHEMA public FROM empleado1;
REVOKE INSERT ON TABLE empleados FROM empleado1;
REVOKE UPDATE ON TABLE empleados FROM empleado1;
REVOKE UPDATE ON TABLE empresas FROM empleado1;



-- Cambiar el propietario de cada vista materializada existente a admin2
ALTER MATERIALIZED VIEW vista_empleados OWNER TO admin2;
ALTER MATERIALIZED VIEW clientes_info OWNER TO admin2;
ALTER MATERIALIZED VIEW empresas_info OWNER TO admin2;
ALTER MATERIALIZED VIEW productos_info OWNER TO admin2;
ALTER MATERIALIZED VIEW solicitudes_pendientes OWNER TO admin2;


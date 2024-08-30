--
-- PostgreSQL database dump
--

-- Dumped from database version 16.4 (Debian 16.4-1.pgdg120+1)
-- Dumped by pg_dump version 16.4 (Debian 16.4-1.pgdg120+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: aceptar_solicitud(integer); Type: FUNCTION; Schema: public; Owner: Grupo3
--

CREATE FUNCTION public.aceptar_solicitud(solicitud_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.aceptar_solicitud(solicitud_id integer) OWNER TO "Grupo3";

--
-- Name: obtener_clientes_info(); Type: FUNCTION; Schema: public; Owner: Grupo3
--

CREATE FUNCTION public.obtener_clientes_info() RETURNS TABLE(id integer, nombre1 character varying, nombre2 character varying, apellido1 character varying, apellido2 character varying, direccion character varying, telefono character varying, cedula character varying, fecha_registro timestamp without time zone, nombre_empresa character varying, nombre_provincia character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW clientes_info;
    RETURN QUERY
    SELECT * FROM clientes_info;
END;
$$;


ALTER FUNCTION public.obtener_clientes_info() OWNER TO "Grupo3";

--
-- Name: obtener_empleados(); Type: FUNCTION; Schema: public; Owner: Grupo3
--

CREATE FUNCTION public.obtener_empleados() RETURNS TABLE(id integer, usuario_id integer, nombre_usuario character varying, correo character varying, rol_id integer, nombre1 character varying, nombre2 character varying, apellido1 character varying, apellido2 character varying, direccion character varying, cedula character varying, fecha_registro timestamp without time zone, nombre_provincia character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW vista_empleados;
    RETURN QUERY SELECT * FROM vista_empleados;
END;
$$;


ALTER FUNCTION public.obtener_empleados() OWNER TO "Grupo3";

--
-- Name: obtener_empresas_info(); Type: FUNCTION; Schema: public; Owner: Grupo3
--

CREATE FUNCTION public.obtener_empresas_info() RETURNS TABLE(id integer, nombre_empresa character varying, direccion character varying, telefono character varying, email character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW empresas_info;
    RETURN QUERY
    SELECT * FROM empresas_info;
END;
$$;


ALTER FUNCTION public.obtener_empresas_info() OWNER TO "Grupo3";

--
-- Name: obtener_productos_info(); Type: FUNCTION; Schema: public; Owner: Grupo3
--

CREATE FUNCTION public.obtener_productos_info() RETURNS TABLE(id integer, nombre character varying, descripcion text, precio numeric)
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW productos_info;
    RETURN QUERY
    SELECT * FROM productos_info;
END;
$$;


ALTER FUNCTION public.obtener_productos_info() OWNER TO "Grupo3";

--
-- Name: obtener_solicitudes(text); Type: FUNCTION; Schema: public; Owner: Grupo3
--

CREATE FUNCTION public.obtener_solicitudes(estado_param text) RETURNS TABLE(id integer, cliente_nombre character varying, producto_nombre character varying, cantidad integer, precio_ofrecido numeric, tipo character varying, estado character varying)
    LANGUAGE plpgsql
    AS $$
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
$$;


ALTER FUNCTION public.obtener_solicitudes(estado_param text) OWNER TO "Grupo3";

--
-- Name: obtener_solicitudes_pendientes(); Type: FUNCTION; Schema: public; Owner: Grupo3
--

CREATE FUNCTION public.obtener_solicitudes_pendientes() RETURNS TABLE(id integer, cantidad integer, precio_ofrecido numeric, tipo character varying, cliente_nombre character varying, producto_nombre character varying)
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Refrescar la vista materializada
    REFRESH MATERIALIZED VIEW solicitudes_pendientes;
    RETURN QUERY
    SELECT * FROM solicitudes_pendientes;
END;
$$;


ALTER FUNCTION public.obtener_solicitudes_pendientes() OWNER TO "Grupo3";

--
-- Name: rechazar_solicitud(integer); Type: FUNCTION; Schema: public; Owner: Grupo3
--

CREATE FUNCTION public.rechazar_solicitud(solicitud_id integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
    -- Actualizar estado de la solicitud
    UPDATE solicitudes SET estado = 'rechazado' WHERE id = solicitud_id AND estado = 'pendiente';

    IF NOT FOUND THEN
        RAISE EXCEPTION 'Solicitud no encontrada o ya procesada';
    END IF;
END;
$$;


ALTER FUNCTION public.rechazar_solicitud(solicitud_id integer) OWNER TO "Grupo3";

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: clientes; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.clientes (
    id integer NOT NULL,
    nombre1 character varying(100) NOT NULL,
    nombre2 character varying(100),
    apellido1 character varying(100) NOT NULL,
    apellido2 character varying(100) NOT NULL,
    direccion character varying(255) NOT NULL,
    telefono character varying(20),
    cedula character varying(20),
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    empresa_id integer,
    provincia_id integer
);


ALTER TABLE public.clientes OWNER TO "Grupo3";

--
-- Name: clientes_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.clientes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.clientes_id_seq OWNER TO "Grupo3";

--
-- Name: clientes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.clientes_id_seq OWNED BY public.clientes.id;


--
-- Name: empresas; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.empresas (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    direccion character varying(255) NOT NULL,
    telefono character varying(20),
    email character varying(255)
);


ALTER TABLE public.empresas OWNER TO "Grupo3";

--
-- Name: provincias; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.provincias (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL
);


ALTER TABLE public.provincias OWNER TO "Grupo3";

--
-- Name: clientes_info; Type: MATERIALIZED VIEW; Schema: public; Owner: admin2
--

CREATE MATERIALIZED VIEW public.clientes_info AS
 SELECT c.id,
    c.nombre1,
    c.nombre2,
    c.apellido1,
    c.apellido2,
    c.direccion,
    c.telefono,
    c.cedula,
    c.fecha_registro,
    e.nombre AS nombre_empresa,
    p.nombre AS nombre_provincia
   FROM ((public.clientes c
     LEFT JOIN public.empresas e ON ((c.empresa_id = e.id)))
     LEFT JOIN public.provincias p ON ((p.id = c.provincia_id)))
  WITH NO DATA;


ALTER MATERIALIZED VIEW public.clientes_info OWNER TO admin2;

--
-- Name: empleados; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.empleados (
    id integer NOT NULL,
    usuario_id integer NOT NULL,
    nombre1 character varying(100) NOT NULL,
    nombre2 character varying(100),
    apellido1 character varying(100) NOT NULL,
    apellido2 character varying(100) NOT NULL,
    cedula character varying(20),
    fecha_registro timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    direccion character varying(200) NOT NULL,
    provincia_id integer
);


ALTER TABLE public.empleados OWNER TO "Grupo3";

--
-- Name: empleados_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.empleados_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.empleados_id_seq OWNER TO "Grupo3";

--
-- Name: empleados_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.empleados_id_seq OWNED BY public.empleados.id;


--
-- Name: empresas_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.empresas_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.empresas_id_seq OWNER TO "Grupo3";

--
-- Name: empresas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.empresas_id_seq OWNED BY public.empresas.id;


--
-- Name: empresas_info; Type: MATERIALIZED VIEW; Schema: public; Owner: admin2
--

CREATE MATERIALIZED VIEW public.empresas_info AS
 SELECT id,
    nombre AS nombre_empresa,
    direccion,
    telefono,
    email
   FROM public.empresas e
  WITH NO DATA;


ALTER MATERIALIZED VIEW public.empresas_info OWNER TO admin2;

--
-- Name: inventario; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.inventario (
    id integer NOT NULL,
    producto_id integer NOT NULL,
    cantidad integer NOT NULL,
    CONSTRAINT inventario_cantidad_check CHECK ((cantidad >= 0))
);


ALTER TABLE public.inventario OWNER TO "Grupo3";

--
-- Name: inventario_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.inventario_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.inventario_id_seq OWNER TO "Grupo3";

--
-- Name: inventario_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.inventario_id_seq OWNED BY public.inventario.id;


--
-- Name: productos; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.productos (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    descripcion text,
    precio numeric(10,2) NOT NULL
);


ALTER TABLE public.productos OWNER TO "Grupo3";

--
-- Name: productos_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.productos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.productos_id_seq OWNER TO "Grupo3";

--
-- Name: productos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.productos_id_seq OWNED BY public.productos.id;


--
-- Name: productos_info; Type: MATERIALIZED VIEW; Schema: public; Owner: admin2
--

CREATE MATERIALIZED VIEW public.productos_info AS
 SELECT id,
    nombre,
    descripcion,
    precio
   FROM public.productos p
  WITH NO DATA;


ALTER MATERIALIZED VIEW public.productos_info OWNER TO admin2;

--
-- Name: provincias_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.provincias_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.provincias_id_seq OWNER TO "Grupo3";

--
-- Name: provincias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.provincias_id_seq OWNED BY public.provincias.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.roles (
    id integer NOT NULL,
    nombre character varying(20) NOT NULL,
    CONSTRAINT roles_nombre_check CHECK (((nombre)::text = ANY ((ARRAY['cliente'::character varying, 'admin'::character varying])::text[])))
);


ALTER TABLE public.roles OWNER TO "Grupo3";

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.roles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO "Grupo3";

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: solicitudes; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.solicitudes (
    id integer NOT NULL,
    cliente_id integer NOT NULL,
    producto_id integer NOT NULL,
    cantidad integer NOT NULL,
    precio_ofrecido numeric(10,2) NOT NULL,
    estado character varying(20) DEFAULT 'pendiente'::character varying NOT NULL,
    tipo character varying(20) NOT NULL,
    fecha timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    empleado_id integer,
    CONSTRAINT solicitudes_estado_check CHECK (((estado)::text = ANY ((ARRAY['pendiente'::character varying, 'aprobado'::character varying, 'rechazado'::character varying])::text[]))),
    CONSTRAINT solicitudes_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['compra'::character varying, 'venta'::character varying])::text[])))
);


ALTER TABLE public.solicitudes OWNER TO "Grupo3";

--
-- Name: solicitudes_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.solicitudes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.solicitudes_id_seq OWNER TO "Grupo3";

--
-- Name: solicitudes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.solicitudes_id_seq OWNED BY public.solicitudes.id;


--
-- Name: solicitudes_pendientes; Type: MATERIALIZED VIEW; Schema: public; Owner: admin2
--

CREATE MATERIALIZED VIEW public.solicitudes_pendientes AS
 SELECT s.id,
    s.cantidad,
    s.precio_ofrecido,
    s.tipo,
    c.nombre1 AS cliente_nombre,
    p.nombre AS producto_nombre
   FROM ((public.solicitudes s
     JOIN public.clientes c ON ((s.cliente_id = c.id)))
     JOIN public.productos p ON ((s.producto_id = p.id)))
  WHERE ((s.estado)::text = 'pendiente'::text)
  WITH NO DATA;


ALTER MATERIALIZED VIEW public.solicitudes_pendientes OWNER TO admin2;

--
-- Name: transacciones; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.transacciones (
    id integer NOT NULL,
    cliente_id integer NOT NULL,
    usuario_id integer NOT NULL,
    producto_id integer NOT NULL,
    cantidad integer NOT NULL,
    total numeric(10,2) NOT NULL,
    fecha timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    tipo character varying(20) NOT NULL,
    CONSTRAINT transacciones_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['compra'::character varying, 'venta'::character varying])::text[])))
);


ALTER TABLE public.transacciones OWNER TO "Grupo3";

--
-- Name: transacciones_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.transacciones_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.transacciones_id_seq OWNER TO "Grupo3";

--
-- Name: transacciones_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.transacciones_id_seq OWNED BY public.transacciones.id;


--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: Grupo3
--

CREATE TABLE public.usuarios (
    id integer NOT NULL,
    nombre character varying(100) NOT NULL,
    correo character varying(255) NOT NULL,
    "contraseña" character varying(255) NOT NULL,
    rol_id integer NOT NULL
);


ALTER TABLE public.usuarios OWNER TO "Grupo3";

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: public; Owner: Grupo3
--

CREATE SEQUENCE public.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.usuarios_id_seq OWNER TO "Grupo3";

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: Grupo3
--

ALTER SEQUENCE public.usuarios_id_seq OWNED BY public.usuarios.id;


--
-- Name: vista_empleados; Type: MATERIALIZED VIEW; Schema: public; Owner: admin2
--

CREATE MATERIALIZED VIEW public.vista_empleados AS
 SELECT e.id,
    e.usuario_id,
    u.nombre AS nombre_usuario,
    u.correo,
    u.rol_id,
    e.nombre1,
    e.nombre2,
    e.apellido1,
    e.apellido2,
    e.direccion,
    e.cedula,
    e.fecha_registro,
    p.nombre AS nombre_provincia
   FROM ((public.empleados e
     LEFT JOIN public.usuarios u ON ((e.usuario_id = u.id)))
     LEFT JOIN public.provincias p ON ((e.provincia_id = p.id)))
  WITH NO DATA;


ALTER MATERIALIZED VIEW public.vista_empleados OWNER TO admin2;

--
-- Name: clientes id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.clientes ALTER COLUMN id SET DEFAULT nextval('public.clientes_id_seq'::regclass);


--
-- Name: empleados id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.empleados ALTER COLUMN id SET DEFAULT nextval('public.empleados_id_seq'::regclass);


--
-- Name: empresas id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.empresas ALTER COLUMN id SET DEFAULT nextval('public.empresas_id_seq'::regclass);


--
-- Name: inventario id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.inventario ALTER COLUMN id SET DEFAULT nextval('public.inventario_id_seq'::regclass);


--
-- Name: productos id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.productos ALTER COLUMN id SET DEFAULT nextval('public.productos_id_seq'::regclass);


--
-- Name: provincias id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.provincias ALTER COLUMN id SET DEFAULT nextval('public.provincias_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: solicitudes id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.solicitudes ALTER COLUMN id SET DEFAULT nextval('public.solicitudes_id_seq'::regclass);


--
-- Name: transacciones id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.transacciones ALTER COLUMN id SET DEFAULT nextval('public.transacciones_id_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.usuarios ALTER COLUMN id SET DEFAULT nextval('public.usuarios_id_seq'::regclass);


--
-- Data for Name: clientes; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.clientes (id, nombre1, nombre2, apellido1, apellido2, direccion, telefono, cedula, fecha_registro, empresa_id, provincia_id) FROM stdin;
1	LUIS	MARTIN	PEREZ	GOMEZ	Calle Principal 123	0991234567	1111111111	2024-08-30 03:16:35.066886	1	1
2	CARLA	VERONICA	RAMIREZ	LOPEZ	Av. Central 456	0997654321	2222222222	2024-08-30 03:16:35.066886	2	2
\.


--
-- Data for Name: empleados; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.empleados (id, usuario_id, nombre1, nombre2, apellido1, apellido2, cedula, fecha_registro, direccion, provincia_id) FROM stdin;
1	1	PRUEBA_NOSE	MARIA	GOMEZ	GARCIA	0102030405	2024-08-30 03:16:35.052478	Av. Siempre Viva 123	1
2	2	MARIA	LUISA	SANCHEZ	RAMIREZ	0607080910	2024-08-30 03:16:35.052478	Calle Falsa 456	2
\.


--
-- Data for Name: empresas; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.empresas (id, nombre, direccion, telefono, email) FROM stdin;
1	EMPRESA A	Av. Tecnología 789	022345678	contacto@empresaA.com
2	EMPRESA B	Calle Innovación 321	042345678	contacto@empresaB.com
\.


--
-- Data for Name: inventario; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.inventario (id, producto_id, cantidad) FROM stdin;
\.


--
-- Data for Name: productos; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.productos (id, nombre, descripcion, precio) FROM stdin;
1	PLÁTANO VERDE	Plátano en estado verde, ideal para exportación.	1.50
2	PLÁTANO MADURO	Plátano en estado maduro listo para consumo.	2.00
3	PLÁTANO ORGÁNICO	Plátano cultivado orgánicamente, sin pesticidas ni químicos.	2.50
4	PLÁTANO BABY	Plátano pequeño, ideal para mercados específicos.	1.00
5	PLÁTANO FRITO	Plátano maduro frito, típico en muchos países.	3.00
\.


--
-- Data for Name: provincias; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.provincias (id, nombre) FROM stdin;
1	Azuay
2	Bolívar
3	Cañar
4	Carchi
5	Chimborazo
6	Cotopaxi
7	El Oro
8	Esmeraldas
9	Galápagos
10	Guayas
11	Imbabura
12	Loja
13	Los Ríos
14	Manabí
15	Morona Santiago
16	Napo
17	Orellana
18	Pastaza
19	Pichincha
20	Santa Elena
21	Santo Domingo de los Tsáchilas
22	Sucumbíos
23	Tungurahua
24	Zamora Chinchipe
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.roles (id, nombre) FROM stdin;
1	cliente
2	admin
\.


--
-- Data for Name: solicitudes; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.solicitudes (id, cliente_id, producto_id, cantidad, precio_ofrecido, estado, tipo, fecha, empleado_id) FROM stdin;
\.


--
-- Data for Name: transacciones; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.transacciones (id, cliente_id, usuario_id, producto_id, cantidad, total, fecha, tipo) FROM stdin;
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: Grupo3
--

COPY public.usuarios (id, nombre, correo, "contraseña", rol_id) FROM stdin;
1	ADMIN PRINCIPAL	admin@example.com	adminpass	2
2	JUAN PEREZ	juan.perez@example.com	pass123	1
3	ANA GOMEZ	ana.gomez@example.com	pass123	2
4	CARLOS RUIZ	carlos.ruiz@example.com	pass123	1
5	MARIA SANCHEZ	maria.sanchez@example.com	pass123	2
\.


--
-- Name: clientes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.clientes_id_seq', 2, true);


--
-- Name: empleados_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.empleados_id_seq', 2, true);


--
-- Name: empresas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.empresas_id_seq', 2, true);


--
-- Name: inventario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.inventario_id_seq', 1, false);


--
-- Name: productos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.productos_id_seq', 5, true);


--
-- Name: provincias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.provincias_id_seq', 24, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.roles_id_seq', 2, true);


--
-- Name: solicitudes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.solicitudes_id_seq', 1, false);


--
-- Name: transacciones_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.transacciones_id_seq', 1, false);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: public; Owner: Grupo3
--

SELECT pg_catalog.setval('public.usuarios_id_seq', 5, true);


--
-- Name: clientes clientes_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_pkey PRIMARY KEY (id);


--
-- Name: empleados empleados_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.empleados
    ADD CONSTRAINT empleados_pkey PRIMARY KEY (id);


--
-- Name: empresas empresas_email_key; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.empresas
    ADD CONSTRAINT empresas_email_key UNIQUE (email);


--
-- Name: empresas empresas_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.empresas
    ADD CONSTRAINT empresas_pkey PRIMARY KEY (id);


--
-- Name: inventario inventario_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_pkey PRIMARY KEY (id);


--
-- Name: productos productos_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_pkey PRIMARY KEY (id);


--
-- Name: provincias provincias_nombre_key; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.provincias
    ADD CONSTRAINT provincias_nombre_key UNIQUE (nombre);


--
-- Name: provincias provincias_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.provincias
    ADD CONSTRAINT provincias_pkey PRIMARY KEY (id);


--
-- Name: roles roles_nombre_key; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_nombre_key UNIQUE (nombre);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: solicitudes solicitudes_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.solicitudes
    ADD CONSTRAINT solicitudes_pkey PRIMARY KEY (id);


--
-- Name: transacciones transacciones_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.transacciones
    ADD CONSTRAINT transacciones_pkey PRIMARY KEY (id);


--
-- Name: usuarios usuarios_correo_key; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_correo_key UNIQUE (correo);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: clientes clientes_empresa_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_empresa_id_fkey FOREIGN KEY (empresa_id) REFERENCES public.empresas(id) ON DELETE SET NULL;


--
-- Name: clientes clientes_provincia_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.clientes
    ADD CONSTRAINT clientes_provincia_id_fkey FOREIGN KEY (provincia_id) REFERENCES public.provincias(id) ON DELETE SET NULL;


--
-- Name: empleados empleados_provincia_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.empleados
    ADD CONSTRAINT empleados_provincia_id_fkey FOREIGN KEY (provincia_id) REFERENCES public.provincias(id) ON DELETE SET NULL;


--
-- Name: empleados empleados_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.empleados
    ADD CONSTRAINT empleados_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: inventario inventario_producto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.inventario
    ADD CONSTRAINT inventario_producto_id_fkey FOREIGN KEY (producto_id) REFERENCES public.productos(id) ON DELETE CASCADE;


--
-- Name: solicitudes solicitudes_cliente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.solicitudes
    ADD CONSTRAINT solicitudes_cliente_id_fkey FOREIGN KEY (cliente_id) REFERENCES public.clientes(id) ON DELETE CASCADE;


--
-- Name: solicitudes solicitudes_empleado_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.solicitudes
    ADD CONSTRAINT solicitudes_empleado_id_fkey FOREIGN KEY (empleado_id) REFERENCES public.empleados(id) ON DELETE SET NULL;


--
-- Name: solicitudes solicitudes_producto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.solicitudes
    ADD CONSTRAINT solicitudes_producto_id_fkey FOREIGN KEY (producto_id) REFERENCES public.productos(id) ON DELETE CASCADE;


--
-- Name: transacciones transacciones_cliente_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.transacciones
    ADD CONSTRAINT transacciones_cliente_id_fkey FOREIGN KEY (cliente_id) REFERENCES public.clientes(id) ON DELETE CASCADE;


--
-- Name: transacciones transacciones_producto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.transacciones
    ADD CONSTRAINT transacciones_producto_id_fkey FOREIGN KEY (producto_id) REFERENCES public.productos(id) ON DELETE CASCADE;


--
-- Name: transacciones transacciones_usuario_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.transacciones
    ADD CONSTRAINT transacciones_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES public.usuarios(id) ON DELETE CASCADE;


--
-- Name: usuarios usuarios_rol_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: Grupo3
--

ALTER TABLE ONLY public.usuarios
    ADD CONSTRAINT usuarios_rol_id_fkey FOREIGN KEY (rol_id) REFERENCES public.roles(id) ON DELETE RESTRICT;


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: pg_database_owner
--

GRANT USAGE ON SCHEMA public TO empleado1;


--
-- Name: TABLE clientes; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.clientes TO admin2;
GRANT SELECT,INSERT,UPDATE ON TABLE public.clientes TO empleado1;


--
-- Name: SEQUENCE clientes_id_seq; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,USAGE ON SEQUENCE public.clientes_id_seq TO admin2;


--
-- Name: TABLE empresas; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.empresas TO admin2;
GRANT SELECT,INSERT ON TABLE public.empresas TO empleado1;


--
-- Name: TABLE provincias; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.provincias TO admin2;
GRANT SELECT,INSERT,UPDATE ON TABLE public.provincias TO empleado1;


--
-- Name: TABLE clientes_info; Type: ACL; Schema: public; Owner: admin2
--

GRANT SELECT,INSERT,UPDATE ON TABLE public.clientes_info TO empleado1;


--
-- Name: TABLE empleados; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.empleados TO admin2;
GRANT SELECT ON TABLE public.empleados TO empleado1;


--
-- Name: SEQUENCE empleados_id_seq; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,USAGE ON SEQUENCE public.empleados_id_seq TO admin2;


--
-- Name: SEQUENCE empresas_id_seq; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,USAGE ON SEQUENCE public.empresas_id_seq TO admin2;


--
-- Name: TABLE empresas_info; Type: ACL; Schema: public; Owner: admin2
--

GRANT SELECT,INSERT,UPDATE ON TABLE public.empresas_info TO empleado1;


--
-- Name: TABLE inventario; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,UPDATE ON TABLE public.inventario TO empleado1;


--
-- Name: TABLE productos; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.productos TO admin2;
GRANT SELECT,INSERT,UPDATE ON TABLE public.productos TO empleado1;


--
-- Name: SEQUENCE productos_id_seq; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,USAGE ON SEQUENCE public.productos_id_seq TO admin2;


--
-- Name: TABLE productos_info; Type: ACL; Schema: public; Owner: admin2
--

GRANT SELECT,INSERT,UPDATE ON TABLE public.productos_info TO empleado1;


--
-- Name: TABLE roles; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.roles TO admin2;
GRANT SELECT,INSERT,UPDATE ON TABLE public.roles TO empleado1;


--
-- Name: TABLE solicitudes; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.solicitudes TO admin2;
GRANT SELECT,INSERT,UPDATE ON TABLE public.solicitudes TO empleado1;


--
-- Name: SEQUENCE solicitudes_id_seq; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,USAGE ON SEQUENCE public.solicitudes_id_seq TO admin2;


--
-- Name: TABLE solicitudes_pendientes; Type: ACL; Schema: public; Owner: admin2
--

GRANT SELECT,INSERT,UPDATE ON TABLE public.solicitudes_pendientes TO empleado1;


--
-- Name: TABLE transacciones; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.transacciones TO admin2;
GRANT SELECT,INSERT,UPDATE ON TABLE public.transacciones TO empleado1;


--
-- Name: SEQUENCE transacciones_id_seq; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,USAGE ON SEQUENCE public.transacciones_id_seq TO admin2;


--
-- Name: TABLE usuarios; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.usuarios TO admin2;
GRANT SELECT,INSERT,UPDATE ON TABLE public.usuarios TO empleado1;


--
-- Name: SEQUENCE usuarios_id_seq; Type: ACL; Schema: public; Owner: Grupo3
--

GRANT SELECT,USAGE ON SEQUENCE public.usuarios_id_seq TO admin2;


--
-- Name: TABLE vista_empleados; Type: ACL; Schema: public; Owner: admin2
--

GRANT SELECT,INSERT,UPDATE ON TABLE public.vista_empleados TO empleado1;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: Grupo3
--

ALTER DEFAULT PRIVILEGES FOR ROLE "Grupo3" IN SCHEMA public GRANT SELECT,INSERT,UPDATE ON TABLES TO empleado1;


--
-- Name: clientes_info; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: admin2
--

REFRESH MATERIALIZED VIEW public.clientes_info;


--
-- Name: empresas_info; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: admin2
--

REFRESH MATERIALIZED VIEW public.empresas_info;


--
-- Name: productos_info; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: admin2
--

REFRESH MATERIALIZED VIEW public.productos_info;


--
-- Name: solicitudes_pendientes; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: admin2
--

REFRESH MATERIALIZED VIEW public.solicitudes_pendientes;


--
-- Name: vista_empleados; Type: MATERIALIZED VIEW DATA; Schema: public; Owner: admin2
--

REFRESH MATERIALIZED VIEW public.vista_empleados;


--
-- PostgreSQL database dump complete
--


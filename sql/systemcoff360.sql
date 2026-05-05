-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-03-2026 a las 17:18:16
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `systemcoff360`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_lote`
--

CREATE TABLE `actividad_lote` (
  `id_actividad` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `descripcion` text DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `proxima_fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activo`
--

CREATE TABLE `activo` (
  `id_activo` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `estado` varchar(20) NOT NULL DEFAULT 'bueno',
  `fecha_registro` date NOT NULL,
  `valor` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aplicacion_insumo`
--

CREATE TABLE `aplicacion_insumo` (
  `id_aplicacion` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cantidad_aplicada` decimal(10,2) NOT NULL,
  `unidad` varchar(20) NOT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignacion_tarea`
--

CREATE TABLE `asignacion_tarea` (
  `id_asignacion` int(11) NOT NULL,
  `id_tarea` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria_activo`
--

CREATE TABLE `categoria_activo` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria_activo`
--

INSERT INTO `categoria_activo` (`id_categoria`, `nombre`, `descripcion`) VALUES
(1, 'Herramientas manuales', 'Palas, machetes, tijeras de poda'),
(2, 'Equipos de fumigación', 'Bombas de espalda, atomizadores'),
(3, 'Equipos de transporte', 'Carretillas, costales'),
(4, 'Infraestructura', 'Beneficiadero, bodegas, tanques'),
(5, 'Equipos eléctricos', 'Motobombas, descerezadoras');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `certificacion`
--

CREATE TABLE `certificacion` (
  `id_certificacion` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `entidad_certif` varchar(150) NOT NULL,
  `fecha_expedicion` date NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `documento` varchar(300) DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'vigente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `certificacion`
--

INSERT INTO `certificacion` (`id_certificacion`, `nombre`, `entidad_certif`, `fecha_expedicion`, `fecha_vencimiento`, `documento`, `estado`) VALUES
(1, 'Buenas Prácticas Agrícolas (BPA)', 'ICA', '2024-03-15', '2026-03-15', NULL, 'vigente'),
(2, 'Registro caficultor activo', 'Federación Nacional de Cafeteros', '2024-01-10', '2025-12-31', NULL, 'vigente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `id_compra` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cosecha`
--

CREATE TABLE `cosecha` (
  `id_cosecha` int(11) NOT NULL,
  `id_lote` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cantidad_kg` decimal(10,2) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'recolectada',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id_detalle` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrega_epp`
--

CREATE TABLE `entrega_epp` (
  `id_entrega` int(11) NOT NULL,
  `id_epp` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_entrega` date NOT NULL,
  `estado_elemento` varchar(20) NOT NULL DEFAULT 'bueno',
  `fecha_devolucion` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entrega_herramienta`
--

CREATE TABLE `entrega_herramienta` (
  `id_entrega` int(11) NOT NULL,
  `id_herramienta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_entrega` date NOT NULL,
  `estado_herramienta` varchar(20) NOT NULL DEFAULT 'bueno',
  `fecha_devolucion` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `epp`
--

CREATE TABLE `epp` (
  `id_epp` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad_total` int(11) NOT NULL DEFAULT 0,
  `stock_disponible` int(11) NOT NULL DEFAULT 0,
  `talla` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `epp`
--

INSERT INTO `epp` (`id_epp`, `nombre`, `descripcion`, `cantidad_total`, `stock_disponible`, `talla`) VALUES
(1, 'Botas de caucho T-42', 'Botas impermeables para campo', 6, 5, NULL),
(2, 'Guantes de nitrilo', 'Para manejo de agroquímicos', 20, 18, NULL),
(3, 'Mascarilla respiradora N95', 'Para fumigación', 10, 8, NULL),
(4, 'Overol azul talla L', 'Overol manga larga', 5, 4, NULL),
(5, 'Gafas de seguridad', 'Protección ocular', 8, 7, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencia_tarea`
--

CREATE TABLE `evidencia_tarea` (
  `id_evidencia` int(11) NOT NULL,
  `id_tarea` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `archivo` varchar(300) NOT NULL,
  `tipo_archivo` varchar(20) NOT NULL DEFAULT 'imagen',
  `fecha_subida` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `herramienta`
--

CREATE TABLE `herramienta` (
  `id_herramienta` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'disponible',
  `fecha_registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `herramienta`
--

INSERT INTO `herramienta` (`id_herramienta`, `nombre`, `descripcion`, `estado`, `fecha_registro`) VALUES
(1, 'Machete largo 24\"', 'Para limpieza de maleza', 'disponible', '2026-03-04'),
(2, 'Pala de punta', 'Para siembra', 'disponible', '2026-03-04'),
(3, 'Tijeras de poda', 'Para poda de café', 'disponible', '2026-03-04'),
(4, 'Bomba de espalda 20L', 'Para fumigación manual', 'disponible', '2026-03-04'),
(5, 'Carretilla metálica', 'Para transporte de insumos', 'disponible', '2026-03-04'),
(6, 'Azadón de plateo', 'Para plateo y aporque', 'disponible', '2026-03-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumo`
--

CREATE TABLE `insumo` (
  `id_insumo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `unidad` varchar(20) NOT NULL,
  `stock_actual` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT 5.00,
  `precio_unidad` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `insumo`
--

INSERT INTO `insumo` (`id_insumo`, `nombre`, `tipo`, `unidad`, `stock_actual`, `stock_minimo`, `precio_unidad`) VALUES
(1, 'Urea 46%', 'fertilizante', 'kg', 80.00, 20.00, 2500.00),
(2, 'Triple 15 (N-P-K)', 'fertilizante', 'kg', 60.00, 15.00, 3200.00),
(3, 'Roundup glifosato', 'herbicida', 'L', 12.00, 5.00, 18500.00),
(4, 'Curzate M-8', 'fungicida', 'kg', 8.00, 3.00, 22000.00),
(5, 'Lorsban 4E', 'pesticida', 'L', 5.00, 2.00, 28000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lote`
--

CREATE TABLE `lote` (
  `id_lote` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `ubicacion` text DEFAULT NULL,
  `tipo_plantacion` varchar(80) NOT NULL,
  `area_hectareas` decimal(8,2) DEFAULT NULL,
  `estado` varchar(10) NOT NULL DEFAULT 'activo',
  `fecha_registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lote`
--

INSERT INTO `lote` (`id_lote`, `nombre`, `ubicacion`, `tipo_plantacion`, `area_hectareas`, `estado`, `fecha_registro`) VALUES
(1, 'Lote El Mirador', 'Sector norte', 'Café arábica variedad Castillo', 2.50, 'activo', '2026-03-04'),
(2, 'Lote La Primavera', 'Sector sur', 'Café arábica variedad Colombia', 1.80, 'activo', '2026-03-04'),
(3, 'Lote El Naranjo', 'Planada central', 'Plátano dominico hartón', 0.90, 'activo', '2026-03-04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mantenimiento`
--

CREATE TABLE `mantenimiento` (
  `id_mantenimiento` int(11) NOT NULL,
  `id_activo` int(11) NOT NULL,
  `id_responsable` int(11) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'preventivo',
  `fecha` date NOT NULL,
  `descripcion` text NOT NULL,
  `costo` decimal(10,2) NOT NULL DEFAULT 0.00,
  `proxima_fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id_notificacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'general',
  `estado` varchar(10) NOT NULL DEFAULT 'no_leida',
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago_nomina`
--

CREATE TABLE `pago_nomina` (
  `id_pago` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_registrado_por` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `tipo_pago` varchar(20) NOT NULL DEFAULT 'jornal',
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `NIT` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `producto_servicio` text DEFAULT NULL,
  `estado` varchar(10) NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre`, `NIT`, `telefono`, `correo`, `producto_servicio`, `estado`) VALUES
(1, 'Agro La Cosecha Ltda.', '890123456-1', '3205678901', 'ventas@agrolacosecha.com', 'Fertilizantes y pesticidas', 'activo'),
(2, 'Ferretería El Constructor', '900234567-2', '3214567890', 'info@elconstructor.com', 'Herramientas manuales', 'activo'),
(3, 'Almacén El Caficultor', '800345678-3', '3185678901', 'compras@elcaficultor.com', 'EPP y dotación', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_clima`
--

CREATE TABLE `registro_clima` (
  `id_clima` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `lluvia_mm` decimal(6,2) NOT NULL DEFAULT 0.00,
  `temp_min` decimal(5,2) DEFAULT NULL,
  `temp_max` decimal(5,2) DEFAULT NULL,
  `humedad_pct` decimal(5,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'Administrador', 'Acceso total al sistema'),
(2, 'Trabajador', 'Acceso limitado a tareas asignadas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE `tarea` (
  `id_tarea` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `prioridad` varchar(10) NOT NULL DEFAULT 'media',
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_limite` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `DNI` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `estado` varchar(10) NOT NULL DEFAULT 'activo',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `intentos_fallidos` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `id_rol`, `nombre`, `DNI`, `telefono`, `correo`, `contrasena`, `estado`, `fecha_registro`, `intentos_fallidos`) VALUES
(1, 1, 'Juan David Lizcano Díaz', '1060123456', '3112345678', 'admin@systemcoff.com', '$2b$12$hashAdmin123', 'activo', '2026-03-04 11:51:43', 0),
(2, 2, 'Carlos Pérez Torres', '1060654321', '3124567890', 'carlos@systemcoff.com', '$2b$12$hashTrabajador456', 'activo', '2026-03-04 11:51:43', 0),
(3, 2, 'María Gómez Vargas', '1060789012', '3136789012', 'maria@systemcoff.com', '$2b$12$hashTrabajador789', 'activo', '2026-03-04 11:51:43', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id_venta` int(11) NOT NULL,
  `id_cosecha` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `cantidad_kg` decimal(10,2) NOT NULL,
  `precio_kg` decimal(10,2) NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `comprador` varchar(150) NOT NULL,
  `tipo_cafe` varchar(20) NOT NULL DEFAULT 'pergamino',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad_lote`
--
ALTER TABLE `actividad_lote`
  ADD PRIMARY KEY (`id_actividad`),
  ADD KEY `fk_actlot_usuario` (`id_responsable`),
  ADD KEY `idx_actlote_lote` (`id_lote`);

--
-- Indices de la tabla `activo`
--
ALTER TABLE `activo`
  ADD PRIMARY KEY (`id_activo`),
  ADD KEY `idx_activo_categoria` (`id_categoria`);

--
-- Indices de la tabla `aplicacion_insumo`
--
ALTER TABLE `aplicacion_insumo`
  ADD PRIMARY KEY (`id_aplicacion`),
  ADD KEY `fk_aplinsumo_resp` (`id_responsable`),
  ADD KEY `idx_aplins_lote` (`id_lote`),
  ADD KEY `idx_aplins_insumo` (`id_insumo`);

--
-- Indices de la tabla `asignacion_tarea`
--
ALTER TABLE `asignacion_tarea`
  ADD PRIMARY KEY (`id_asignacion`),
  ADD UNIQUE KEY `uq_asig_tarea_usr` (`id_tarea`,`id_usuario`),
  ADD KEY `idx_asig_tarea` (`id_tarea`),
  ADD KEY `idx_asig_usuario` (`id_usuario`);

--
-- Indices de la tabla `categoria_activo`
--
ALTER TABLE `categoria_activo`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `uq_cat_nombre` (`nombre`);

--
-- Indices de la tabla `certificacion`
--
ALTER TABLE `certificacion`
  ADD PRIMARY KEY (`id_certificacion`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`id_compra`),
  ADD KEY `fk_compra_usuario` (`id_usuario`),
  ADD KEY `idx_compra_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `cosecha`
--
ALTER TABLE `cosecha`
  ADD PRIMARY KEY (`id_cosecha`),
  ADD KEY `idx_cosecha_lote` (`id_lote`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id_detalle`),
  ADD KEY `fk_detcomp_insumo` (`id_insumo`),
  ADD KEY `idx_detcomp_compra` (`id_compra`);

--
-- Indices de la tabla `entrega_epp`
--
ALTER TABLE `entrega_epp`
  ADD PRIMARY KEY (`id_entrega`),
  ADD KEY `fk_entrepp_epp` (`id_epp`),
  ADD KEY `idx_entrepp_usuario` (`id_usuario`);

--
-- Indices de la tabla `entrega_herramienta`
--
ALTER TABLE `entrega_herramienta`
  ADD PRIMARY KEY (`id_entrega`),
  ADD KEY `fk_entherr_herramienta` (`id_herramienta`),
  ADD KEY `idx_entherr_usuario` (`id_usuario`);

--
-- Indices de la tabla `epp`
--
ALTER TABLE `epp`
  ADD PRIMARY KEY (`id_epp`);

--
-- Indices de la tabla `evidencia_tarea`
--
ALTER TABLE `evidencia_tarea`
  ADD PRIMARY KEY (`id_evidencia`),
  ADD KEY `fk_evid_tarea` (`id_tarea`),
  ADD KEY `fk_evid_usuario` (`id_usuario`);

--
-- Indices de la tabla `herramienta`
--
ALTER TABLE `herramienta`
  ADD PRIMARY KEY (`id_herramienta`);

--
-- Indices de la tabla `insumo`
--
ALTER TABLE `insumo`
  ADD PRIMARY KEY (`id_insumo`);

--
-- Indices de la tabla `lote`
--
ALTER TABLE `lote`
  ADD PRIMARY KEY (`id_lote`);

--
-- Indices de la tabla `mantenimiento`
--
ALTER TABLE `mantenimiento`
  ADD PRIMARY KEY (`id_mantenimiento`),
  ADD KEY `fk_mant_responsable` (`id_responsable`),
  ADD KEY `idx_mant_activo` (`id_activo`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `idx_notif_usuario` (`id_usuario`);

--
-- Indices de la tabla `pago_nomina`
--
ALTER TABLE `pago_nomina`
  ADD PRIMARY KEY (`id_pago`),
  ADD KEY `fk_pnomina_admin` (`id_registrado_por`),
  ADD KEY `idx_pnomina_usuario` (`id_usuario`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `registro_clima`
--
ALTER TABLE `registro_clima`
  ADD PRIMARY KEY (`id_clima`),
  ADD UNIQUE KEY `uq_clima_fecha` (`fecha`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `uq_rol_nombre` (`nombre`);

--
-- Indices de la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD PRIMARY KEY (`id_tarea`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `uq_usuario_dni` (`DNI`),
  ADD UNIQUE KEY `uq_usuario_correo` (`correo`),
  ADD KEY `idx_usuario_rol` (`id_rol`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `idx_venta_cosecha` (`id_cosecha`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad_lote`
--
ALTER TABLE `actividad_lote`
  MODIFY `id_actividad` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `activo`
--
ALTER TABLE `activo`
  MODIFY `id_activo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `aplicacion_insumo`
--
ALTER TABLE `aplicacion_insumo`
  MODIFY `id_aplicacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `asignacion_tarea`
--
ALTER TABLE `asignacion_tarea`
  MODIFY `id_asignacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria_activo`
--
ALTER TABLE `categoria_activo`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `certificacion`
--
ALTER TABLE `certificacion`
  MODIFY `id_certificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cosecha`
--
ALTER TABLE `cosecha`
  MODIFY `id_cosecha` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id_detalle` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entrega_epp`
--
ALTER TABLE `entrega_epp`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entrega_herramienta`
--
ALTER TABLE `entrega_herramienta`
  MODIFY `id_entrega` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `epp`
--
ALTER TABLE `epp`
  MODIFY `id_epp` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `evidencia_tarea`
--
ALTER TABLE `evidencia_tarea`
  MODIFY `id_evidencia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `herramienta`
--
ALTER TABLE `herramienta`
  MODIFY `id_herramienta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `insumo`
--
ALTER TABLE `insumo`
  MODIFY `id_insumo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `lote`
--
ALTER TABLE `lote`
  MODIFY `id_lote` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mantenimiento`
--
ALTER TABLE `mantenimiento`
  MODIFY `id_mantenimiento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pago_nomina`
--
ALTER TABLE `pago_nomina`
  MODIFY `id_pago` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `registro_clima`
--
ALTER TABLE `registro_clima`
  MODIFY `id_clima` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tarea`
--
ALTER TABLE `tarea`
  MODIFY `id_tarea` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividad_lote`
--
ALTER TABLE `actividad_lote`
  ADD CONSTRAINT `fk_actlot_lote` FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id_lote`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_actlot_usuario` FOREIGN KEY (`id_responsable`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `activo`
--
ALTER TABLE `activo`
  ADD CONSTRAINT `fk_activo_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria_activo` (`id_categoria`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `aplicacion_insumo`
--
ALTER TABLE `aplicacion_insumo`
  ADD CONSTRAINT `fk_aplinsumo_insumo` FOREIGN KEY (`id_insumo`) REFERENCES `insumo` (`id_insumo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_aplinsumo_lote` FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id_lote`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_aplinsumo_resp` FOREIGN KEY (`id_responsable`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `asignacion_tarea`
--
ALTER TABLE `asignacion_tarea`
  ADD CONSTRAINT `fk_asig_tarea` FOREIGN KEY (`id_tarea`) REFERENCES `tarea` (`id_tarea`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_asig_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `fk_compra_prov` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_compra_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `cosecha`
--
ALTER TABLE `cosecha`
  ADD CONSTRAINT `fk_cosecha_lote` FOREIGN KEY (`id_lote`) REFERENCES `lote` (`id_lote`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `fk_detcomp_compra` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detcomp_insumo` FOREIGN KEY (`id_insumo`) REFERENCES `insumo` (`id_insumo`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `entrega_epp`
--
ALTER TABLE `entrega_epp`
  ADD CONSTRAINT `fk_entrepp_epp` FOREIGN KEY (`id_epp`) REFERENCES `epp` (`id_epp`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entrepp_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `entrega_herramienta`
--
ALTER TABLE `entrega_herramienta`
  ADD CONSTRAINT `fk_entherr_herramienta` FOREIGN KEY (`id_herramienta`) REFERENCES `herramienta` (`id_herramienta`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_entherr_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `evidencia_tarea`
--
ALTER TABLE `evidencia_tarea`
  ADD CONSTRAINT `fk_evid_tarea` FOREIGN KEY (`id_tarea`) REFERENCES `tarea` (`id_tarea`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_evid_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mantenimiento`
--
ALTER TABLE `mantenimiento`
  ADD CONSTRAINT `fk_mant_activo` FOREIGN KEY (`id_activo`) REFERENCES `activo` (`id_activo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mant_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago_nomina`
--
ALTER TABLE `pago_nomina`
  ADD CONSTRAINT `fk_pnomina_admin` FOREIGN KEY (`id_registrado_por`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pnomina_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `fk_venta_cosecha` FOREIGN KEY (`id_cosecha`) REFERENCES `cosecha` (`id_cosecha`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

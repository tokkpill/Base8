-- ============================================================
-- BASE8 — UAEMex Academic Marketplace
-- Production Database Schema
-- ============================================================
-- This script extends the existing devwebcamp database.
-- Run AFTER the original schema is in place.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. FACULTIES — UAEMex academic faculties
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faculties` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(200) NOT NULL,
    `codigo` VARCHAR(20) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faculties` (`nombre`, `codigo`) VALUES
('Facultad de Ingeniería', 'FI'),
('Facultad de Ciencias', 'FC'),
('Facultad de Contaduría y Administración', 'FCA'),
('Facultad de Derecho', 'FD'),
('Facultad de Medicina', 'FM'),
('Facultad de Arquitectura y Diseño', 'FAD'),
('Facultad de Humanidades', 'FH'),
('Facultad de Ciencias Políticas y Sociales', 'FCPS'),
('Facultad de Economía', 'FE'),
('Facultad de Química', 'FQ'),
('Facultad de Lenguas', 'FL'),
('Facultad de Ciencias de la Conducta', 'FCC');

-- ------------------------------------------------------------
-- 2. CAREERS — Academic programs linked to faculties
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `careers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(200) NOT NULL,
    `faculty_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `careers` (`nombre`, `faculty_id`) VALUES
-- Facultad de Ingeniería (1)
('Ingeniería en Computación', 1),
('Ingeniería Civil', 1),
('Ingeniería Mecánica', 1),
('Ingeniería en Electrónica', 1),
('Ingeniería en Sistemas Energéticos Sustentables', 1),
-- Facultad de Ciencias (2)
('Licenciatura en Matemáticas', 2),
('Licenciatura en Física', 2),
('Licenciatura en Biología', 2),
('Licenciatura en Biotecnología', 2),
-- Facultad de Contaduría y Administración (3)
('Licenciatura en Contaduría', 3),
('Licenciatura en Administración', 3),
('Licenciatura en Informática Administrativa', 3),
('Licenciatura en Mercadotecnia', 3),
-- Facultad de Derecho (4)
('Licenciatura en Derecho', 4),
-- Facultad de Medicina (5)
('Médico Cirujano', 5),
('Licenciatura en Nutrición', 5),
('Licenciatura en Terapia Física', 5),
-- Facultad de Arquitectura y Diseño (6)
('Licenciatura en Arquitectura', 6),
('Licenciatura en Diseño Gráfico', 6),
('Licenciatura en Diseño Industrial', 6),
-- Facultad de Humanidades (7)
('Licenciatura en Filosofía', 7),
('Licenciatura en Historia', 7),
('Licenciatura en Letras Latinoamericanas', 7),
-- Facultad de Ciencias Políticas y Sociales (8)
('Licenciatura en Ciencias Políticas', 8),
('Licenciatura en Comunicación', 8),
('Licenciatura en Sociología', 8),
-- Facultad de Economía (9)
('Licenciatura en Economía', 9),
('Licenciatura en Actuaría', 9),
('Licenciatura en Relaciones Económicas Internacionales', 9),
-- Facultad de Química (10)
('Licenciatura en Química', 10),
('Químico Farmacéutico Biólogo', 10),
-- Facultad de Lenguas (11)
('Licenciatura en Lenguas', 11),
('Licenciatura en Enseñanza del Inglés', 11),
-- Facultad de Ciencias de la Conducta (12)
('Licenciatura en Psicología', 12),
('Licenciatura en Trabajo Social', 12),
('Licenciatura en Educación', 12);

-- ------------------------------------------------------------
-- 3. EXTEND USUARIOS TABLE — New academic & reputation fields
-- ------------------------------------------------------------
ALTER TABLE `usuarios`
    ADD COLUMN IF NOT EXISTS `role` ENUM('student','advisor','admin') NOT NULL DEFAULT 'student' AFTER `admin`,
    ADD COLUMN IF NOT EXISTS `faculty_id` INT UNSIGNED NULL AFTER `role`,
    ADD COLUMN IF NOT EXISTS `career_id` INT UNSIGNED NULL AFTER `faculty_id`,
    ADD COLUMN IF NOT EXISTS `bio` TEXT NULL AFTER `career_id`,
    ADD COLUMN IF NOT EXISTS `avatar` VARCHAR(255) DEFAULT 'default.png' AFTER `bio`,
    ADD COLUMN IF NOT EXISTS `prestige_points` DECIMAL(10,2) DEFAULT 0.00 AFTER `avatar`,
    ADD COLUMN IF NOT EXISTS `reputation_badge` VARCHAR(50) DEFAULT 'Novato' AFTER `prestige_points`,
    ADD COLUMN IF NOT EXISTS `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `reputation_badge`;

-- Add FK constraints (safe — won't fail if the columns already exist with data)
-- We use a separate ALTER to avoid errors if FK already exists
ALTER TABLE `usuarios`
    ADD CONSTRAINT `fk_usuarios_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE SET NULL,
    ADD CONSTRAINT `fk_usuarios_career` FOREIGN KEY (`career_id`) REFERENCES `careers`(`id`) ON DELETE SET NULL;

-- Sync the admin field to new role field for existing admins
UPDATE `usuarios` SET `role` = 'admin' WHERE `admin` = 1;

-- ------------------------------------------------------------
-- 4. ASSET CATEGORIES — Types of academic resources
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `asset_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `icono` VARCHAR(50) DEFAULT 'fa-file',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `asset_categories` (`nombre`, `slug`, `icono`) VALUES
('Apuntes', 'apuntes', 'fa-book'),
('Guías de Estudio', 'guias-estudio', 'fa-compass'),
('Plantillas', 'plantillas', 'fa-file-lines'),
('Proyectos', 'proyectos', 'fa-diagram-project'),
('Resúmenes', 'resumenes', 'fa-list-check'),
('Presentaciones', 'presentaciones', 'fa-display'),
('Exámenes Resueltos', 'examenes', 'fa-pen-to-square'),
('Tutoriales', 'tutoriales', 'fa-video'),
('Código Fuente', 'codigo', 'fa-code'),
('Investigaciones', 'investigaciones', 'fa-flask');

-- ------------------------------------------------------------
-- 5. ACADEMIC ASSETS — Marketplace items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `academic_assets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(300) NOT NULL,
    `descripcion` TEXT NOT NULL,
    `category_id` INT UNSIGNED NOT NULL,
    `faculty_id` INT UNSIGNED NOT NULL,
    `career_id` INT UNSIGNED NULL,
    `autor_id` INT NOT NULL,
    `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `archivo` VARCHAR(500) NOT NULL,
    `archivo_preview` VARCHAR(500) NULL,
    `formato` VARCHAR(20) DEFAULT 'pdf',
    `tamano_bytes` BIGINT UNSIGNED DEFAULT 0,
    `descargas` INT UNSIGNED DEFAULT 0,
    `rating_promedio` DECIMAL(3,2) DEFAULT 0.00,
    `total_ratings` INT UNSIGNED DEFAULT 0,
    `estado` ENUM('borrador','publicado','suspendido','eliminado') DEFAULT 'publicado',
    `destacado` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `asset_categories`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`career_id`) REFERENCES `careers`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`autor_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    INDEX `idx_assets_faculty` (`faculty_id`),
    INDEX `idx_assets_career` (`career_id`),
    INDEX `idx_assets_category` (`category_id`),
    INDEX `idx_assets_rating` (`rating_promedio` DESC),
    INDEX `idx_assets_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. REPUTATION LOG — Tracks prestige point events
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reputation_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `tipo` ENUM('descarga','rating','contribucion','venta','resena','bonus') NOT NULL,
    `puntos` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    `referencia_id` INT UNSIGNED NULL COMMENT 'ID of asset/transaction that triggered this',
    `descripcion` VARCHAR(300) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    INDEX `idx_reputation_usuario` (`usuario_id`),
    INDEX `idx_reputation_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. MARKETPLACE TRANSACTIONS — P2P purchase flow
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `marketplace_transactions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `asset_id` INT UNSIGNED NOT NULL,
    `comprador_id` INT NOT NULL,
    `vendedor_id` INT NOT NULL,
    `monto` DECIMAL(10,2) NOT NULL,
    `estado` ENUM('pendiente','completada','verificada','disputada','reembolsada','cancelada') DEFAULT 'pendiente',
    `metodo_pago` VARCHAR(50) DEFAULT 'creditos',
    `referencia_pago` VARCHAR(255) NULL,
    `notas` TEXT NULL,
    `completed_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`asset_id`) REFERENCES `academic_assets`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`comprador_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    INDEX `idx_tx_comprador` (`comprador_id`),
    INDEX `idx_tx_vendedor` (`vendedor_id`),
    INDEX `idx_tx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. ASSET REVIEWS — Star ratings + text reviews
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `asset_reviews` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `asset_id` INT UNSIGNED NOT NULL,
    `usuario_id` INT NOT NULL,
    `transaction_id` INT UNSIGNED NULL,
    `rating` TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `comentario` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`asset_id`) REFERENCES `academic_assets`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`transaction_id`) REFERENCES `marketplace_transactions`(`id`) ON DELETE SET NULL,
    UNIQUE KEY `uq_review_per_user_asset` (`asset_id`, `usuario_id`),
    INDEX `idx_reviews_asset` (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. ADVISOR PROFILES — Extended advisor information
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `advisor_profiles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL UNIQUE,
    `especialidades` TEXT NOT NULL COMMENT 'JSON array of specialties',
    `descripcion` TEXT NOT NULL,
    `tarifa_hora` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `disponibilidad` TEXT NULL COMMENT 'JSON schedule',
    `estudiantes_ayudados` INT UNSIGNED DEFAULT 0,
    `rating_promedio` DECIMAL(3,2) DEFAULT 0.00,
    `total_sesiones` INT UNSIGNED DEFAULT 0,
    `activo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. ADVISOR BOOKINGS — Consultancy sessions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `advisor_bookings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `advisor_id` INT UNSIGNED NOT NULL,
    `estudiante_id` INT NOT NULL,
    `fecha` DATE NOT NULL,
    `hora_inicio` TIME NOT NULL,
    `hora_fin` TIME NOT NULL,
    `tema` VARCHAR(300) NOT NULL,
    `notas` TEXT NULL,
    `estado` ENUM('pendiente','confirmada','completada','cancelada') DEFAULT 'pendiente',
    `rating` TINYINT UNSIGNED NULL CHECK (`rating` BETWEEN 1 AND 5),
    `monto` DECIMAL(10,2) DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`advisor_id`) REFERENCES `advisor_profiles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`estudiante_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    INDEX `idx_bookings_advisor` (`advisor_id`),
    INDEX `idx_bookings_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. SEARCH INDEX — Optimized FULLTEXT search
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `search_index` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tipo` ENUM('asset','advisor','usuario') NOT NULL,
    `referencia_id` INT UNSIGNED NOT NULL,
    `titulo` VARCHAR(300) NOT NULL,
    `contenido` TEXT NOT NULL,
    `tags` VARCHAR(500) NULL,
    `faculty_id` INT UNSIGNED NULL,
    `career_id` INT UNSIGNED NULL,
    `category_id` INT UNSIGNED NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT KEY `ft_search` (`titulo`, `contenido`, `tags`),
    INDEX `idx_search_tipo` (`tipo`),
    INDEX `idx_search_faculty` (`faculty_id`),
    INDEX `idx_search_career` (`career_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- END OF BASE8 PRODUCTION SCHEMA
-- ============================================================

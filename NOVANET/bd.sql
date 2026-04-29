CREATE DATABASE IF NOT EXISTS gestion_espacios;
USE gestion_espacios;

-- Tabla roles
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) UNIQUE NOT NULL
);

INSERT INTO roles (nombre_rol) VALUES ('admin'), ('area_academica'), ('prefecto');

-- Tabla usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    facultad VARCHAR(100),
    ultima_conexion DATETIME,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE RESTRICT
);

-- Insertar usuario admin por defecto (contraseña: admin123)
INSERT INTO usuarios (nombre, email, password_hash, id_rol) 
VALUES ('Administrador', 'admin@institucion.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Tabla espacios
CREATE TABLE espacios (
    id_espacio INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('salon','laboratorio','computo','usos_multiples') NOT NULL,
    capacidad INT NOT NULL CHECK (capacidad > 0),
    equipamiento TEXT,
    ubicacion VARCHAR(150),
    estado_actual ENUM('disponible','ocupado','mantenimiento') DEFAULT 'disponible'
    
);

-- Tabla franjas horarias (horarios base)
CREATE TABLE franjas_horarias (
    id_franja INT AUTO_INCREMENT PRIMARY KEY,
    dia_semana TINYINT NOT NULL CHECK (dia_semana BETWEEN 1 AND 7), -- 1=Lunes,...,7=Domingo
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL
);

-- Tabla asignaciones (programación académica)
CREATE TABLE asignaciones (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_espacio INT NOT NULL,
    id_docente INT NOT NULL,      -- referencia a usuarios con rol 'area_academica' o docente
    id_franja INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    periodo_academico VARCHAR(20),
    estado ENUM('activa','cancelada') DEFAULT 'activa',
    FOREIGN KEY (id_espacio) REFERENCES espacios(id_espacio) ON DELETE CASCADE,
    FOREIGN KEY (id_docente) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_franja) REFERENCES franjas_horarias(id_franja) ON DELETE CASCADE,
    UNIQUE KEY unique_asignacion (id_espacio, id_franja, fecha_inicio) -- evita conflictos básicos
);

-- Tabla incidencias
CREATE TABLE incidencias (
    id_incidencia INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('ausencia_docente','falla_recurso','otro') NOT NULL,
    descripcion TEXT NOT NULL,
    id_espacio INT NOT NULL,
    id_reportante INT NOT NULL,   -- usuario que reporta
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente','en_proceso','resuelta') DEFAULT 'pendiente',
    prioridad ENUM('baja','media','alta') DEFAULT 'media',
    FOREIGN KEY (id_espacio) REFERENCES espacios(id_espacio) ON DELETE CASCADE,
    FOREIGN KEY (id_reportante) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla mantenimientos
CREATE TABLE mantenimientos (
    id_mantenimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_espacio INT NOT NULL,
    id_incidencia_asociada INT NULL,
    descripcion_tarea TEXT NOT NULL,
    fecha_programada DATE NOT NULL,
    fecha_ejecucion DATE NULL,
    tecnico_responsable VARCHAR(100),
    estado ENUM('programado','en_curso','completado') DEFAULT 'programado',
    FOREIGN KEY (id_espacio) REFERENCES espacios(id_espacio) ON DELETE CASCADE,
    FOREIGN KEY (id_incidencia_asociada) REFERENCES incidencias(id_incidencia) ON DELETE SET NULL
);


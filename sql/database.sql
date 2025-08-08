-- CRM Cámara de Comercio de Querétaro Database Schema
-- MySQL 5.7

CREATE DATABASE IF NOT EXISTS crm_canaco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crm_canaco;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'jefe_afiliadores', 'afiliador', 'administrativo', 'consejero', 'mesa_directiva', 'contabilidad', 'auxiliar_contabilidad', 'atencion_clientes') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Prospects table
CREATE TABLE prospects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    commercial_name VARCHAR(200) NOT NULL,
    contact_name VARCHAR(200),
    position VARCHAR(100),
    address TEXT,
    phone VARCHAR(20),
    whatsapp VARCHAR(20),
    email VARCHAR(100),
    rfc VARCHAR(13),
    company_size ENUM('micro', 'pequeña', 'mediana', 'grande'),
    business_category ENUM('servicios', 'retail', 'mayorista_b2b', 'servicios_productos', 'restaurantes_bares', 'turismo', 'despachos_notarias', 'b2b_restaurantero', 'b2b_industrial', 'tics', 'consultorias', 'miscelaneas', 'cadenas_autoservicio', 'servicios_industriales', 'inmobiliario'),
    customer_journey_stage ENUM('prospectacion', 'atencion', 'facturacion', 'postventa', 'upselling', 'crossselling', 'patrocinios', 'mailing') DEFAULT 'prospectacion',
    membership_type ENUM('basica', 'emprendedor', 'pyme', 'visionario', 'premier') NULL,
    special_discount TEXT,
    notes TEXT,
    assigned_to INT,
    is_member BOOLEAN DEFAULT FALSE,
    membership_number VARCHAR(50) NULL,
    membership_expiry DATE NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_type ENUM('interno', 'externo', 'terceros', 'rueda_prensa') NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    location VARCHAR(255),
    max_attendees INT,
    registration_link VARCHAR(500),
    is_public BOOLEAN DEFAULT TRUE,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Event attendees table
CREATE TABLE event_attendees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    prospect_id INT NULL,
    name VARCHAR(200),
    email VARCHAR(100),
    phone VARCHAR(20),
    attended BOOLEAN DEFAULT FALSE,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (prospect_id) REFERENCES prospects(id)
);

-- Agenda table
CREATE TABLE agenda (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    action_type ENUM('llamada', 'whatsapp', 'email', 'visita', 'seguimiento', 'otro') NOT NULL,
    scheduled_date DATETIME NOT NULL,
    completed_date DATETIME NULL,
    status ENUM('pendiente', 'completada', 'cancelada') DEFAULT 'pendiente',
    prospect_id INT,
    assigned_to INT NOT NULL,
    created_by INT NOT NULL,
    notes TEXT,
    next_action TEXT,
    next_action_date DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (prospect_id) REFERENCES prospects(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('agenda', 'evento', 'prospecto', 'sistema') DEFAULT 'sistema',
    is_read BOOLEAN DEFAULT FALSE,
    related_id INT NULL,
    related_type VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Customer journey history table
CREATE TABLE customer_journey_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prospect_id INT NOT NULL,
    previous_stage ENUM('prospectacion', 'atencion', 'facturacion', 'postventa', 'upselling', 'crossselling', 'patrocinios', 'mailing'),
    current_stage ENUM('prospectacion', 'atencion', 'facturacion', 'postventa', 'upselling', 'crossselling', 'patrocinios', 'mailing'),
    changed_by INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prospect_id) REFERENCES prospects(id),
    FOREIGN KEY (changed_by) REFERENCES users(id)
);

-- SIEM registrations table (Sistema de Información Empresarial Mexicano)
CREATE TABLE siem_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    prospect_id INT NOT NULL,
    registration_number VARCHAR(100),
    registered_by INT NOT NULL,
    registration_date DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prospect_id) REFERENCES prospects(id),
    FOREIGN KEY (registered_by) REFERENCES users(id)
);

-- Insert example data

-- Insert admin user (password: admin123)
INSERT INTO users (username, email, password, first_name, last_name, role) VALUES
('admin', 'admin@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'Sistema', 'admin'),
('jcoronado', 'jcoronado@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Coronado', 'jefe_afiliadores'),
('mlopez', 'mlopez@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'María', 'López', 'afiliador'),
('pgarcia', 'pgarcia@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pedro', 'García', 'afiliador'),
('lsanchez', 'lsanchez@camaradecomercioqro.mx', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Laura', 'Sánchez', 'administrativo');

-- Insert example prospects
INSERT INTO prospects (commercial_name, contact_name, position, address, phone, whatsapp, email, rfc, company_size, business_category, assigned_to, created_by) VALUES
('Restaurante El Mesón', 'Carlos Hernández', 'Propietario', 'Av. Constituyentes 123, Centro, Querétaro', '4421234567', '4421234567', 'carlos@elmeson.com', 'HERC850101ABC', 'pequeña', 'restaurantes_bares', 3, 2),
('TecnoSoluciones QRO', 'Ana Martínez', 'Gerente General', 'Blvd. Bernardo Quintana 200, Col. Centro Sur', '4427654321', '4427654321', 'ana@tecnosol.com', 'MARA900215XYZ', 'mediana', 'tics', 4, 2),
('Consultoría Empresarial', 'Roberto Flores', 'Director', 'Av. Universidad 456, Col. Las Campanas', '4423456789', '4423456789', 'roberto@consultoria.mx', 'FLOR750320DEF', 'pequeña', 'consultorias', 3, 2),
('Autopartes del Centro', 'Sofía Ramírez', 'Administradora', 'Calle 5 de Mayo 789, Centro Histórico', '4428765432', '4428765432', 'sofia@autopartes.com', 'RAMS820505GHI', 'micro', 'servicios', 4, 2),
('Hotel Plaza Querétaro', 'Miguel Ángel Torres', 'Gerente de Ventas', 'Plaza de Armas 15, Centro', '4421112233', '4421112233', 'miguel@hotelplaza.com', 'TORM880910JKL', 'mediana', 'turismo', 3, 2);

-- Insert example events
INSERT INTO events (title, description, event_type, start_date, end_date, location, max_attendees, created_by) VALUES
('Foro de Negocios 2024', 'Evento anual para promocionar los negocios afiliados', 'interno', '2024-03-15 09:00:00', '2024-03-15 18:00:00', 'Centro de Convenciones Querétaro', 200, 2),
('Capacitación Fiscal', 'Taller sobre las nuevas disposiciones fiscales', 'interno', '2024-02-20 10:00:00', '2024-02-20 16:00:00', 'Sala de Juntas CANACO', 50, 2),
('Expo PyME', 'Exposición de pequeñas y medianas empresas', 'externo', '2024-04-10 08:00:00', '2024-04-12 20:00:00', 'Centro Expositor Querétaro', 500, 2);

-- Insert example agenda items
INSERT INTO agenda (title, description, action_type, scheduled_date, prospect_id, assigned_to, created_by) VALUES
('Llamada de seguimiento - El Mesón', 'Contactar para agendar visita presencial', 'llamada', '2024-01-15 10:00:00', 1, 3, 2),
('Visita TecnoSoluciones', 'Presentar beneficios de membresía PyME', 'visita', '2024-01-16 14:00:00', 2, 4, 2),
('Email informativo - Consultoría', 'Enviar información sobre eventos próximos', 'email', '2024-01-17 09:00:00', 3, 3, 2);

-- Insert example notifications
INSERT INTO notifications (user_id, title, message, type) VALUES
(3, 'Nueva tarea asignada', 'Se te ha asignado seguimiento con Restaurante El Mesón', 'agenda'),
(4, 'Evento próximo', 'Recuerda el Foro de Negocios 2024 el 15 de marzo', 'evento'),
(2, 'Nuevo prospecto', 'Se ha registrado un nuevo prospecto: Hotel Plaza Querétaro', 'prospecto');

-- Insert customer journey history
INSERT INTO customer_journey_history (prospect_id, previous_stage, current_stage, changed_by, notes) VALUES
(1, NULL, 'prospectacion', 2, 'Prospecto inicial registrado'),
(2, 'prospectacion', 'atencion', 2, 'Primer contacto realizado exitosamente'),
(3, NULL, 'prospectacion', 2, 'Referido por cliente existente');
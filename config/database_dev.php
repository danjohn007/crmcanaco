<?php
/**
 * Development Database Configuration (SQLite)
 * CRM Cámara de Comercio de Querétaro
 */

// For development, use SQLite
$dbPath = __DIR__ . '/../data/crm_canaco.sqlite';
$dbDir = dirname($dbPath);

// Create data directory if it doesn't exist
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

try {
    $dsn = "sqlite:$dbPath";
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create tables if they don't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        role VARCHAR(50) NOT NULL DEFAULT 'afiliador',
        is_active BOOLEAN DEFAULT 1,
        last_login DATETIME NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS prospects (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        commercial_name VARCHAR(200) NOT NULL,
        contact_name VARCHAR(200),
        position VARCHAR(100),
        address TEXT,
        phone VARCHAR(20),
        whatsapp VARCHAR(20),
        email VARCHAR(100),
        rfc VARCHAR(13),
        company_size VARCHAR(20),
        business_category VARCHAR(50),
        customer_journey_stage VARCHAR(30) DEFAULT 'prospectacion',
        membership_type VARCHAR(20) NULL,
        special_discount TEXT,
        notes TEXT,
        assigned_to INTEGER,
        is_member BOOLEAN DEFAULT 0,
        membership_number VARCHAR(50) NULL,
        membership_expiry DATE NULL,
        created_by INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        event_type VARCHAR(20) NOT NULL,
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        location VARCHAR(255),
        max_attendees INTEGER,
        registration_link VARCHAR(500),
        is_public BOOLEAN DEFAULT 1,
        created_by INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS agenda (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        action_type VARCHAR(20) NOT NULL,
        scheduled_date DATETIME NOT NULL,
        completed_date DATETIME NULL,
        status VARCHAR(20) DEFAULT 'pendiente',
        prospect_id INTEGER,
        assigned_to INTEGER NOT NULL,
        created_by INTEGER NOT NULL,
        notes TEXT,
        next_action TEXT,
        next_action_date DATETIME NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(20) DEFAULT 'sistema',
        is_read BOOLEAN DEFAULT 0,
        related_id INTEGER NULL,
        related_type VARCHAR(50) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS customer_journey_history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prospect_id INTEGER NOT NULL,
        previous_stage VARCHAR(30),
        current_stage VARCHAR(30),
        changed_by INTEGER NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS siem_registrations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        prospect_id INTEGER NOT NULL,
        registration_number VARCHAR(100),
        registered_by INTEGER NOT NULL,
        registration_date DATE NOT NULL,
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_attendees (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        event_id INTEGER NOT NULL,
        prospect_id INTEGER NULL,
        name VARCHAR(200),
        email VARCHAR(100),
        phone VARCHAR(20),
        attended BOOLEAN DEFAULT 0,
        registered_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Check if we need to insert default users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
    $result = $stmt->fetch();
    if ($result['count'] == 0) {
        // Insert default admin (password: admin123)
        $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, email, password, first_name, last_name, role) VALUES 
                   ('admin', 'admin@canaco.test', '$adminHash', 'Admin', 'Sistema', 'admin')");
        
        // Insert sample data
        $pdo->exec("INSERT INTO users (username, email, password, first_name, last_name, role) VALUES 
                   ('jcoronado', 'jcoronado@canaco.test', '$adminHash', 'Juan', 'Coronado', 'jefe_afiliadores'),
                   ('mlopez', 'mlopez@canaco.test', '$adminHash', 'María', 'López', 'afiliador')");
        
        // Insert sample prospects
        $pdo->exec("INSERT INTO prospects (commercial_name, contact_name, position, phone, email, business_category, assigned_to, created_by) VALUES 
                   ('Restaurante El Mesón', 'Carlos Hernández', 'Propietario', '4421234567', 'carlos@elmeson.com', 'restaurantes_bares', 3, 1),
                   ('TecnoSoluciones QRO', 'Ana Martínez', 'Gerente', '4427654321', 'ana@tecnosol.com', 'tics', 3, 1)");
        
        // Insert sample events
        $pdo->exec("INSERT INTO events (title, description, event_type, start_date, end_date, location, created_by) VALUES 
                   ('Foro de Negocios 2024', 'Evento anual para promocionar los negocios', 'interno', '2024-03-15 09:00:00', '2024-03-15 18:00:00', 'Centro de Convenciones', 1)");
        
        // Insert sample agenda items
        $pdo->exec("INSERT INTO agenda (title, description, action_type, scheduled_date, prospect_id, assigned_to, created_by) VALUES 
                   ('Llamada de seguimiento - El Mesón', 'Contactar para agendar visita', 'llamada', datetime('now', '+1 day'), 1, 3, 1)");
    }
    
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
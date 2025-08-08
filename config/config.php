<?php
/**
 * General Configuration
 * CRM Cámara de Comercio de Querétaro
 */

// Site settings
define('SITE_NAME', 'CRM Cámara de Comercio de Querétaro');
define('SITE_URL', 'http://localhost');
define('BASE_PATH', dirname(__DIR__));

// Session settings
define('SESSION_TIMEOUT', 3600); // 1 hour

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_JEFE_AFILIADORES', 'jefe_afiliadores');
define('ROLE_AFILIADOR', 'afiliador');
define('ROLE_ADMINISTRATIVO', 'administrativo');
define('ROLE_CONSEJERO', 'consejero');
define('ROLE_MESA_DIRECTIVA', 'mesa_directiva');
define('ROLE_CONTABILIDAD', 'contabilidad');
define('ROLE_AUXILIAR_CONTABILIDAD', 'auxiliar_contabilidad');
define('ROLE_ATENCION_CLIENTES', 'atencion_clientes');

// Company sizes
$company_sizes = [
    'micro' => 'Micro (hasta 10 trabajadores)',
    'pequeña' => 'Pequeña (hasta 49 trabajadores)', 
    'mediana' => 'Mediana (hasta 500 trabajadores)',
    'grande' => 'Grande (más de 500 trabajadores)'
];

// Business categories
$business_categories = [
    'servicios' => 'Servicios',
    'retail' => 'Venta de productos retail',
    'mayorista_b2b' => 'Venta de productos mayorista B2B',
    'servicios_productos' => 'Servicios y productos',
    'restaurantes_bares' => 'Restaurantes & bares',
    'turismo' => 'Turismo',
    'despachos_notarias' => 'Despachos, notarias y corporativos',
    'b2b_restaurantero' => 'B2B sector restaurantero & turismo',
    'b2b_industrial' => 'B2B industrial',
    'tics' => 'TIC\'s',
    'consultorias' => 'Consultorías, capacitadores, certificaciones',
    'miscelaneas' => 'Miscelaneas',
    'cadenas_autoservicio' => 'Cadenas y tiendas autoservicio',
    'servicios_industriales' => 'Servicios industriales',
    'inmobiliario' => 'Sector Inmobiliario'
];

// Membership types
$membership_types = [
    'basica' => 'Básica',
    'emprendedor' => 'Emprendedor',
    'pyme' => 'PyME',
    'visionario' => 'Visionario',
    'premier' => 'Premier'
];

// Customer journey stages
$customer_journey_stages = [
    'prospectacion' => 'Prospectación',
    'atencion' => 'Atención',
    'facturacion' => 'Facturación',
    'postventa' => 'Servicio Post-venta',
    'upselling' => 'Upselling',
    'crossselling' => 'Cross-selling',
    'patrocinios' => 'Patrocinios',
    'mailing' => 'Servicios de Mailing'
];

// Event types
$event_types = [
    'interno' => 'Evento Interno',
    'externo' => 'Evento Externo',
    'terceros' => 'Evento de Terceros',
    'rueda_prensa' => 'Rueda de Prensa'
];

// Time zone
date_default_timezone_set('America/Mexico_City');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
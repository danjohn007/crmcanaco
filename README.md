# CRM Cámara de Comercio de Querétaro

Sistema de gestión de relaciones con clientes (CRM) desarrollado específicamente para la Cámara de Comercio de Querétaro. Este sistema permite gestionar prospectos, eventos, agenda y el customer journey completo de los afiliados.

## Características Principales

### 🎯 Dashboard Inteligente
- Panel de control personalizado por rol de usuario
- Métricas en tiempo real de prospectos, eventos y tareas
- Visualización del customer journey
- Notificaciones automatizadas

### 👥 Gestión de Prospectos
- Registro completo de información comercial
- Clasificación por tamaño de empresa y gremio
- Seguimiento del customer journey
- Asignación a afiliadores
- Historial de interacciones

### 📅 Sistema de Eventos
- Creación de eventos internos, externos y de terceros
- Gestión de invitaciones y registros
- Control de asistencias
- Ligas de registro automatizadas

### 🗓️ Agenda Inteligente
- Programación de actividades por tipo (llamadas, visitas, emails)
- Seguimiento de tareas completadas
- Programación de acciones de seguimiento
- Alertas y recordatorios

### 🔍 Búsqueda Inteligente
- Búsqueda por nombre comercial, RFC, teléfono, dirección
- Filtros avanzados por categoría y etapa del customer journey
- Resultados en tiempo real

### 📊 Customer Journey Tracking
- Seguimiento visual del proceso de afiliación
- Etapas: Prospectación → Atención → Facturación → Post-venta
- Historial de cambios de etapa
- Métricas por etapa

### 📈 Dashboard de Métricas
- Estadísticas de afiliaciones por tipo de membresía
- Número de registros SIEM
- Métricas de desempeño por afiliador
- Reportes de cumplimiento de objetivos

### 👤 Gestión de Usuarios
- Roles diferenciados: Admin, Jefe de Afiliadores, Afiliador, etc.
- Permisos granulares por función
- Gestión de perfiles y passwords

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+ (sin frameworks)
- **Base de Datos**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3, HTML5, CSS3, JavaScript (jQuery)
- **Servidor Web**: Apache 2.4+
- **Arquitectura**: MVC (Model-View-Controller)

## Requisitos del Sistema

### Servidor Web
- Apache 2.4 o superior
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Módulos Apache requeridos:
  - mod_rewrite
  - mod_headers
  - mod_deflate (opcional, para compresión)
  - mod_expires (opcional, para cache)

### Extensiones PHP Requeridas
- PDO MySQL
- mysqli
- json
- session
- filter
- hash

## Instalación en Servidor Apache

### 1. Preparar el Entorno

```bash
# En Ubuntu/Debian
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-pdo php-json

# En CentOS/RHEL
sudo yum install httpd mysql-server php php-mysql php-pdo php-json
```

### 2. Configurar Apache

Crear un virtual host para el CRM:

```apache
<VirtualHost *:80>
    ServerName crm-canaco.local
    DocumentRoot /var/www/html/crmcanaco
    
    <Directory /var/www/html/crmcanaco>
        AllowOverride All
        Require all granted
        Options -Indexes
    </Directory>
    
    # Seguridad adicional
    <FilesMatch "\.php$">
        SetHandler application/x-httpd-php
    </FilesMatch>
    
    # Log de errores
    ErrorLog ${APACHE_LOG_DIR}/crm-canaco_error.log
    CustomLog ${APACHE_LOG_DIR}/crm-canaco_access.log combined
</VirtualHost>
```

Habilitar módulos necesarios:
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires
sudo systemctl restart apache2
```

### 3. Clonar el Repositorio

```bash
cd /var/www/html
sudo git clone https://github.com/danjohn007/crmcanaco.git
sudo chown -R www-data:www-data crmcanaco
sudo chmod -R 755 crmcanaco
```

### 4. Configurar la Base de Datos

```bash
# Acceder a MySQL
mysql -u root -p

# Crear la base de datos
CREATE DATABASE crm_canaco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Crear usuario (opcional, usar root también es válido)
CREATE USER 'crm_user'@'localhost' IDENTIFIED BY 'tu_password_seguro';
GRANT ALL PRIVILEGES ON crm_canaco.* TO 'crm_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Importar el esquema
mysql -u root -p crm_canaco < /var/www/html/crmcanaco/sql/database.sql
```

### 5. Configurar la Aplicación

Editar el archivo de configuración de base de datos:

```bash
sudo nano /var/www/html/crmcanaco/config/database.php
```

Actualizar las credenciales:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'crm_canaco');
define('DB_USER', 'root'); // o 'crm_user'
define('DB_PASS', 'tu_password');
```

### 6. Configurar Permisos

```bash
sudo chown -R www-data:www-data /var/www/html/crmcanaco
sudo find /var/www/html/crmcanaco -type d -exec chmod 755 {} \;
sudo find /var/www/html/crmcanaco -type f -exec chmod 644 {} \;
```

### 7. Configurar el Host (opcional)

Para desarrollo local, agregar al archivo `/etc/hosts`:
```
127.0.0.1    crm-canaco.local
```

### 8. Acceder al Sistema

Abrir el navegador y navegar a:
- `http://crm-canaco.local` (si se configuró virtual host)
- `http://localhost/crmcanaco` (instalación en subdirectorio)

**Credenciales de prueba:**
- Usuario: `admin`
- Contraseña: `admin123`

## Estructura del Proyecto

```
crmcanaco/
├── assets/                 # Recursos estáticos
│   ├── css/               # Hojas de estilo
│   ├── js/                # JavaScript
│   └── images/            # Imágenes
├── config/                # Configuración
│   ├── database.php       # Conexión a BD
│   └── config.php         # Configuración general
├── controllers/           # Controladores MVC
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── ProspectController.php
│   ├── EventController.php
│   └── AgendaController.php
├── models/                # Modelos de datos
│   ├── Database.php       # Clase base de BD
│   ├── User.php
│   ├── Prospect.php
│   ├── Event.php
│   └── Agenda.php
├── views/                 # Vistas
│   ├── layouts/           # Plantillas base
│   ├── auth/              # Autenticación
│   ├── dashboard/         # Dashboard
│   ├── prospects/         # Gestión de prospectos
│   ├── events/            # Gestión de eventos
│   └── agenda/            # Gestión de agenda
├── sql/                   # Scripts de BD
│   └── database.sql       # Esquema y datos iniciales
├── .htaccess             # Configuración Apache
├── index.php             # Punto de entrada
└── README.md             # Este archivo
```

## Usuarios y Roles

### Roles del Sistema
- **Admin**: Acceso completo al sistema
- **Jefe de Afiliadores**: Gestión de equipo y reportes
- **Afiliador**: Gestión de prospectos y agenda
- **Administrativo**: Funciones administrativas
- **Consejero**: Consulta de información
- **Mesa Directiva**: Reportes ejecutivos
- **Contabilidad**: Gestión financiera
- **Auxiliar Contabilidad**: Soporte contable
- **Atención a Clientes**: Servicio post-venta

### Usuarios de Prueba Incluidos
```
admin / admin123          - Administrador
jcoronado / admin123      - Jefe de Afiliadores  
mlopez / admin123         - Afiliador
pgarcia / admin123        - Afiliador
lsanchez / admin123       - Administrativo
```

## Funcionalidades por Módulo

### Gestión de Prospectos
- ✅ Alta de prospectos con información completa
- ✅ Clasificación por tamaño y gremio empresarial
- ✅ Asignación a afiliadores
- ✅ Seguimiento del customer journey
- ✅ Historial de interacciones
- ✅ Búsqueda avanzada

### Gestión de Eventos
- ✅ Creación de eventos por tipo
- ✅ Gestión de invitaciones
- ✅ Control de registros y asistencias
- ✅ Generación de ligas de registro

### Sistema de Agenda
- ✅ Programación de actividades
- ✅ Diferentes tipos de acción
- ✅ Seguimiento de completitud
- ✅ Programación de seguimientos

### Dashboard y Reportes
- ✅ Métricas en tiempo real
- ✅ Visualización del customer journey
- ✅ Estadísticas por usuario y periodo
- ✅ Notificaciones automatizadas

## Configuración Avanzada

### Seguridad
El sistema incluye medidas de seguridad como:
- Validación de entrada de datos
- Protección contra inyección SQL
- Control de acceso basado en roles
- Headers de seguridad HTTP
- Protección de archivos sensibles

### Performance
- Compresión GZIP habilitada
- Cache de archivos estáticos
- Consultas optimizadas de base de datos
- Carga diferida de componentes

### Personalización
- Colores corporativos configurables en CSS
- Logos y branding personalizables
- Campos adicionales en modelos
- Reportes personalizados

## Mantenimiento

### Backup de Base de Datos
```bash
# Crear backup
mysqldump -u root -p crm_canaco > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
mysql -u root -p crm_canaco < backup_20240101_120000.sql
```

### Logs del Sistema
Los logs se encuentran en:
- Apache: `/var/log/apache2/crm-canaco_error.log`
- PHP: Configurado en `.htaccess`

### Actualizaciones
```bash
cd /var/www/html/crmcanaco
sudo git pull origin main
sudo chown -R www-data:www-data .
```

## Soporte y Desarrollo

### Desarrollo Local
Para desarrollo, usar:
```bash
# Servidor PHP integrado (solo para desarrollo)
php -S localhost:8000 -t /var/www/html/crmcanaco
```

### Contribuciones
1. Fork el repositorio
2. Crear branch para feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push al branch (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

## Licencia

Este proyecto es propiedad de la Cámara de Comercio de Querétaro.

## Contacto

- **Organización**: Cámara de Comercio de Querétaro
- **Website**: https://www.camaradecomercioqro.mx/
- **Soporte Técnico**: Contactar al administrador del sistema

---

*Desarrollado específicamente para las necesidades de la Cámara de Comercio de Querétaro*

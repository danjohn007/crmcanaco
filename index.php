<?php
/**
 * Main Entry Point - CRM CANACO
 * Simple Router for PHP without frameworks
 */

// Include configuration
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';

// Initialize authentication controller
$auth = new AuthController();

// Get the requested path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');

// Remove base path if running in subdirectory
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $requestUri = substr($requestUri, strlen($basePath));
}

// If empty, set to home
if (empty($requestUri)) {
    $requestUri = '/';
}

// Route handling
switch ($requestUri) {
    case '/':
        // Redirect to dashboard if logged in, otherwise to login
        if ($auth->isLoggedIn()) {
            header('Location: /dashboard');
        } else {
            header('Location: /login');
        }
        exit;
        
    case '/login':
        if ($auth->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        $auth->login();
        break;
        
    case '/logout':
        $auth->logout();
        break;
        
    case '/dashboard':
        $auth->requireLogin();
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case '/prospects':
        $auth->requireLogin();
        require_once __DIR__ . '/controllers/ProspectController.php';
        $controller = new ProspectController();
        
        $action = $_GET['action'] ?? 'index';
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $id = $_GET['id'] ?? null;
                $controller->edit($id);
                break;
            case 'view':
                $id = $_GET['id'] ?? null;
                $controller->view($id);
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                $controller->delete($id);
                break;
            default:
                $controller->index();
        }
        break;
        
    case '/events':
        $auth->requireLogin();
        require_once __DIR__ . '/controllers/EventController.php';
        $controller = new EventController();
        
        $action = $_GET['action'] ?? 'index';
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $id = $_GET['id'] ?? null;
                $controller->edit($id);
                break;
            case 'view':
                $id = $_GET['id'] ?? null;
                $controller->view($id);
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                $controller->delete($id);
                break;
            default:
                $controller->index();
        }
        break;
        
    case '/agenda':
        $auth->requireLogin();
        require_once __DIR__ . '/controllers/AgendaController.php';
        $controller = new AgendaController();
        
        $action = $_GET['action'] ?? 'index';
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $id = $_GET['id'] ?? null;
                $controller->edit($id);
                break;
            case 'complete':
                $id = $_GET['id'] ?? null;
                $controller->complete($id);
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                $controller->delete($id);
                break;
            default:
                $controller->index();
        }
        break;
        
    case '/users':
        $auth->requireRole('jefe_afiliadores'); // Only jefe_afiliadores and admin
        require_once __DIR__ . '/controllers/UserController.php';
        $controller = new UserController();
        
        $action = $_GET['action'] ?? 'index';
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $id = $_GET['id'] ?? null;
                $controller->edit($id);
                break;
            case 'delete':
                $id = $_GET['id'] ?? null;
                $controller->delete($id);
                break;
            default:
                $controller->index();
        }
        break;
        
    case '/reports':
        $auth->requireRole('jefe_afiliadores'); // Only jefe_afiliadores and admin
        require_once __DIR__ . '/controllers/ReportsController.php';
        $controller = new ReportsController();
        $controller->index();
        break;
        
    case '/profile':
        $auth->requireLogin();
        require_once __DIR__ . '/controllers/ProfileController.php';
        $controller = new ProfileController();
        $controller->index();
        break;
        
    case '/search':
        $auth->requireLogin();
        require_once __DIR__ . '/controllers/SearchController.php';
        $controller = new SearchController();
        $controller->index();
        break;
        
    // API endpoints
    case (preg_match('/^\/api\//', $requestUri) ? true : false):
        $auth->requireLogin();
        require_once __DIR__ . '/controllers/ApiController.php';
        $controller = new ApiController();
        $controller->handleRequest($requestUri, $_SERVER['REQUEST_METHOD']);
        break;
        
    default:
        // 404 Not Found
        header('HTTP/1.1 404 Not Found');
        $title = 'Página no encontrada';
        $content = '
            <div class="container text-center mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                        <h1 class="h2">Página no encontrada</h1>
                        <p class="text-muted mb-4">La página que buscas no existe o ha sido movida.</p>
                        <a href="/dashboard" class="btn btn-canaco">
                            <i class="fas fa-home me-2"></i>Volver al inicio
                        </a>
                    </div>
                </div>
            </div>';
        include __DIR__ . '/views/layouts/main.php';
        break;
}
?>
<?php
/**
 * Dashboard Controller
 * CRM Cámara de Comercio de Querétaro
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Prospect.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Agenda.php';

class DashboardController {
    private $userModel;
    private $prospectModel;
    private $eventModel;
    private $agendaModel;

    public function __construct() {
        $this->userModel = new User();
        $this->prospectModel = new Prospect();
        $this->eventModel = new Event();
        $this->agendaModel = new Agenda();
    }

    public function index() {
        $currentUser = $this->userModel->findById($_SESSION['user_id']);
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats($currentUser);
        
        // Get recent activities
        $recentProspects = $this->prospectModel->getRecentProspects(5);
        $upcomingEvents = $this->eventModel->getUpcomingEvents(5);
        $todayAgenda = $this->agendaModel->getTodayAgenda($_SESSION['user_id']);
        $pendingTasks = $this->agendaModel->getPendingTasks($_SESSION['user_id'], 10);
        
        // Get notifications
        $notifications = $this->getRecentNotifications($_SESSION['user_id']);
        
        // Customer journey overview
        $journeyStats = $this->prospectModel->getCustomerJourneyStats();
        
        include __DIR__ . '/../views/dashboard/index.php';
    }

    private function getDashboardStats($currentUser) {
        $stats = [];
        
        // Total prospects
        $stats['total_prospects'] = $this->prospectModel->getTotalCount();
        
        // New prospects this month
        $stats['new_prospects_month'] = $this->prospectModel->getNewThisMonth();
        
        // Active members
        $stats['active_members'] = $this->prospectModel->getActiveMembersCount();
        
        // Upcoming events
        $stats['upcoming_events'] = $this->eventModel->getUpcomingCount();
        
        // Pending tasks
        $stats['pending_tasks'] = $this->agendaModel->getPendingTasksCount($_SESSION['user_id']);
        
        // Completed tasks today
        $stats['completed_today'] = $this->agendaModel->getCompletedTodayCount($_SESSION['user_id']);
        
        // Monthly affiliations
        $stats['monthly_affiliations'] = $this->prospectModel->getMonthlyAffiliations();
        
        // SIEM registrations this month
        $stats['siem_month'] = $this->prospectModel->getSIEMRegistrationsMonth();
        
        // User-specific stats based on role
        if ($currentUser['role'] === 'afiliador') {
            $stats['my_prospects'] = $this->prospectModel->getCountByUser($_SESSION['user_id']);
            $stats['my_completed_tasks'] = $this->agendaModel->getCompletedTasksCount($_SESSION['user_id']);
        }
        
        return $stats;
    }

    private function getRecentNotifications($userId) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 5";
        return $db->fetchAll($sql, [$userId]);
    }
}
?>
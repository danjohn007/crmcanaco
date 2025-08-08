<?php
/**
 * Agenda Model
 * CRM Cámara de Comercio de Querétaro
 */

require_once __DIR__ . '/Database.php';

class Agenda {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($userId = null, $limit = null) {
        $sql = "SELECT a.*, p.commercial_name, p.contact_name,
                       u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name,
                       c.first_name as created_by_first, c.last_name as created_by_last,
                       CONCAT(c.first_name, ' ', c.last_name) as created_by_name
                FROM agenda a 
                LEFT JOIN prospects p ON a.prospect_id = p.id
                LEFT JOIN users u ON a.assigned_to = u.id
                LEFT JOIN users c ON a.created_by = c.id";
        
        $params = [];
        if ($userId) {
            $sql .= " WHERE a.assigned_to = ?";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY a.scheduled_date ASC";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql, $params);
    }

    public function findById($id) {
        $sql = "SELECT a.*, p.commercial_name, p.contact_name, p.phone, p.email,
                       u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name,
                       c.first_name as created_by_first, c.last_name as created_by_last,
                       CONCAT(c.first_name, ' ', c.last_name) as created_by_name
                FROM agenda a 
                LEFT JOIN prospects p ON a.prospect_id = p.id
                LEFT JOIN users u ON a.assigned_to = u.id
                LEFT JOIN users c ON a.created_by = c.id
                WHERE a.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create($data) {
        $data['created_by'] = $_SESSION['user_id'];
        return $this->db->insert('agenda', $data);
    }

    public function update($id, $data) {
        return $this->db->update('agenda', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('agenda', 'id = ?', [$id]);
    }

    public function getTodayAgenda($userId) {
        $sql = "SELECT a.*, p.commercial_name, p.contact_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name
                FROM agenda a 
                LEFT JOIN prospects p ON a.prospect_id = p.id
                LEFT JOIN users u ON a.assigned_to = u.id
                WHERE a.assigned_to = ? 
                AND DATE(a.scheduled_date) = CURRENT_DATE()
                ORDER BY a.scheduled_date ASC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getPendingTasks($userId, $limit = null) {
        $sql = "SELECT a.*, p.commercial_name, p.contact_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name
                FROM agenda a 
                LEFT JOIN prospects p ON a.prospect_id = p.id
                LEFT JOIN users u ON a.assigned_to = u.id
                WHERE a.assigned_to = ? 
                AND a.status = 'pendiente'
                AND a.scheduled_date >= NOW()
                ORDER BY a.scheduled_date ASC";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getOverdueTasks($userId) {
        $sql = "SELECT a.*, p.commercial_name, p.contact_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name
                FROM agenda a 
                LEFT JOIN prospects p ON a.prospect_id = p.id
                LEFT JOIN users u ON a.assigned_to = u.id
                WHERE a.assigned_to = ? 
                AND a.status = 'pendiente'
                AND a.scheduled_date < NOW()
                ORDER BY a.scheduled_date ASC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getPendingTasksCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM agenda 
                WHERE assigned_to = ? AND status = 'pendiente'";
        $result = $this->db->fetchOne($sql, [$userId]);
        return $result['count'];
    }

    public function getCompletedTodayCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM agenda 
                WHERE assigned_to = ? 
                AND status = 'completada' 
                AND DATE(completed_date) = CURRENT_DATE()";
        $result = $this->db->fetchOne($sql, [$userId]);
        return $result['count'];
    }

    public function getCompletedTasksCount($userId, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT COUNT(*) as count FROM agenda 
                WHERE assigned_to = ? AND status = 'completada'";
        $params = [$userId];
        
        if ($dateFrom) {
            $sql .= " AND completed_date >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $sql .= " AND completed_date <= ?";
            $params[] = $dateTo;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'];
    }

    public function markCompleted($id, $notes = '', $nextAction = '', $nextActionDate = null) {
        $data = [
            'status' => 'completada',
            'completed_date' => date('Y-m-d H:i:s'),
            'notes' => $notes
        ];
        
        if (!empty($nextAction)) {
            $data['next_action'] = $nextAction;
        }
        
        if ($nextActionDate) {
            $data['next_action_date'] = $nextActionDate;
        }
        
        return $this->update($id, $data);
    }

    public function markCancelled($id, $reason = '') {
        $data = [
            'status' => 'cancelada',
            'notes' => $reason
        ];
        return $this->update($id, $data);
    }

    public function getTasksByProspect($prospectId) {
        $sql = "SELECT a.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name
                FROM agenda a 
                LEFT JOIN users u ON a.assigned_to = u.id
                WHERE a.prospect_id = ?
                ORDER BY a.scheduled_date DESC";
        return $this->db->fetchAll($sql, [$prospectId]);
    }

    public function getTasksByDateRange($userId, $startDate, $endDate) {
        $sql = "SELECT a.*, p.commercial_name, p.contact_name
                FROM agenda a 
                LEFT JOIN prospects p ON a.prospect_id = p.id
                WHERE a.assigned_to = ?
                AND a.scheduled_date >= ? 
                AND a.scheduled_date <= ?
                ORDER BY a.scheduled_date ASC";
        return $this->db->fetchAll($sql, [$userId, $startDate, $endDate]);
    }

    public function getTasksByActionType($userId, $actionType) {
        $sql = "SELECT a.*, p.commercial_name, p.contact_name
                FROM agenda a 
                LEFT JOIN prospects p ON a.prospect_id = p.id
                WHERE a.assigned_to = ? AND a.action_type = ?
                ORDER BY a.scheduled_date ASC";
        return $this->db->fetchAll($sql, [$userId, $actionType]);
    }

    public function createFollowUpTask($prospectId, $title, $actionType, $scheduledDate, $description = '') {
        $data = [
            'title' => $title,
            'description' => $description,
            'action_type' => $actionType,
            'scheduled_date' => $scheduledDate,
            'prospect_id' => $prospectId,
            'assigned_to' => $_SESSION['user_id'],
            'status' => 'pendiente'
        ];
        
        return $this->create($data);
    }

    public function getUserTaskStats($userId, $dateFrom = null, $dateTo = null) {
        $dateCondition = '';
        $params = [$userId];
        
        if ($dateFrom && $dateTo) {
            $dateCondition = " AND scheduled_date BETWEEN ? AND ?";
            $params[] = $dateFrom;
            $params[] = $dateTo;
        }
        
        $sql = "SELECT 
                    status,
                    action_type,
                    COUNT(*) as count
                FROM agenda 
                WHERE assigned_to = ? $dateCondition
                GROUP BY status, action_type";
        
        return $this->db->fetchAll($sql, $params);
    }
}
?>
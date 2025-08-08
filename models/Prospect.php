<?php
/**
 * Prospect Model
 * CRM Cámara de Comercio de Querétaro
 */

require_once __DIR__ . '/Database.php';

class Prospect {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT p.*, u.first_name, u.last_name, 
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name
                FROM prospects p 
                LEFT JOIN users u ON p.assigned_to = u.id 
                ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db->fetchAll($sql);
    }

    public function findById($id) {
        $sql = "SELECT p.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name,
                       c.first_name as created_by_name, c.last_name as created_by_lastname
                FROM prospects p 
                LEFT JOIN users u ON p.assigned_to = u.id 
                LEFT JOIN users c ON p.created_by = c.id
                WHERE p.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create($data) {
        $data['created_by'] = $_SESSION['user_id'];
        return $this->db->insert('prospects', $data);
    }

    public function update($id, $data) {
        return $this->db->update('prospects', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('prospects', 'id = ?', [$id]);
    }

    public function search($query) {
        $searchTerm = "%$query%";
        $sql = "SELECT p.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name
                FROM prospects p 
                LEFT JOIN users u ON p.assigned_to = u.id 
                WHERE p.commercial_name LIKE ? 
                   OR p.contact_name LIKE ? 
                   OR p.rfc LIKE ? 
                   OR p.phone LIKE ? 
                   OR p.email LIKE ?
                   OR p.address LIKE ?
                ORDER BY p.commercial_name";
        
        return $this->db->fetchAll($sql, [
            $searchTerm, $searchTerm, $searchTerm, 
            $searchTerm, $searchTerm, $searchTerm
        ]);
    }

    public function getByAssignedUser($userId) {
        $sql = "SELECT p.*, u.first_name, u.last_name
                FROM prospects p 
                LEFT JOIN users u ON p.assigned_to = u.id 
                WHERE p.assigned_to = ?
                ORDER BY p.created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getRecentProspects($limit = 10) {
        $sql = "SELECT p.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_name
                FROM prospects p 
                LEFT JOIN users u ON p.assigned_to = u.id 
                ORDER BY p.created_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as count FROM prospects";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }

    public function getNewThisMonth() {
        $sql = "SELECT COUNT(*) as count FROM prospects 
                WHERE strftime('%m', created_at) = strftime('%m', 'now') 
                AND strftime('%Y', created_at) = strftime('%Y', 'now')";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }

    public function getActiveMembersCount() {
        $sql = "SELECT COUNT(*) as count FROM prospects 
                WHERE is_member = 1 
                AND (membership_expiry IS NULL OR membership_expiry >= date('now'))";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }

    public function getCountByUser($userId) {
        $sql = "SELECT COUNT(*) as count FROM prospects WHERE assigned_to = ?";
        $result = $this->db->fetchOne($sql, [$userId]);
        return $result['count'];
    }

    public function updateCustomerJourneyStage($id, $newStage, $notes = '') {
        // Get current stage
        $prospect = $this->findById($id);
        if (!$prospect) return false;
        
        $oldStage = $prospect['customer_journey_stage'];
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Update prospect stage
            $this->update($id, ['customer_journey_stage' => $newStage]);
            
            // Insert into history
            $historyData = [
                'prospect_id' => $id,
                'previous_stage' => $oldStage,
                'current_stage' => $newStage,
                'changed_by' => $_SESSION['user_id'],
                'notes' => $notes
            ];
            $this->db->insert('customer_journey_history', $historyData);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function getCustomerJourneyStats() {
        $sql = "SELECT customer_journey_stage, COUNT(*) as count 
                FROM prospects 
                GROUP BY customer_journey_stage";
        $results = $this->db->fetchAll($sql);
        
        $stats = [];
        foreach ($results as $result) {
            $stats[$result['customer_journey_stage']] = $result['count'];
        }
        
        return $stats;
    }

    public function getMonthlyAffiliations() {
        $sql = "SELECT COUNT(*) as count FROM prospects 
                WHERE is_member = 1 
                AND strftime('%m', created_at) = strftime('%m', 'now') 
                AND strftime('%Y', created_at) = strftime('%Y', 'now')";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }

    public function getSIEMRegistrationsMonth() {
        $sql = "SELECT COUNT(*) as count FROM siem_registrations 
                WHERE strftime('%m', registration_date) = strftime('%m', 'now') 
                AND strftime('%Y', registration_date) = strftime('%Y', 'now')";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }

    public function getByMembershipType($type) {
        $sql = "SELECT * FROM prospects WHERE membership_type = ? AND is_member = 1";
        return $this->db->fetchAll($sql, [$type]);
    }

    public function getByBusinessCategory($category) {
        $sql = "SELECT * FROM prospects WHERE business_category = ?";
        return $this->db->fetchAll($sql, [$category]);
    }

    public function getByCompanySize($size) {
        $sql = "SELECT * FROM prospects WHERE company_size = ?";
        return $this->db->fetchAll($sql, [$size]);
    }

    public function getCustomerJourneyHistory($prospectId) {
        $sql = "SELECT h.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as changed_by_name
                FROM customer_journey_history h
                LEFT JOIN users u ON h.changed_by = u.id
                WHERE h.prospect_id = ?
                ORDER BY h.created_at DESC";
        return $this->db->fetchAll($sql, [$prospectId]);
    }

    public function addSIEMRegistration($prospectId, $registrationNumber, $notes = '') {
        $data = [
            'prospect_id' => $prospectId,
            'registration_number' => $registrationNumber,
            'registered_by' => $_SESSION['user_id'],
            'registration_date' => date('Y-m-d'),
            'notes' => $notes
        ];
        return $this->db->insert('siem_registrations', $data);
    }
}
?>
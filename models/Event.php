<?php
/**
 * Event Model
 * CRM Cámara de Comercio de Querétaro
 */

require_once __DIR__ . '/Database.php';

class Event {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($limit = null) {
        $sql = "SELECT e.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as created_by_name,
                       (SELECT COUNT(*) FROM event_attendees ea WHERE ea.event_id = e.id) as attendee_count
                FROM events e 
                LEFT JOIN users u ON e.created_by = u.id 
                ORDER BY e.start_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->fetchAll($sql);
    }

    public function findById($id) {
        $sql = "SELECT e.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM events e 
                LEFT JOIN users u ON e.created_by = u.id 
                WHERE e.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create($data) {
        $data['created_by'] = $_SESSION['user_id'];
        return $this->db->insert('events', $data);
    }

    public function update($id, $data) {
        return $this->db->update('events', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('events', 'id = ?', [$id]);
    }

    public function getUpcomingEvents($limit = 10) {
        $sql = "SELECT e.*, u.first_name, u.last_name,
                       (u.first_name || ' ' || u.last_name) as created_by_name,
                       (SELECT COUNT(*) FROM event_attendees ea WHERE ea.event_id = e.id) as attendee_count
                FROM events e 
                LEFT JOIN users u ON e.created_by = u.id 
                WHERE e.start_date >= datetime('now')
                ORDER BY e.start_date ASC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }

    public function getUpcomingCount() {
        $sql = "SELECT COUNT(*) as count FROM events WHERE start_date >= datetime('now')";
        $result = $this->db->fetchOne($sql);
        return $result['count'];
    }

    public function getEventsByMonth($year, $month) {
        $sql = "SELECT * FROM events 
                WHERE YEAR(start_date) = ? AND MONTH(start_date) = ?
                ORDER BY start_date ASC";
        return $this->db->fetchAll($sql, [$year, $month]);
    }

    public function getEventsByDateRange($startDate, $endDate) {
        $sql = "SELECT e.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM events e 
                LEFT JOIN users u ON e.created_by = u.id 
                WHERE e.start_date >= ? AND e.end_date <= ?
                ORDER BY e.start_date ASC";
        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }

    public function addAttendee($eventId, $data) {
        $data['event_id'] = $eventId;
        return $this->db->insert('event_attendees', $data);
    }

    public function getAttendees($eventId) {
        $sql = "SELECT ea.*, p.commercial_name, p.contact_name
                FROM event_attendees ea
                LEFT JOIN prospects p ON ea.prospect_id = p.id
                WHERE ea.event_id = ?
                ORDER BY ea.registered_at DESC";
        return $this->db->fetchAll($sql, [$eventId]);
    }

    public function markAttendance($attendeeId, $attended = true) {
        return $this->db->update('event_attendees', 
                                ['attended' => $attended ? 1 : 0], 
                                'id = ?', [$attendeeId]);
    }

    public function getEventStats($eventId) {
        $sql = "SELECT 
                    COUNT(*) as total_registered,
                    SUM(attended) as total_attended,
                    COUNT(*) - SUM(attended) as no_shows
                FROM event_attendees 
                WHERE event_id = ?";
        return $this->db->fetchOne($sql, [$eventId]);
    }

    public function getEventsByType($type) {
        $sql = "SELECT e.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM events e 
                LEFT JOIN users u ON e.created_by = u.id 
                WHERE e.event_type = ?
                ORDER BY e.start_date DESC";
        return $this->db->fetchAll($sql, [$type]);
    }

    public function searchEvents($query) {
        $searchTerm = "%$query%";
        $sql = "SELECT e.*, u.first_name, u.last_name,
                       CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM events e 
                LEFT JOIN users u ON e.created_by = u.id 
                WHERE e.title LIKE ? 
                   OR e.description LIKE ? 
                   OR e.location LIKE ?
                ORDER BY e.start_date DESC";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
}
?>
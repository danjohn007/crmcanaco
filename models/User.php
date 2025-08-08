<?php
/**
 * User Model
 * CRM Cámara de Comercio de Querétaro
 */

require_once __DIR__ . '/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function authenticate($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND is_active = 1";
        $user = $this->db->fetchOne($sql, [$username]);

        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->updateLastLogin($user['id']);
            return $user;
        }
        return false;
    }

    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        return $this->db->fetchOne($sql, [$username]);
    }

    public function getAllUsers() {
        $sql = "SELECT id, username, email, first_name, last_name, role, is_active, last_login, created_at 
                FROM users ORDER BY first_name, last_name";
        return $this->db->fetchAll($sql);
    }

    public function getUsersByRole($role) {
        $sql = "SELECT id, username, email, first_name, last_name, role, is_active 
                FROM users WHERE role = ? AND is_active = 1 ORDER BY first_name, last_name";
        return $this->db->fetchAll($sql, [$role]);
    }

    public function create($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->insert('users', $data);
    }

    public function update($id, $data) {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        // Soft delete - deactivate user
        return $this->db->update('users', ['is_active' => 0], 'id = ?', [$id]);
    }

    public function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = datetime('now') WHERE id = ?";
        return $this->db->query($sql, [$userId]);
    }

    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->db->update('users', ['password' => $hashedPassword], 'id = ?', [$userId]);
    }

    public function hasPermission($userRole, $requiredRole) {
        $roleHierarchy = [
            'admin' => 10,
            'jefe_afiliadores' => 8,
            'mesa_directiva' => 7,
            'consejero' => 6,
            'contabilidad' => 5,
            'afiliador' => 4,
            'administrativo' => 3,
            'auxiliar_contabilidad' => 2,
            'atencion_clientes' => 1
        ];

        $userLevel = $roleHierarchy[$userRole] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;

        return $userLevel >= $requiredLevel;
    }

    public function getRoleName($role) {
        $roleNames = [
            'admin' => 'Administrador',
            'jefe_afiliadores' => 'Jefe de Afiliadores',
            'afiliador' => 'Afiliador',
            'administrativo' => 'Administrativo',
            'consejero' => 'Consejero',
            'mesa_directiva' => 'Mesa Directiva',
            'contabilidad' => 'Contabilidad',
            'auxiliar_contabilidad' => 'Auxiliar de Contabilidad',
            'atencion_clientes' => 'Atención a Clientes'
        ];

        return $roleNames[$role] ?? $role;
    }

    public function getFullName($user) {
        return trim($user['first_name'] . ' ' . $user['last_name']);
    }
}
?>
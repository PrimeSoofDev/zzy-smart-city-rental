<?php
class User extends Model {
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$data['username'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT)]);
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function assignRole($userId, $roleName) {
        $role = $this->db->prepare("SELECT id FROM roles WHERE role_name = ?");
        $role->execute([$roleName]);
        $roleId = $role->fetchColumn();

        $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $roleId]);
    }

    public function getRole($userId) {
        $stmt = $this->db->prepare("SELECT r.role_name FROM roles r JOIN user_roles ur ON r.id = ur.role_id WHERE ur.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT id, username, email, phone, status, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

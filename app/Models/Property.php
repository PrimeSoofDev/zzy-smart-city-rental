<?php
class Property extends Model {
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO properties (landlord_id, title, description, address, price, rooms, bathrooms, property_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['landlord_id'], $data['title'], $data['description'],
            $data['address'], $data['price'], $data['rooms'],
            $data['bathrooms'], $data['type']
        ]);
    }

    public function getAllApproved() {
        $stmt = $this->db->query("SELECT * FROM properties WHERE status = 'approved'");
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE properties SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM properties WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

<?php
class Property extends Model {
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO properties (landlord_id, title, description, address, price, rooms, bathrooms, property_type, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['landlord_id'], $data['title'], $data['description'],
            $data['address'], $data['price'], $data['rooms'],
            $data['bathrooms'], $data['type'], $data['latitude'] ?? null, $data['longitude'] ?? null
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

    public function getPropertiesInBounds($north, $south, $east, $west) {
        $stmt = $this->db->prepare("SELECT * FROM properties WHERE status = 'approved' AND latitude BETWEEN ? AND ? AND longitude BETWEEN ? AND ?");
        $stmt->execute([$south, $north, $west, $east]);
        return $stmt->fetchAll();
    }

    public function updateCoordinates($id, $lat, $lng) {
        $stmt = $this->db->prepare("UPDATE properties SET latitude = ?, longitude = ? WHERE id = ?");
        return $stmt->execute([$lat, $lng, $id]);
    }

    public function getSuggested($type, $excludeId, $limit = 3) {
        $stmt = $this->db->prepare("SELECT * FROM properties WHERE property_type = ? AND id != ? AND status = 'approved' ORDER BY RAND() LIMIT ?");
        // PDO doesn't like LIMIT as a bound param unless using bindValue with PARAM_INT
        $stmt->bindValue(1, $type);
        $stmt->bindValue(2, $excludeId, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

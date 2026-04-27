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
        $stmt = $this->db->query("
            SELECT p.*, pi.image_url as primary_image
            FROM properties p
            LEFT JOIN property_images pi ON p.id = pi.property_id AND pi.is_primary = 1
            WHERE p.status = 'approved'
        ");
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

    public function getPropertiesInBounds($north, $south, $east, $west, $query = null) {
        $sql = "SELECT * FROM properties WHERE status = 'approved' AND (
                    (latitude BETWEEN ? AND ? AND longitude BETWEEN ? AND ?)
                ";
        $params = [$south, $north, $west, $east];

        if ($query) {
            $sql .= " OR (title LIKE ? OR address LIKE ? OR description LIKE ?)";
            $searchTerm = "%$query%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= ")";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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

    public function search($location = null, $type = null, $priceRange = null) {
        $sql = "
            SELECT p.*, pi.image_url as primary_image
            FROM properties p
            LEFT JOIN property_images pi ON p.id = pi.property_id AND pi.is_primary = 1
            WHERE p.status = 'approved'
        ";
        $params = [];

        if ($location) {
            $sql .= " AND p.address LIKE ?";
            $params[] = "%$location%";
        }

        if ($type && $type !== 'All Types') {
            $sql .= " AND p.property_type = ?";
            $params[] = strtolower($type);
        }

        if ($priceRange && $priceRange !== 'Any Price') {
            if (strpos($priceRange, '-') !== false) {
                list($min, $max) = explode('-', $priceRange);
                $minVal = (float)str_replace(['₦', 'k', 'M', ' ', ','], ['', '000', '000000', '', ''], $min);
                $maxVal = (float)str_replace(['₦', 'k', 'M', ' ', ','], ['', '000', '000000', '', ''], $max);
                $sql .= " AND p.price BETWEEN ? AND ?";
                $params[] = $minVal;
                $params[] = $maxVal;
            } elseif (strpos($priceRange, '+') !== false) {
                $minVal = (float)str_replace(['₦', 'k', 'M', '+', ' ', ','], ['', '000', '000000', '', '', ''], $priceRange);
                $sql .= " AND p.price >= ?";
                $params[] = $minVal;
            }
        }

        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function getByLandlord($landlordId) {
        $stmt = $this->db->prepare("
            SELECT p.*, pi.image_url as primary_image
            FROM properties p
            LEFT JOIN property_images pi ON p.id = pi.property_id AND pi.is_primary = 1
            WHERE p.landlord_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$landlordId]);
        return $stmt->fetchAll();
    }
}

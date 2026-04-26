<?php

class Review {
    public static function create($data) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO reviews (reviewer_id, reviewee_id, request_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['reviewer_id'],
            $data['reviewee_id'],
            $data['request_id'],
            $data['rating'],
            $data['comment']
        ]);
    }

    public static function getByReviewee($revieweeId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT r.*, u.name as reviewer_name FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewee_id = ? AND r.status = 'active' ORDER BY r.created_at DESC");
        $stmt->execute([$revieweeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("
            SELECT r.*, 
                   u1.name as reviewer_name, u1.role as reviewer_role,
                   u2.name as reviewee_name, u2.role as reviewee_role,
                   p.title as property_title
            FROM reviews r
            JOIN users u1 ON r.reviewer_id = u1.id
            JOIN users u2 ON r.reviewee_id = u2.id
            LEFT JOIN rental_requests rr ON r.request_id = rr.id
            LEFT JOIN properties p ON rr.property_id = p.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateStatus($id, $status) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE reviews SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}

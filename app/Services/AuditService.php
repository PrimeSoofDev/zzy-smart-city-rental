<?php

class AuditService {
    public static function log($action, $entityType = null, $entityId = null) {
        $db = Database::getInstance()->getConnection();
        
        $userId = $_SESSION['user_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        $stmt = $db->prepare("INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            $ipAddress
        ]);
    }
}

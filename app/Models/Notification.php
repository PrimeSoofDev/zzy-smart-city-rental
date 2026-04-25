<?php
// app/Models/Notification.php

class Notification {
    public static function send($userId, $message) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        return $stmt->execute([$userId, $message]);
    }

    public static function getUnreadCount($userId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public static function getRecent($userId, $limit = 5) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        // PDO needs limit as INT or bindParam
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAll($userId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function markAsRead($userId, $notificationId = null) {
        $db = Database::getInstance()->getConnection();
        if ($notificationId) {
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND id = ?");
            return $stmt->execute([$userId, $notificationId]);
        } else {
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
            return $stmt->execute([$userId]);
        }
    }
}

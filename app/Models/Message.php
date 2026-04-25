<?php
// app/Models/Message.php

class Message {
    public static function send($senderId, $receiverId, $message) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        return $stmt->execute([$senderId, $receiverId, $message]);
    }

    public static function getThread($user1, $user2) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT m.*, u.username as sender_name 
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC
        ");
        $stmt->execute([$user1, $user2, $user2, $user1]);
        return $stmt->fetchAll();
    }

    public static function getUnreadCount($userId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    public static function markThreadAsRead($senderId, $receiverId) {
        // Marks all messages sent BY $senderId TO $receiverId as read
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
        return $stmt->execute([$senderId, $receiverId]);
    }
}

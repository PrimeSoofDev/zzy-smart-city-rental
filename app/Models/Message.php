<?php
// app/Models/Message.php

class Message {
    public static function send($senderId, $receiverId, $message, $type = 'text', $attachmentId = null, $profileSnapshot = null) {
        $db = Database::getInstance()->getConnection();
        $attachmentId = !empty($attachmentId) ? $attachmentId : null;
        
        if ($profileSnapshot === null) {
            $profile = [];
            if (isset($_SESSION['username'])) $profile['username'] = $_SESSION['username'];
            if (isset($_SESSION['avatar'])) $profile['avatar'] = $_SESSION['avatar'];
            $profileSnapshot = !empty($profile) ? json_encode($profile) : null;
        }

        $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, message, type, attachment_id, likes, reactions, voice_note_path, profile_snapshot) VALUES (?, ?, ?, ?, ?, 0, NULL, NULL, ?)");
        return $stmt->execute([$senderId, $receiverId, $message, $type, $attachmentId, $profileSnapshot]);
    }

    public static function createAttachment($filePath, $fileName, $fileType, $fileSize) {
        $db = Database::getInstance()->getConnection();
        // Insert with file_type_mime matching file_type to satisfy the NOT NULL constraint
        $stmt = $db->prepare("INSERT INTO attachments (file_path, file_name, file_type, file_type_mime, file_size) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$filePath, $fileName, $fileType, $fileType, $fileSize]);
        return $db->lastInsertId();
    }

    public static function getThread($user1, $user2) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT m.*, u.username as sender_name, a.file_path, a.file_name, a.file_type, a.file_size, m.profile_snapshot
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            LEFT JOIN attachments a ON m.attachment_id = a.id
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

    public static function addLike($messageId) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE messages SET likes = likes + 1 WHERE id = ?");
        return $stmt->execute([$messageId]);
    }

    public static function addEmoji($messageId, $emoji) {
        $db = Database::getInstance()->getConnection();
        // Fetch current emojis JSON array
                $stmt = $db->prepare("SELECT reactions FROM messages WHERE id = ?");
        $stmt->execute([$messageId]);
        $current = $stmt->fetchColumn();
        $emojis = $current ? json_decode($current, true) : [];
        if (!is_array($emojis)) $emojis = [];
        $emojis[] = $emoji;
        $newJson = json_encode($emojis);
                $update = $db->prepare("UPDATE messages SET reactions = ? WHERE id = ?");
        return $update->execute([$newJson, $messageId]);
    }
}


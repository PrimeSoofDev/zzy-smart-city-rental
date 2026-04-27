<?php
// app/Controllers/MessageController.php

class MessageController extends Controller {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }

        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'] ?? 'Tenant';

        $db = Database::getInstance()->getConnection();
        $contacts = [];

        // Determine Contacts based on Role
        if ($role === 'Tenant') {
            // Find properties the tenant has paid for
            $stmt = $db->prepare("
                SELECT p.landlord_id, u.username as landlord_name, 
                       a.lawyer_id, l.username as lawyer_name,
                       pv.staff_id, s.username as staff_name
                FROM rental_requests rr
                JOIN properties p ON rr.property_id = p.id
                JOIN users u ON p.landlord_id = u.id
                LEFT JOIN agreements a ON a.request_id = rr.id
                LEFT JOIN users l ON a.lawyer_id = l.id
                LEFT JOIN property_verifications pv ON pv.property_id = p.id AND pv.result = 'approved'
                LEFT JOIN users s ON pv.staff_id = s.id
                WHERE rr.tenant_id = ? AND rr.status IN ('paid', 'completed')
            ");
            $stmt->execute([$userId]);
            $rentals = $stmt->fetchAll();

            foreach ($rentals as $r) {
                if ($r['landlord_id']) $contacts[$r['landlord_id']] = ['id' => $r['landlord_id'], 'name' => $r['landlord_name'], 'type' => 'Landlord'];
                if ($r['lawyer_id']) $contacts[$r['lawyer_id']] = ['id' => $r['lawyer_id'], 'name' => $r['lawyer_name'], 'type' => 'Lawyer'];
                if ($r['staff_id']) $contacts[$r['staff_id']] = ['id' => $r['staff_id'], 'name' => $r['staff_name'], 'type' => 'Staff'];
            }
        } elseif ($role === 'Landlord') {
            // Find tenants who rented their properties
            $stmt = $db->prepare("
                SELECT rr.tenant_id, t.username as tenant_name
                FROM rental_requests rr
                JOIN properties p ON rr.property_id = p.id
                JOIN users t ON rr.tenant_id = t.id
                WHERE p.landlord_id = ? AND rr.status IN ('paid', 'completed')
            ");
            $stmt->execute([$userId]);
            $tenants = $stmt->fetchAll();
            foreach ($tenants as $t) {
                if ($t['tenant_id']) $contacts[$t['tenant_id']] = ['id' => $t['tenant_id'], 'name' => $t['tenant_name'], 'type' => 'Tenant'];
            }
        } elseif ($role === 'Lawyer') {
            // Lawyer: can chat with Admin + parties in their agreements
            $adminStmt = $db->query("
                SELECT u.id, u.username FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name = 'Admin' LIMIT 1
            ");
            $admin = $adminStmt->fetch();
            if ($admin) $contacts[$admin['id']] = ['id' => $admin['id'], 'name' => $admin['username'], 'type' => 'Admin'];

            $stmt = $db->prepare("
                SELECT rr.tenant_id, t.username as tenant_name,
                       p.landlord_id, l.username as landlord_name
                FROM agreements a
                JOIN rental_requests rr ON a.request_id = rr.id
                JOIN properties p ON rr.property_id = p.id
                JOIN users t ON rr.tenant_id = t.id
                JOIN users l ON p.landlord_id = l.id
                WHERE a.lawyer_id = ?
            ");
            $stmt->execute([$userId]);
            $parties = $stmt->fetchAll();
            foreach ($parties as $p) {
                if ($p['tenant_id']) $contacts[$p['tenant_id']] = ['id' => $p['tenant_id'], 'name' => $p['tenant_name'], 'type' => 'Tenant'];
                if ($p['landlord_id']) $contacts[$p['landlord_id']] = ['id' => $p['landlord_id'], 'name' => $p['landlord_name'], 'type' => 'Landlord'];
            }
        } elseif ($role === 'Staff') {
            // Staff: can chat with Admin + tenants/landlords from their verified properties
            // Add Admin contact
            $adminStmt = $db->query("
                SELECT u.id, u.username FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name = 'Admin'
                LIMIT 1
            ");
            $admin = $adminStmt->fetch();
            if ($admin) $contacts[$admin['id']] = ['id' => $admin['id'], 'name' => $admin['username'], 'type' => 'Admin'];

            $stmt = $db->prepare("
                SELECT rr.tenant_id, t.username as tenant_name,
                       p.landlord_id, l.username as landlord_name
                FROM property_verifications pv
                JOIN properties p ON pv.property_id = p.id
                JOIN rental_requests rr ON rr.property_id = p.id
                JOIN users t ON rr.tenant_id = t.id
                JOIN users l ON p.landlord_id = l.id
                WHERE pv.staff_id = ? AND rr.status IN ('paid', 'completed')
            ");
            $stmt->execute([$userId]);
            $parties = $stmt->fetchAll();
            foreach ($parties as $p) {
                if ($p['tenant_id']) $contacts[$p['tenant_id']] = ['id' => $p['tenant_id'], 'name' => $p['tenant_name'], 'type' => 'Tenant'];
                if ($p['landlord_id']) $contacts[$p['landlord_id']] = ['id' => $p['landlord_id'], 'name' => $p['landlord_name'], 'type' => 'Landlord'];
            }
        } elseif ($role === 'Admin') {
            // Admin: can chat with all Staff and all Lawyers
            $internalStmt = $db->query("
                SELECT u.id, u.username, r.role_name
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name IN ('Staff', 'Lawyer')
                ORDER BY r.role_name, u.username
            ");
            $internalUsers = $internalStmt->fetchAll();
            foreach ($internalUsers as $u) {
                $contacts[$u['id']] = [
                    'id'   => $u['id'],
                    'name' => $u['username'],
                    'type' => $u['role_name']
                ];
            }
        }

        // Sort contacts by name
        usort($contacts, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Add unread counts to contacts
        foreach ($contacts as &$contact) {
            $cStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
            $cStmt->execute([$contact['id'], $userId]);
            $contact['unread'] = $cStmt->fetchColumn();
        }

        $layoutPrefix = strtolower($role);
        if (!in_array($layoutPrefix, ['admin', 'staff', 'lawyer', 'landlord', 'tenant'])) {
            $layoutPrefix = 'tenant';
        }

        require_once "../views/layouts/{$layoutPrefix}_layout_start.php";
        $agoraAppId = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'agora_app_id'")->fetchColumn();
        $this->view('messages/index', ['contacts' => $contacts, 'userId' => $userId, 'agoraAppId' => $agoraAppId]);
        require_once "../views/layouts/{$layoutPrefix}_layout_end.php";
    }

    public function apiContacts() {
        header('Content-Type: application/json');
        
        $userId = $_SESSION['user_id'] ?? $_GET['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $role = $_SESSION['role'] ?? $_GET['role'] ?? 'Tenant';
        $db = Database::getInstance()->getConnection();
        $contacts = [];

        // Determine Contacts based on Role
        if ($role === 'Tenant') {
            $stmt = $db->prepare("
                SELECT p.landlord_id as id, u.username as name, 'Landlord' as type,
                       (SELECT message FROM messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message,
                       (SELECT created_at FROM messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_time
                FROM rental_requests rr
                JOIN properties p ON rr.property_id = p.id
                JOIN users u ON p.landlord_id = u.id
                WHERE rr.tenant_id = ? AND rr.status IN ('paid', 'completed')
                GROUP BY u.id
            ");
            $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
            $contacts = $stmt->fetchAll();
        } elseif ($role === 'Admin') {
             $stmt = $db->query("
                SELECT u.id, u.username as name, r.role_name as type
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE r.role_name IN ('Staff', 'Lawyer')
                ORDER BY u.username
            ");
            $contacts = $stmt->fetchAll();
        }

        // Add unread counts and last message defaults
        foreach ($contacts as &$contact) {
            $cStmt = $db->prepare("SELECT COUNT(*) FROM messages WHERE sender_id = ? AND receiver_id = ? AND is_read = 0");
            $cStmt->execute([$contact['id'], $userId]);
            $contact['unread'] = (int)$cStmt->fetchColumn();
            $contact['last_message'] = $contact['last_message'] ?? 'No messages yet';
            $contact['last_time'] = $contact['last_time'] ?? '';
        }

        echo json_encode($contacts);
    }
    public function fetchThread() {
        if (!isset($_SESSION['user_id'])) exit;
        $userId = $_SESSION['user_id'];
        $contactId = $_GET['contact_id'] ?? null;

        if ($contactId) {
            Message::markThreadAsRead($contactId, $userId);
            $thread = Message::getThread($userId, $contactId);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'messages' => $thread, 'user_id' => $userId]);
            exit;
        }
        echo json_encode(['success' => false]);
    }

    public function apiThread() {
        header('Content-Type: application/json');
        
        $userId = $_SESSION['user_id'] ?? $_GET['user_id'] ?? null;
        $contactId = $_GET['contact_id'] ?? null;

        if (!$userId || !$contactId) {
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
            exit;
        }

        Message::markThreadAsRead($contactId, $userId);
        $thread = Message::getThread($userId, $contactId);
        
        echo json_encode($thread);
    }
    public function send() {
        if (!isset($_SESSION['user_id'])) exit;
        $userId = $_SESSION['user_id'];
        $receiverId = $_POST['receiver_id'] ?? null;
        $message = trim($_POST['message'] ?? '');
        $type = $_POST['type'] ?? 'text';
        $attachmentId = $_POST['attachment_id'] ?? null;

        if ($receiverId && ($message !== '' || $attachmentId)) {
            if (Message::send($userId, $receiverId, $message, $type, $attachmentId)) {

                // Notification alert
                $db = Database::getInstance()->getConnection();
                $senderName = $_SESSION['username'] ?? 'Someone';
                $notifMsg = "You have a new chat message from {$senderName}.";

                // Check if already notified recently to prevent spam
                $nStmt = $db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND message = ? AND is_read = 0");
                $nStmt->execute([$receiverId, $notifMsg]);
                if ($nStmt->fetchColumn() == 0) {
                    Notification::send($receiverId, $notifMsg);
                }

                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            } else {
                error_log("Message::send failed for user " . $userId);
            }
        } else {
            error_log("Message send validation failed. receiver_id: $receiverId, message empty: " . ($message === '') . ", attachment_id: $attachmentId");
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Validation failed or database error']);
        exit;
    }

    public function uploadFile() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if (!isset($_FILES['file'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            exit;
        }

        $file = $_FILES['file'];
        $uploadDir = 'uploads/chat/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        // Security: Validate file extension and MIME type
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'audio/webm' => 'webm',
            'audio/ogg' => 'ogg',
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
            'audio/mp4' => 'mp4',
            'audio/x-m4a' => 'm4a',
            'video/webm' => 'webm',
        ];

        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
        } else {
            // Fallback for systems where fileinfo is not enabled
            $mimeType = $file['type'];
        }

        if (!array_key_exists($mimeType, $allowedTypes)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Invalid file type: ' . $mimeType]);
            exit;
        }

        $extension = $allowedTypes[$mimeType];
        $fileName = pathinfo($file['name'], PATHINFO_FILENAME);
        $uniqueName = uniqid('chat_', true) . '.' . $extension;
        $destination = $uploadDir . $uniqueName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Store browser-relative path in DB
            $dbPath = 'uploads/chat/' . $uniqueName;
            $attachmentId = Message::createAttachment(
                $dbPath,
                $file['name'],
                $mimeType,
                $file['size']
            );
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'attachment_id' => $attachmentId]);
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Upload failed']);
        exit;
    }

    public function react() {
        if (!isset($_SESSION['user_id'])) exit;
        
        $messageId = $_POST['message_id'] ?? null;
        $action = $_POST['action'] ?? null; // 'love' or 'emoji'
        $emoji = $_POST['emoji'] ?? null;
        
        if ($messageId && $action) {
            if ($action === 'love') {
                Message::addLike($messageId);
            } elseif ($action === 'emoji' && $emoji) {
                Message::addEmoji($messageId, $emoji);
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
        exit;
    }
}

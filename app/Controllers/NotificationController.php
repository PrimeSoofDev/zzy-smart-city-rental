<?php
class NotificationController extends Controller {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
        
        $userId = $_SESSION['user_id'];
        $notifications = Notification::getAll($userId);
        
        // Mark all as read when they visit the page
        Notification::markAsRead($userId);

        // We need to render the view with the correct layout based on user role
        $role = $_SESSION['role'] ?? 'Tenant';
        
        $layoutPrefix = strtolower($role);
        // Fallback for Admin, Staff, Lawyer, Landlord, Tenant
        if (!in_array($layoutPrefix, ['admin', 'staff', 'lawyer', 'landlord', 'tenant'])) {
            $layoutPrefix = 'tenant';
        }

        require_once "../views/layouts/{$layoutPrefix}_layout_start.php";
        $this->view('notifications/index', ['notifications' => $notifications]);
        require_once "../views/layouts/{$layoutPrefix}_layout_end.php";
    }

    public function markRead() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $id = $_POST['id'] ?? null;
        
        Notification::markAsRead($userId, $id);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

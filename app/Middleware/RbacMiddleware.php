<?php
class RbacMiddleware {
    public static function check($allowedRoles) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . APP_URL . "/auth/login");
            exit;
        }

        if (!in_array($_SESSION['role'], $allowedRoles)) {
            header("HTTP/1.1 403 Forbidden");
            echo "Access Denied: You do not have the required permissions.";
            exit;
        }
    }
}

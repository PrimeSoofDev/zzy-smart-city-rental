<?php
class AdminController extends Controller {

    private function renderAdminView($view, $data = []) {
        require_once "../views/layouts/admin_layout_start.php";
        $this->view('admin/pages/' . $view, $data);
        require_once "../views/layouts/admin_layout_end.php";
    }

    public function dashboard() {
        RbacMiddleware::check(['Admin']);

        $db = Database::getInstance()->getConnection();
        $totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $totalProperties = $db->query("SELECT COUNT(*) FROM properties")->fetchColumn();
        $pendingVerifications = $db->query("SELECT COUNT(*) FROM properties WHERE status = 'pending_verification'")->fetchColumn();

        $users = $db->query("
            SELECT u.id, u.username, u.email, u.status, r.role_name
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            ORDER BY u.id DESC
        ")->fetchAll();

        require_once "../views/layouts/admin_layout_start.php";
        $this->view('admin/dashboard', [
            'users' => $users,
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalProperties' => $totalProperties,
                'pendingVerifications' => $pendingVerifications
            ]
        ]);
        require_once "../views/layouts/admin_layout_end.php";
    }

    public function users() {
        RbacMiddleware::check(['Admin']);
        $role = $_GET['role'] ?? 'all';
        $db = Database::getInstance()->getConnection();

        $query = "SELECT u.id, u.username, u.email, u.status, r.role_name
                  FROM users u
                  LEFT JOIN user_roles ur ON u.id = ur.user_id
                  LEFT JOIN roles r ON ur.role_id = r.id";

        if ($role !== 'all') {
            $query .= " WHERE r.role_name = :role";
            $stmt = $db->prepare($query);
            $stmt->execute(['role' => $role]);
            $users = $stmt->fetchAll();
        } else {
            $users = $db->query($query)->fetchAll();
        }

        $this->renderAdminView('users', ['users' => $users]);
    }

    public function properties() {
        RbacMiddleware::check(['Admin']);
        $status = $_GET['status'] ?? 'all';
        $db = Database::getInstance()->getConnection();

        $query = "SELECT p.*, u.username as landlord_name
                   FROM properties p
                   JOIN users u ON p.landlord_id = u.id";

        if ($status !== 'all') {
            $query .= " WHERE p.status = :status";
            $stmt = $db->prepare($query);
            $stmt->execute(['status' => $status]);
            $properties = $stmt->fetchAll();
        } else {
            $properties = $db->query($query)->fetchAll();
        }

        $this->renderAdminView('properties', ['properties' => $properties]);
    }

    public function verifications() {
        RbacMiddleware::check(['Admin']);
        $db = Database::getInstance()->getConnection();

        $tenants = $db->query("SELECT tp.*, u.username FROM tenant_profiles tp JOIN users u ON tp.user_id = u.id WHERE tp.verification_status = 'pending'")->fetchAll();
        $landlords = $db->query("SELECT lp.*, u.username FROM landlord_profiles lp JOIN users u ON lp.user_id = u.id WHERE lp.verification_status = 'pending'")->fetchAll();

        $this->renderAdminView('verifications', [
            'tenants' => $tenants,
            'landlords' => $landlords
        ]);
    }

    public function transactions() {
        RbacMiddleware::check(['Admin']);
        $db = Database::getInstance()->getConnection();
        $transactions = $db->query("SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC")->fetchAll();

        $this->renderAdminView('transactions', ['transactions' => $transactions]);
    }

    public function logs() {
        RbacMiddleware::check(['Admin']);
        $db = Database::getInstance()->getConnection();
        $logs = $db->query("SELECT * FROM audit_logs ORDER BY created_at DESC")->fetchAll();

        $this->renderAdminView('logs', ['logs' => $logs]);
    }

    public function settings() {
        RbacMiddleware::check(['Admin']);
        $db = Database::getInstance()->getConnection();
        $settings = $db->query("SELECT * FROM system_settings")->fetchAll();

        $settingsMap = [];
        foreach($settings as $s) {
            $settingsMap[$s['setting_key']] = $s['setting_value'];
        }

        $this->renderAdminView('settings', ['settings' => $settingsMap]);
    }

    public function updateSettings() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance()->getConnection();
            foreach ($_POST['settings'] as $key => $value) {
                $stmt = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            }
            $this->redirect('admin/settings');
        }
    }

    public function addUser() {
        RbacMiddleware::check(['Admin']);
        $defaultRole = $_GET['role'] ?? 'Staff';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->sanitize($_POST['username']);
            $email = $this->sanitize($_POST['email']);
            $phone = $this->sanitize($_POST['phone']);
            $location = $this->sanitize($_POST['location']);
            $role = $_POST['role'];

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            try {
                $stmt = $db->prepare("INSERT INTO users (username, email, password, status, is_active) VALUES (?, ?, ?, 'verified', 0)");
                $stmt->execute([$username, $email, '']);
                $userId = $db->lastInsertId();

                $roleStmt = $db->prepare("SELECT id FROM roles WHERE role_name = ?");
                $roleStmt->execute([$role]);
                $roleId = $roleStmt->fetchColumn();
                $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$userId, $roleId]);

                if ($role === 'Staff') {
                    $db->prepare("INSERT INTO staff_profiles (user_id, assigned_location) VALUES (?, ?)")->execute([$userId, $location]);
                } elseif ($role === 'Lawyer') {
                    $db->prepare("INSERT INTO lawyer_profiles (user_id, assigned_location) VALUES (?, ?)")->execute([$userId, $location]);
                }

                $token = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
                $db->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)")->execute([$userId, $token, $expiresAt]);

                $db->commit();
                $resetLink = APP_URL . "/auth/set-password?token=" . $token;
                $_SESSION['success'] = "User created! Invite link: " . $resetLink;
                $this->redirect('admin/add-user');

            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Error creating user: " . $e->getMessage();
                $this->redirect('admin/add-user');
            }
        }
        $this->renderAdminView('add_user', ['defaultRole' => $defaultRole]);
    }

    public function approveLandlord() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            if (!$userId) {
                $_SESSION['error'] = "User ID is required.";
                $this->redirect('admin/verifications');
            }

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                $stmt = $db->prepare("UPDATE landlord_profiles SET verification_status = 'approved' WHERE user_id = ?");
                $stmt->execute([$userId]);

                $stmtUser = $db->prepare("UPDATE users SET status = 'verified' WHERE id = ?");
                $stmtUser->execute([$userId]);

                $db->commit();
                $_SESSION['success'] = "Landlord approved successfully.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Error approving landlord: " . $e->getMessage();
            }
            $this->redirect('admin/verifications');
        }
    }


    public function deleteLandlord() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $db = Database::getInstance()->getConnection();

            $db->beginTransaction();
            try {
                $db->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$userId]);
                $db->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
                $db->commit();
                $_SESSION['success'] = "Landlord account and all associated data deleted.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Delete failed: " . $e->getMessage();
            }
            $this->redirect('admin/properties');
        }
    }

    public function manageUser() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'];
            $newStatus = $_POST['status'];
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $userId]);
            $this->redirect('admin/dashboard');
        }
    }
}

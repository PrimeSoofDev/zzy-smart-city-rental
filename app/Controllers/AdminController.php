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

        $tenants = $db->query("SELECT tp.*, u.username FROM tenant_profiles tp JOIN users u ON tp.user_id = u.id")->fetchAll();
        $landlords = $db->query("SELECT lp.*, u.username FROM landlord_profiles lp JOIN users u ON lp.user_id = u.id")->fetchAll();

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

    public function requests() {
        RbacMiddleware::check(['Admin']);
        $db = Database::getInstance()->getConnection();
        $status = $_GET['status'] ?? 'all';

        $query = "
            SELECT rr.*, p.title as property_title, p.price,
                   tu.username as tenant_name,
                   lu.username as landlord_name
            FROM rental_requests rr
            JOIN properties p ON rr.property_id = p.id
            JOIN users tu ON rr.tenant_id = tu.id
            JOIN users lu ON p.landlord_id = lu.id
        ";
        
        $params = [];
        if ($status !== 'all') {
            $query .= " WHERE rr.status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY rr.request_date DESC";
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $requests = $stmt->fetchAll();

        $this->renderAdminView('requests', ['requests' => $requests, 'status' => $status]);
    }

    public function updateRequestStatus() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestId = $_POST['request_id'] ?? null;
            $status = $_POST['status'] ?? null;

            if ($requestId && $status) {
                $db = Database::getInstance()->getConnection();
                try {
                    $stmt = $db->prepare("UPDATE rental_requests SET status = ? WHERE id = ?");
                    $stmt->execute([$status, $requestId]);

                    $reqStmt = $db->prepare("SELECT tenant_id, property_id FROM rental_requests WHERE id = ?");
                    $reqStmt->execute([$requestId]);
                    $req = $reqStmt->fetch();
                    if ($req) {
                        $propStmt = $db->prepare("SELECT landlord_id, title FROM properties WHERE id = ?");
                        $propStmt->execute([$req['property_id']]);
                        $prop = $propStmt->fetch();
                        
                        $msg = "Rental Request #{$requestId} for '{$prop['title']}' has been updated to: " . strtoupper($status);
                        Notification::send($req['tenant_id'], $msg);
                        if ($prop) Notification::send($prop['landlord_id'], $msg);
                    }

                    $_SESSION['success'] = "Rental request status updated successfully.";
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error updating request status: " . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = "Invalid input.";
            }
            $this->redirect('admin/requests');
        }
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
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'];

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            try {
                $stmt = $db->prepare("INSERT INTO users (username, email, phone, password, status) VALUES (?, ?, ?, ?, 'verified')");
                $stmt->execute([$username, $email, $phone, $hashedPassword]);
                $userId = $db->lastInsertId();

                $roleStmt = $db->prepare("SELECT id FROM roles WHERE role_name = ?");
                $roleStmt->execute([$role]);
                $roleId = $roleStmt->fetchColumn();
                $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$userId, $roleId]);

                if ($role === 'Staff') {
                    // Generate a unique staff ID like STAFF-00042
                    $staffId = 'STAFF-' . str_pad($userId, 5, '0', STR_PAD_LEFT);
                    $db->prepare("INSERT INTO staff_profiles (user_id, staff_id, department) VALUES (?, ?, ?)")
                       ->execute([$userId, $staffId, $location]);
                } elseif ($role === 'Lawyer') {
                    // Generate a placeholder license number like LAW-00042
                    $licenseNum = 'LAW-' . str_pad($userId, 5, '0', STR_PAD_LEFT);
                    $db->prepare("INSERT INTO lawyer_profiles (user_id, license_number, firm_name) VALUES (?, ?, ?)")
                       ->execute([$userId, $licenseNum, $location]);
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

    public function approveUser() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $role = $_POST['role'] ?? null;
            if (!$userId) {
                $_SESSION['error'] = "User ID is required.";
                $this->redirect('admin/verifications');
            }

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                if ($role === 'Landlord') {
                    $stmt = $db->prepare("UPDATE landlord_profiles SET verification_status = 'approved' WHERE user_id = ?");
                    $stmt->execute([$userId]);
                } elseif ($role === 'Tenant') {
                    $stmt = $db->prepare("UPDATE tenant_profiles SET verification_status = 'approved' WHERE user_id = ?");
                    $stmt->execute([$userId]);
                }

                $stmtUser = $db->prepare("UPDATE users SET status = 'verified' WHERE id = ?");
                $stmtUser->execute([$userId]);

                Notification::send($userId, "Congratulations! Your account verification has been APPROVED. You now have full access.");

                $db->commit();
                $_SESSION['success'] = "User approved successfully.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Error approving user: " . $e->getMessage();
            }
            $this->redirect('admin/verifications');
        }
    }

    public function rejectUser() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $role = $_POST['role'] ?? null;
            if (!$userId) {
                $_SESSION['error'] = "User ID is required.";
                $this->redirect('admin/verifications');
            }

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                if ($role === 'Landlord') {
                    $stmt = $db->prepare("UPDATE landlord_profiles SET verification_status = 'rejected' WHERE user_id = ?");
                    $stmt->execute([$userId]);
                } elseif ($role === 'Tenant') {
                    $stmt = $db->prepare("UPDATE tenant_profiles SET verification_status = 'rejected' WHERE user_id = ?");
                    $stmt->execute([$userId]);
                }

                $stmt = $db->prepare("UPDATE users SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$userId]);

                Notification::send($userId, "Your account verification has been REJECTED. Please contact support or re-submit valid documents.");

                $db->commit();
                $_SESSION['success'] = "User rejected/banned successfully.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Error rejecting user: " . $e->getMessage();
            }
            $this->redirect('admin/verifications');
        }
    }

    public function approveProperty() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $propertyId = $_POST['property_id'] ?? null;
            if (!$propertyId) {
                $_SESSION['error'] = "Property ID is required.";
                $this->redirect('admin/properties');
            }

            $db = Database::getInstance()->getConnection();
            try {
                $stmt = $db->prepare("UPDATE properties SET status = 'approved' WHERE id = ?");
                $stmt->execute([$propertyId]);
                $_SESSION['success'] = "Property approved successfully.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Error approving property: " . $e->getMessage();
            }
            $this->redirect('admin/properties');
        }
    }

    public function rejectProperty() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $propertyId = $_POST['property_id'] ?? null;
            if (!$propertyId) {
                $_SESSION['error'] = "Property ID is required.";
                $this->redirect('admin/properties');
            }

            $db = Database::getInstance()->getConnection();
            try {
                $stmt = $db->prepare("UPDATE properties SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$propertyId]);
                $_SESSION['success'] = "Property rejected successfully.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Error rejecting property: " . $e->getMessage();
            }
            $this->redirect('admin/properties');
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

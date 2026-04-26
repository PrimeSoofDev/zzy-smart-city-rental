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

        // Financial Overview Data
        $commissionRate = (float)SiteSetting::get('commission_rate', 15);
        
        $totalVolume = $db->query("SELECT SUM(amount) FROM transactions WHERE status IN ('completed', 'escrow_hold', 'released') AND transaction_type = 'escrow_deposit'")->fetchColumn() ?: 0;
        $escrowFunds = $db->query("SELECT SUM(amount) FROM transactions WHERE status = 'escrow_hold'")->fetchColumn() ?: 0;
        $totalPayouts = $db->query("SELECT SUM(amount) FROM transactions WHERE transaction_type = 'landlord_payout' AND status = 'completed'")->fetchColumn() ?: 0;
        
        // Estimated Platform Earnings (Commission)
        $totalEarnings = $totalVolume * ($commissionRate / 100);

        // Analytical Data: Monthly Revenue (Platform Volume)
        $revenueData = $db->query("
            SELECT DATE_FORMAT(created_at, '%b %Y') as label, SUM(amount) as total 
            FROM transactions 
            WHERE status IN ('completed', 'escrow_hold', 'released')
            GROUP BY label 
            ORDER BY created_at ASC 
            LIMIT 6
        ")->fetchAll();

        // Analytical Data: Property Types
        $propertyTypes = $db->query("
            SELECT property_type as label, COUNT(*) as value 
            FROM properties 
            GROUP BY property_type
        ")->fetchAll();

        // Analytical Data: User Roles
        $userRoles = $db->query("
            SELECT r.role_name as label, COUNT(*) as value 
            FROM users u 
            JOIN user_roles ur ON u.id = ur.user_id 
            JOIN roles r ON ur.role_id = r.id 
            GROUP BY r.role_name
        ")->fetchAll();

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
                'pendingVerifications' => $pendingVerifications,
                'totalVolume' => $totalVolume,
                'totalEarnings' => $totalEarnings,
                'escrowFunds' => $escrowFunds,
                'totalPayouts' => $totalPayouts
            ],
            'analytics' => [
                'revenue' => $revenueData,
                'propertyTypes' => $propertyTypes,
                'userRoles' => $userRoles
            ]
        ]);
        require_once "../views/layouts/admin_layout_end.php";
    }

    public function users() {
        RbacMiddleware::check(['Admin']);
        $role = $_GET['role'] ?? 'all';
        $db = Database::getInstance()->getConnection();

        $query = "SELECT u.id, u.username, u.email, u.phone, u.status, r.role_name
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

    public function editUser() {
        RbacMiddleware::check(['Admin']);
        $userId = $_GET['id'] ?? null;

        if (!$userId) {
            $this->redirect('admin/users');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT u.*, r.role_name FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id LEFT JOIN roles r ON ur.role_id = r.id WHERE u.id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['error'] = "User not found.";
            $this->redirect('admin/users');
        }

        $this->renderAdminView('edit-user', ['user' => $user]);
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

        // Get statistics
        $totalListings = $db->query("SELECT COUNT(*) FROM properties")->fetchColumn();
        $pendingVerification = $db->query("SELECT COUNT(*) FROM properties WHERE status = 'pending_verification'")->fetchColumn();
        $activeProperties = $db->query("SELECT COUNT(*) FROM properties WHERE status = 'approved'")->fetchColumn();
        $rejectedProperties = $db->query("SELECT COUNT(*) FROM properties WHERE status = 'rejected'")->fetchColumn();

        $this->renderAdminView('properties', [
            'properties' => $properties,
            'stats' => [
                'totalListings' => $totalListings,
                'pendingVerification' => $pendingVerification,
                'activeProperties' => $activeProperties,
                'rejectedProperties' => $rejectedProperties
            ],
            'selectedStatus' => $status
        ]);
    }

    public function exportProperties() {
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

        // Generate CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=properties_export_' . date('Y-m-d_H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        // Add CSV headers
        fputcsv($output, ['ID', 'Title', 'Landlord', 'Type', 'Price', 'Rooms', 'Bathrooms', 'Address', 'Status', 'Created Date']);

        // Add data rows
        foreach ($properties as $p) {
            fputcsv($output, [
                $p['id'],
                $p['title'],
                $p['landlord_name'],
                ucfirst($p['property_type']),
                '₦' . number_format($p['price'], 2),
                $p['rooms'] ?? 'N/A',
                $p['bathrooms'] ?? 'N/A',
                $p['address'],
                ucfirst($p['status']),
                $p['created_at']
            ]);
        }

        fclose($output);
        exit;
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
        
        $type = $_GET['type'] ?? 'all';
        $status = $_GET['status'] ?? 'all';

        $query = "SELECT t.*, u.username FROM transactions t JOIN users u ON t.user_id = u.id";
        $where = [];
        $params = [];

        if ($type !== 'all') {
            $where[] = "t.transaction_type = ?";
            $params[] = $type;
        }

        if ($status !== 'all') {
            $where[] = "t.status = ?";
            $params[] = $status;
        }

        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $query .= " ORDER BY t.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $transactions = $stmt->fetchAll();

        // Calculate Stats (Global, not filtered for overview)
        $escrowBalance = $db->query("SELECT SUM(amount) FROM transactions WHERE status = 'escrow_hold'")->fetchColumn() ?: 0;
        $monthlyRevenue = $db->query("
            SELECT SUM(amount * (CAST(setting_value AS DECIMAL)/100)) 
            FROM transactions t 
            JOIN system_settings s ON s.setting_key = 'commission_rate'
            WHERE t.status IN ('completed', 'released') 
            AND t.transaction_type = 'escrow_deposit'
            AND MONTH(t.created_at) = MONTH(CURRENT_DATE())
        ")->fetchColumn() ?: 0;
        $failedCount = $db->query("SELECT COUNT(*) FROM transactions WHERE status = 'failed'")->fetchColumn();

        // Fetch Trend Data for Chart
        $paymentTrend = $db->query("
            SELECT DATE_FORMAT(created_at, '%b %Y') as label, SUM(amount) as total 
            FROM transactions 
            WHERE transaction_type = 'escrow_deposit' AND status IN ('completed', 'released', 'escrow_hold')
            GROUP BY label 
            ORDER BY created_at ASC 
            LIMIT 6
        ")->fetchAll(PDO::FETCH_ASSOC);

        $earningTrend = $db->query("
            SELECT DATE_FORMAT(created_at, '%b %Y') as label, SUM(amount) as total 
            FROM transactions 
            WHERE transaction_type = 'landlord_payout' AND status = 'completed'
            GROUP BY label 
            ORDER BY created_at ASC 
            LIMIT 6
        ")->fetchAll(PDO::FETCH_ASSOC);

        $this->renderAdminView('transactions', [
            'transactions' => $transactions,
            'stats' => [
                'escrowBalance' => $escrowBalance,
                'monthlyRevenue' => $monthlyRevenue,
                'failedCount' => $failedCount
            ],
            'filters' => [
                'type' => $type,
                'status' => $status
            ],
            'trends' => [
                'payments' => $paymentTrend,
                'earnings' => $earningTrend
            ]
        ]);
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
        
        $actionFilter = $_GET['action'] ?? null;
        $whereClause = "";
        $params = [];

        if ($actionFilter) {
            $whereClause = "WHERE a.action LIKE ?";
            $params[] = "%$actionFilter%";
        }
        
        $stmt = $db->prepare("
            SELECT a.*, u.username, u.full_name, r.role_name as user_role
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            $whereClause
            ORDER BY a.created_at DESC
            LIMIT 100
        ");
        $stmt->execute($params);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->renderAdminView('logs', ['logs' => $logs, 'currentFilter' => $actionFilter]);
    }

    public function exportLogs() {
        RbacMiddleware::check(['Admin']);
        $db = Database::getInstance()->getConnection();
        
        $logs = $db->query("
            SELECT a.created_at, u.username, a.action, a.entity_type, a.entity_id, a.ip_address
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="audit_logs_'.date('Y-m-d').'.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Timestamp', 'User', 'Action', 'Entity Type', 'Entity ID', 'IP Address']);
        
        foreach ($logs as $log) {
            fputcsv($output, $log);
        }
        fclose($output);
        exit;
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
            
            // Handle regular settings
            if (isset($_POST['settings'])) {
                foreach ($_POST['settings'] as $key => $value) {
                    $stmt = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                    $stmt->execute([$value, $key]);
                }
            }

            // Handle Branding Uploads
            $uploadDir = 'public/uploads/branding/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach (['logo', 'favicon'] as $type) {
                if (isset($_FILES[$type]) && $_FILES[$type]['error'] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES[$type]['name'], PATHINFO_EXTENSION);
                    $filename = $type . '_' . time() . '.' . $ext;
                    $targetPath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($_FILES[$type]['tmp_name'], $targetPath)) {
                        $stmt = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                        $stmt->execute([$targetPath, $type . '_url']);
                    }
                }
            }
            
            AuditService::log("Updated System Settings & Branding", "Global", 0);
            
            $_SESSION['success'] = "Settings and branding updated successfully!";
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
                // For Staff and Lawyer, we set status to 'pending' so they must verify via OTP
                $initialStatus = in_array($role, ['Staff', 'Lawyer']) ? 'pending' : 'verified';
                $stmt = $db->prepare("INSERT INTO users (username, email, phone, password, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $phone, $hashedPassword, $initialStatus]);
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

                // Send OTP for identity verification
                $otpService = new OtpService();
                $otpChannel = $_POST['otp_channel'] ?? 'email';
                $otpIdentifier = ($otpChannel === 'email') ? $email : $phone;
                
                $otpSent = $otpService->sendOtp($otpIdentifier, $otpChannel, $userId);

                AuditService::log("Created New User", $role, $userId);

                $db->commit();
                $resetLink = APP_URL . "/auth/set-password?token=" . $token;
                
                $msg = "User created! Invite link: " . $resetLink;
                if ($otpSent) {
                    $msg .= " | OTP sent via " . ucfirst($otpChannel);
                } else {
                    $msg .= " | Warning: Failed to send OTP.";
                }
                
                $_SESSION['success'] = $msg;
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

    public function rejectUserWithReason() {
        RbacMiddleware::check(['Admin', 'Staff']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $reason = $this->sanitize($_POST['reason'] ?? '');

            if (!$userId) {
                $_SESSION['error'] = "User ID is required.";
                $this->redirect('admin/users');
            }

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                // Get current user ID (who is performing the action)
                $performedBy = $_SESSION['user_id'] ?? 1;

                // Update user status
                $stmt = $db->prepare("UPDATE users SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$userId]);

                // Log the action
                $actionStmt = $db->prepare("INSERT INTO user_actions (user_id, action_type, reason, performed_by) VALUES (?, 'rejected', ?, ?)");
                $actionStmt->execute([$userId, $reason, $performedBy]);

                // Send notification
                Notification::send($userId, "Your account has been REJECTED due to: " . $reason . ". Please contact support for assistance.");

                $db->commit();
                $_SESSION['success'] = "User rejected successfully.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Error rejecting user: " . $e->getMessage();
            }
            $this->redirect('admin/users');
        }
    }

    public function banUser() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $reason = $this->sanitize($_POST['reason'] ?? '');

            if (!$userId) {
                $_SESSION['error'] = "User ID is required.";
                $this->redirect('admin/users');
            }

            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                // Get current user ID (who is performing the action)
                $performedBy = $_SESSION['user_id'] ?? 1;

                // Update user status
                $stmt = $db->prepare("UPDATE users SET status = 'banned' WHERE id = ?");
                $stmt->execute([$userId]);

                // Log the action
                $actionStmt = $db->prepare("INSERT INTO user_actions (user_id, action_type, reason, performed_by) VALUES (?, 'banned', ?, ?)");
                $actionStmt->execute([$userId, $reason, $performedBy]);

                // Send notification
                Notification::send($userId, "Your account has been BANNED due to: " . $reason . ". This decision is final. Please contact support if you believe this is a mistake.");

                $db->commit();
                $_SESSION['success'] = "User banned successfully.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Error banning user: " . $e->getMessage();
            }
            $this->redirect('admin/users');
        }
    }

    public function updateUserProfile() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            $username = $this->sanitize($_POST['username'] ?? '');
            $email = $this->sanitize($_POST['email'] ?? '');
            $phone = $this->sanitize($_POST['phone'] ?? '');

            if (!$userId) {
                $_SESSION['error'] = "User ID is required.";
                $this->redirect('admin/users');
            }

            $db = Database::getInstance()->getConnection();
            try {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->execute([$username, $email, $phone, $userId]);

                $_SESSION['success'] = "User profile updated successfully.";
            } catch (Exception $e) {
                $_SESSION['error'] = "Error updating user profile: " . $e->getMessage();
            }
            $this->redirect('admin/users');
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
                
                AuditService::log("Deleted User Account", "User", $userId);
                
                $db->commit();
                $_SESSION['success'] = "User account deleted successfully.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['error'] = "Delete failed: " . $e->getMessage();
            }
            $this->redirect('admin/dashboard');
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

    public function disputes() {
        RbacMiddleware::check(['Admin']);
        $db = Database::getInstance()->getConnection();
        
        $query = "
            SELECT d.*, 
                   p.title as property_title,
                   tu.username as tenant_name,
                   lu.username as landlord_name
            FROM disputes d
            JOIN rental_requests rr ON d.request_id = rr.id
            JOIN properties p ON rr.property_id = p.id
            JOIN users tu ON rr.tenant_id = tu.id
            JOIN users lu ON p.landlord_id = lu.id
            ORDER BY d.created_at DESC
        ";
        $disputes = $db->query($query)->fetchAll();

        $this->renderAdminView('disputes', ['disputes' => $disputes]);
    }

    public function mediateDispute() {
        RbacMiddleware::check(['Admin']);
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('admin/disputes');

        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT d.*, r.amount, p.title as property_title, 
                   tu.username as tenant_name, lu.username as landlord_name
            FROM disputes d
            JOIN rental_requests r ON d.request_id = r.id
            JOIN properties p ON r.property_id = p.id
            JOIN users tu ON r.tenant_id = tu.id
            JOIN users lu ON p.landlord_id = lu.id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        $dispute = $stmt->fetch();

        if (!$dispute) $this->redirect('admin/disputes');

        $evStmt = $db->prepare("
            SELECT e.*, u.username as user_name 
            FROM dispute_evidence e 
            JOIN users u ON e.uploaded_by = u.id 
            WHERE e.dispute_id = ? 
            ORDER BY e.created_at ASC
        ");
        $evStmt->execute([$id]);
        $evidence = $evStmt->fetchAll();

        $this->renderAdminView('mediate_dispute', [
            'dispute' => $dispute,
            'evidence' => $evidence
        ]);
    }

    public function resolveDispute() {
        RbacMiddleware::check(['Admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('admin/disputes');

        $disputeId = $_POST['dispute_id'] ?? null;
        $resolutionType = $_POST['resolution_type'] ?? null;
        $notes = $this->sanitize($_POST['notes'] ?? '');

        if (!$disputeId || !$resolutionType) {
            $_SESSION['error'] = "Missing resolution details.";
            $this->redirect('admin/disputes');
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            $db->prepare("UPDATE disputes SET status = 'resolved', resolution_type = ?, resolution_notes = ?, resolved_at = NOW() WHERE id = ?")
               ->execute([$resolutionType, $notes, $disputeId]);

            $stmt = $db->prepare("SELECT request_id FROM disputes WHERE id = ?");
            $stmt->execute([$disputeId]);
            $requestId = $stmt->fetchColumn();

            if ($resolutionType === 'full_release') {
                $db->prepare("UPDATE transactions SET status = 'released' WHERE request_id = ?")->execute([$requestId]);
            } elseif ($resolutionType === 'full_refund') {
                $db->prepare("UPDATE transactions SET status = 'refunded' WHERE request_id = ?")->execute([$requestId]);
                $db->prepare("UPDATE rental_requests SET status = 'cancelled' WHERE id = ?")->execute([$requestId]);
            }

            // Note: partial split logic would be handled by payment service in production.
            $db->commit();
            $_SESSION['success'] = "Dispute resolved successfully.";
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }

        $this->redirect('admin/disputes');
    }
}

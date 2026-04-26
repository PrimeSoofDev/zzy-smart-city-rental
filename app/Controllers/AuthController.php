<?php
class AuthController extends Controller {
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $data = [
                'username' => $this->sanitize($_POST['username']),
                'email' => $this->sanitize($_POST['email']),
                'password' => $_POST['password'],
                'role' => $_POST['role']
            ];

            if ($userModel->create($data)) {
                $db = Database::getInstance()->getConnection();
                $userId = $db->lastInsertId();
                $userModel->assignRole($userId, $data['role']);

                // Tag visitor as signup
                $ip = $_SERVER['REMOTE_ADDR'];
                $db->prepare("UPDATE visitor_logs SET is_new_signup = 1, user_id = ? WHERE ip_address = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY id DESC LIMIT 1")
                   ->execute([$userId, $ip]);

                $this->redirect('auth/login');
            }
        }
        $this->view('auth/signup');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->sanitize($_POST['email']);
            $password = $_POST['password'];
            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Check if user needs OTP verification
                if ($user['status'] === 'pending' && in_array($userModel->getRole($user['id']), ['Staff', 'Lawyer'])) {
                    $_SESSION['temp_user_id'] = $user['id'];
                    $_SESSION['temp_email'] = $user['email'];
                    $_SESSION['temp_phone'] = $user['phone'];
                    $this->redirect('auth/verify-otp');
                    return;
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['full_name'] ?: $user['username'];
                $role = $userModel->getRole($user['id']);
                $_SESSION['role'] = $role;

                AuditService::log("User Logged In", "User", $user['id']);

                $this->redirect($this->getDashboardByRole($role));
            } else {
                $_SESSION['error'] = "Invalid email or password";
                $this->redirect('auth/login');
            }
        }
        $this->view('auth/login');
    }

    public function verifyOtpView() {
        if (!isset($_SESSION['temp_user_id'])) {
            $this->redirect('auth/login');
        }
        $this->view('auth/verify_otp');
    }
    public function verifyOtpSubmit() {
        if (!isset($_SESSION['temp_user_id'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Session expired'], 401);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $otpCode = $input['otp'] ?? null;
        
        $otpService = new OtpService();
        $userId = $_SESSION['temp_user_id'];
        
        // We try to verify against both email and phone if possible, 
        // or just use the sessions we stored.
        $identifier = $_SESSION['temp_email']; // Defaulting to email for now
        $result = $otpService->verifyOtp($identifier, $otpCode, 'email');
        
        if ($result['status'] === 'error') {
            // Try phone if email failed
            $result = $otpService->verifyOtp($_SESSION['temp_phone'], $otpCode, 'phone');
        }

        if ($result['status'] === 'success') {
            // Mark user as verified
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE users SET status = 'verified' WHERE id = ?");
            $stmt->execute([$userId]);

            // Tag visitor as verified on first visit
            $ip = $_SERVER['REMOTE_ADDR'];
            $db->prepare("UPDATE visitor_logs SET is_first_visit_verified = 1 WHERE ip_address = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY id DESC LIMIT 1")
               ->execute([$ip]);

            // Log them in
            $userModel = new User();
            $user = $userModel->findByEmail($_SESSION['temp_email']);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['full_name'] ?: $user['username'];
            $role = $userModel->getRole($user['id']);
            $_SESSION['role'] = $role;

            unset($_SESSION['temp_user_id'], $_SESSION['temp_email'], $_SESSION['temp_phone']);
            
            $this->jsonResponse(['status' => 'success', 'redirect' => $this->getDashboardByRole($role)]);
        } else {
            $this->jsonResponse($result, 400);
        }
    }

    private function getDashboardByRole($role) {
        switch($role) {
            case 'Tenant': return 'tenant/dashboard';
            case 'Landlord': return 'landlord/dashboard';
            case 'Staff': return 'staff/dashboard';
            case 'Admin': return 'admin/dashboard';
            case 'Lawyer': return 'lawyer/dashboard';
            default: return 'auth/login';
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('auth/login');
    }

    private function jsonResponse($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }
}

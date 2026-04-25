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
                $userId = Database::getInstance()->getConnection()->lastInsertId();
                $userModel->assignRole($userId, $data['role']);
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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['full_name'] ?: $user['username'];
                $role = $userModel->getRole($user['id']);
                $_SESSION['role'] = $role;

                // DEBUG: Log role to check if it's actually 'Admin'
                // error_log("User logged in with role: " . $role);

                $this->redirect($this->getDashboardByRole($role));
            } else {
                // Add error feedback if login fails
                $_SESSION['error'] = "Invalid email or password";
                $this->redirect('auth/login');
            }
        }
        $this->view('auth/login');
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
}

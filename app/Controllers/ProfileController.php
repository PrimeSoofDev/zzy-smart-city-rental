<?php
class ProfileController extends Controller {

    public function edit() {
        if (!isset($_SESSION['user_id'])) $this->redirect('auth/login');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        $role = $_SESSION['role'] ?? 'User';
        $layout = strtolower($role) . "_layout_start.php";
        
        // Handle cases where layout might not exist or be named differently
        if (!file_exists("../views/layouts/$layout")) {
            $layout = "tenant_layout_start.php"; // Default fallback
        }

        require_once "../views/layouts/$layout";
        $this->view('profile/edit', ['user' => $user]);
        require_once "../views/layouts/" . str_replace("_start", "_end", $layout);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('profile/edit');
        if (!isset($_SESSION['user_id'])) $this->redirect('auth/login');

        $userId = $_SESSION['user_id'];
        $fullName = $this->sanitize($_POST['full_name']);
        $email = $this->sanitize($_POST['email']);
        $phone = $this->sanitize($_POST['phone']);
        
        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            $avatarUrl = null;
            if (!empty($_FILES['avatar']['name'])) {
                $targetDir = "../public/uploads/avatars/";
                if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
                
                $fileName = time() . "_" . $_FILES['avatar']['name'];
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetDir . $fileName)) {
                    $avatarUrl = 'uploads/avatars/' . $fileName;
                }
            }

            if ($avatarUrl) {
                $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, avatar_url = ? WHERE id = ?");
                $stmt->execute([$fullName, $email, $phone, $avatarUrl, $userId]);
            } else {
                $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, avatar_url = ? WHERE id = ?");
                // Note: keeping avatar_url as is if not uploaded
                $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->execute([$fullName, $email, $phone, $userId]);
            }

            $db->commit();
            $_SESSION['success'] = "Profile updated successfully.";
            $_SESSION['username'] = $fullName ?: $_SESSION['username'];
            $this->redirect('profile/edit');

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
            $this->redirect('profile/edit');
        }
    }
}

<?php
class TenantController extends Controller {
    private function checkVerification() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->redirect('auth/login');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT verification_status FROM tenant_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $status = $stmt->fetchColumn();

        if ($status !== 'approved') {
            $this->redirect('tenant/verify');
        }
    }

    public function dashboard() {
        $this->checkVerification();
        RbacMiddleware::check(['Tenant']);
        $propModel = new Property();
        $properties = $propModel->getAllApproved();

        require_once "../views/layouts/tenant_layout_start.php";
        $this->view('tenant/dashboard', ['properties' => $properties]);
        require_once "../views/layouts/tenant_layout_end.php";
    }

    public function verify() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) $this->redirect('auth/login');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT verification_status FROM tenant_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $status = $stmt->fetchColumn();

        if ($status === 'approved') {
            $this->redirect('tenant/dashboard');
        }

        require_once "../views/layouts/tenant_layout_start.php";
        $this->view('tenant/verification');
        require_once "../views/layouts/tenant_layout_end.php";
    }

    public function submitVerification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('tenant/verify');

        $userId = $_SESSION['user_id'];
        $id_number = $this->sanitize($_POST['id_number']);
        $address = $this->sanitize($_POST['address']);

        $db = Database::getInstance()->getConnection();

        $targetDir = "../public/uploads/kyc/tenant/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES["id_doc"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["id_doc"]["tmp_name"], $targetFilePath)) {
            $stmt = $db->prepare("INSERT INTO tenant_profiles (user_id, bvn_nin, address, verification_status)
                                 VALUES (?, ?, ?, 'pending')
                                 ON DUPLICATE KEY UPDATE bvn_nin = ?, address = ?");
            $stmt->execute([$userId, $id_number, $address, $id_number, $address]);

            $_SESSION['success'] = "Verification documents submitted. Please wait for admin approval.";
            $this->redirect('tenant/verify');
        } else {
            $_SESSION['error'] = "File upload failed.";
            $this->redirect('tenant/verify');
        }
    }

    public function requestRental() {
        $this->checkVerification();
        RbacMiddleware::check(['Tenant']);
        $propertyId = $_GET['id'] ?? null;
        if (!$propertyId) $this->redirect('tenant/dashboard');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO rental_requests (tenant_id, property_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $propertyId]);
        $requestId = $db->lastInsertId();

        // Get landlord ID
        $landlordStmt = $db->prepare("SELECT landlord_id, title FROM properties WHERE id = ?");
        $landlordStmt->execute([$propertyId]);
        $property = $landlordStmt->fetch();

        if ($property) {
            $tenantName = $_SESSION['username'] ?? 'A tenant';
            $msg = "{$tenantName} has requested to rent your property: {$property['title']}. Request ID: #{$requestId}.";
            Notification::send($property['landlord_id'], $msg);
        }

        $this->redirect('tenant/dashboard');
    }
}

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
        $this->view('tenant/dashboard', ['properties' => $properties]);
    }

    public function requestRental() {
        $this->checkVerification();
        RbacMiddleware::check(['Tenant']);
        $propertyId = $_GET['id'] ?? null;
        if (!$propertyId) $this->redirect('tenant/dashboard');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO rental_requests (tenant_id, property_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $propertyId]);

        $this->redirect('tenant/dashboard');
    }

<?php
class TenantController extends Controller {
    public function dashboard() {
        RbacMiddleware::check(['Tenant']);
        $propModel = new Property();
        $properties = $propModel->getAllApproved();
        $this->view('tenant/dashboard', ['properties' => $properties]);
    }

    public function requestRental() {
        RbacMiddleware::check(['Tenant']);
        $propertyId = $_GET['id'] ?? null;
        if (!$propertyId) $this->redirect('tenant/dashboard');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO rental_requests (tenant_id, property_id, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$_SESSION['user_id'], $propertyId]);

        $this->redirect('tenant/dashboard');
    }
}

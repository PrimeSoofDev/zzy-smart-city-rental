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
        $status = $stmt->fetchColumn() ?: 'unverified';

        require_once "../views/layouts/tenant_layout_start.php";
        $this->view('tenant/verification', ['status' => $status]);
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

    public function propertyDetails() {
        $this->checkVerification();
        RbacMiddleware::check(['Tenant']);
        
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('tenant/dashboard');

        $db = Database::getInstance()->getConnection();
        
        // Fetch property with landlord info
        $stmt = $db->prepare("SELECT p.*, u.username as landlord_name, u.email as landlord_email, u.phone as landlord_phone 
                             FROM properties p 
                             JOIN users u ON p.landlord_id = u.id 
                             WHERE p.id = ? AND p.status = 'approved'");
        $stmt->execute([$id]);
        $property = $stmt->fetch();

        if (!$property) $this->redirect('tenant/dashboard');

        // Fetch images
        $imgStmt = $db->prepare("SELECT image_url FROM property_images WHERE property_id = ?");
        $imgStmt->execute([$id]);
        $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

        // Fetch suggested properties
        $propModel = new Property();
        $suggested = $propModel->getSuggested($property['property_type'], $id);

        require_once "../views/layouts/tenant_layout_start.php";
        $this->view('tenant/property_details', [
            'property' => $property, 
            'images' => $images,
            'suggested' => $suggested
        ]);
        require_once "../views/layouts/tenant_layout_end.php";
    }

    public function processPayment() {
        $this->checkVerification();
        RbacMiddleware::check(['Tenant']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('tenant/dashboard');

        $propertyId = $_POST['property_id'] ?? null;
        $tenantId = $_SESSION['user_id'];

        if (!$propertyId) $this->redirect('tenant/dashboard');

        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            // Get property details for notification and transaction
            $pStmt = $db->prepare("SELECT title, price, landlord_id FROM properties WHERE id = ?");
            $pStmt->execute([$propertyId]);
            $property = $pStmt->fetch();

            if (!$property) throw new Exception("Property not found.");

            // 1. Create Rental Request as 'paid'
            $stmt = $db->prepare("INSERT INTO rental_requests (tenant_id, property_id, status) VALUES (?, ?, 'paid')");
            $stmt->execute([$tenantId, $propertyId]);
            $requestId = $db->lastInsertId();

            // Calculate Fees
            $basePrice = floatval($property['price']);
            $platformFee = $basePrice * 0.20;
            $legalFee = $basePrice * 0.10;
            $totalAmount = $basePrice + $platformFee + $legalFee;

            // 2. Create Transaction record (Escrow Deposit)
            $tStmt = $db->prepare("INSERT INTO transactions (request_id, user_id, amount, transaction_type, status) VALUES (?, ?, ?, 'escrow_deposit', 'completed')");
            $tStmt->execute([$requestId, $tenantId, $totalAmount]);

            // 3. Notify Landlord
            $tenantName = $_SESSION['username'] ?? 'A tenant';
            $msg = "Payment Received! {$tenantName} has paid for your property '{$property['title']}'. Rental Request #{$requestId} is now in 'Paid' status. Please await legal agreement drafting.";
            Notification::send($property['landlord_id'], $msg);

            // 4. Notify Admin and Staff
            $adminStaffQuery = $db->query("SELECT u.id FROM users u 
                                          JOIN user_roles ur ON u.id = ur.user_id 
                                          JOIN roles r ON ur.role_id = r.id 
                                          WHERE r.role_name IN ('Admin', 'Staff')");
            $adminStaffIds = $adminStaffQuery->fetchAll(PDO::FETCH_COLUMN);
            
            $adminMsg = "New Escrow Payment: {$tenantName} has paid ₦" . number_format($totalAmount, 2) . " for '{$property['title']}' (#{$requestId}). Please assign a lawyer if not already done.";
            foreach ($adminStaffIds as $recipientId) {
                Notification::send($recipientId, $adminMsg);
            }

            $db->commit();
            $_SESSION['success'] = "Payment successful! The funds are now held in escrow. A lawyer will be assigned to draft the rental agreement shortly.";
            $this->redirect('tenant/dashboard');

        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $_SESSION['error'] = "Payment failed: " . $e->getMessage();
            $this->redirect('tenant/property?id=' . $propertyId);
        }
    }
}

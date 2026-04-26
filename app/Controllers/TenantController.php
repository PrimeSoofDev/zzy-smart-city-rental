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
        $userId = $_SESSION['user_id'];
        $db = Database::getInstance()->getConnection();

        $propModel = new Property();
        $properties = $propModel->getAllApproved();

        // Fetch my escrow transactions
        $escrowStmt = $db->prepare("
            SELECT t.*, p.title as property_title, p.address as property_address, rr.status as request_status
            FROM transactions t
            JOIN rental_requests rr ON t.request_id = rr.id
            JOIN properties p ON rr.property_id = p.id
            WHERE t.user_id = ? AND t.status IN ('escrow_hold', 'released', 'refunded')
            ORDER BY t.created_at DESC
        ");
        $escrowStmt->execute([$userId]);
        $escrowItems = $escrowStmt->fetchAll();

        require_once "../views/layouts/tenant_layout_start.php";
        $this->view('tenant/dashboard', [
            'properties' => $properties,
            'escrowItems' => $escrowItems
        ]);
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
            // Get property and landlord subaccount
            $stmt = $db->prepare("SELECT p.title, p.price, p.landlord_id, lp.subaccount_code 
                                 FROM properties p 
                                 JOIN landlord_profiles lp ON p.landlord_id = lp.user_id 
                                 WHERE p.id = ?");
            $stmt->execute([$propertyId]);
            $property = $stmt->fetch();

            if (!$property) throw new Exception("Property not found.");
            // Allow payment even if landlord has no subaccount. Money goes to main platform escrow.

            // Calculate Total
            $basePrice = floatval($property['price']);
            $platformFee = $basePrice * (PLATFORM_FEE_PERCENT / 100);
            $legalFee = $basePrice * 0.10; // Keeping existing logic for legal fee if any
            $totalAmount = $basePrice + $platformFee + $legalFee;

            // Generate unique reference
            $reference = "ZZY-" . time() . "-" . $tenantId;

            // Initialize Paystack
            $paymentService = new PaymentService();
            $callbackUrl = APP_URL . "/tenant/payment-verify";
            
            $response = $paymentService->initializeTransaction(
                $_SESSION['email'] ?? 'tenant@example.com',
                $totalAmount,
                $reference,
                $callbackUrl,
                $property['subaccount_code']
            );

            if (!$response['status']) throw new Exception("Paystack Error: " . $response['message']);

            // Create pending transaction record
            $tStmt = $db->prepare("INSERT INTO transactions (user_id, amount, transaction_type, status, paystack_reference) 
                                 VALUES (?, ?, 'escrow_deposit', 'pending', ?)");
            $tStmt->execute([$tenantId, $totalAmount, $reference]);

            // Store property ID in session to use after verification
            $_SESSION['pending_booking'] = [
                'property_id' => $propertyId,
                'reference' => $reference,
                'amount' => $totalAmount
            ];

            // Redirect to Paystack
            header("Location: " . $response['data']['authorization_url']);
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = "Payment initialization failed: " . $e->getMessage();
            $this->redirect('tenant/property?id=' . $propertyId);
        }
    }

    public function verifyPayment() {
        $this->checkVerification();
        $reference = $_GET['reference'] ?? $_GET['trxref'] ?? null;
        
        if (!$reference) {
            $_SESSION['error'] = "No transaction reference found.";
            $this->redirect('tenant/dashboard');
        }

        $db = Database::getInstance()->getConnection();
        $paymentService = new PaymentService();

        try {
            $response = $paymentService->verifyTransaction($reference);

            if ($response['status'] && $response['data']['status'] === 'success') {
                $db->beginTransaction();

                // Update transaction status to escrow_hold
                $stmt = $db->prepare("UPDATE transactions SET status = 'escrow_hold' WHERE paystack_reference = ?");
                $stmt->execute([$reference]);

                // Create Rental Request
                $pending = $_SESSION['pending_booking'] ?? null;
                if ($pending && $pending['reference'] === $reference) {
                    $propertyId = $pending['property_id'];
                    $tenantId = $_SESSION['user_id'];

                    $rrStmt = $db->prepare("INSERT INTO rental_requests (tenant_id, property_id, status) VALUES (?, ?, 'paid')");
                    $rrStmt->execute([$tenantId, $propertyId]);
                    $requestId = $db->lastInsertId();

                    // Update transaction with request_id
                    $db->prepare("UPDATE transactions SET request_id = ? WHERE paystack_reference = ?")
                       ->execute([$requestId, $reference]);

                    // Notify Landlord
                    $pStmt = $db->prepare("SELECT landlord_id, title FROM properties WHERE id = ?");
                    $pStmt->execute([$propertyId]);
                    $property = $pStmt->fetch();
                    
                    Notification::send($property['landlord_id'], "Payment Received! Funds are held in escrow for '{$property['title']}'. Rental Request #{$requestId}.");
                    
                    unset($_SESSION['pending_booking']);
                }

                $db->commit();
                $_SESSION['success'] = "Payment successful! Funds are now secured in escrow.";
            } else {
                $_SESSION['error'] = "Payment verification failed: " . ($response['message'] ?? 'Unknown error');
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $_SESSION['error'] = "Verification Error: " . $e->getMessage();
        }

        $this->redirect('tenant/dashboard');
    }

    /**
     * Handles raising a dispute for a rental request
     */
    public function raiseDispute() {
        $this->checkVerification();
        RbacMiddleware::check(['Tenant']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('tenant/dashboard');
        }

        $requestId = $_POST['request_id'] ?? null;
        $reason = $this->sanitize($_POST['reason'] ?? 'Standard dispute raised.');
        $userId = $_SESSION['user_id'];

        if (!$requestId) {
            $_SESSION['error'] = "Invalid Request ID.";
            $this->redirect('tenant/dashboard');
        }

        $db = Database::getInstance()->getConnection();
        
        try {
            // Verify the request belongs to the logged-in tenant
            $stmt = $db->prepare("SELECT rr.*, p.landlord_id, p.title FROM rental_requests rr 
                                 JOIN properties p ON rr.property_id = p.id
                                 WHERE rr.id = ? AND rr.tenant_id = ?");
            $stmt->execute([$requestId, $userId]);
            $request = $stmt->fetch();

            if (!$request) throw new Exception("Rental request not found.");

            // Update status and notify landlord
            $db->prepare("UPDATE rental_requests SET status = 'disputed' WHERE id = ?")->execute([$requestId]);
            Notification::send($request['landlord_id'], "A dispute has been raised for '{$request['title']}' (Request #{$requestId}). Reason: {$reason}");
            
            $_SESSION['success'] = "Dispute raised successfully. Funds will remain in escrow until resolved.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        $this->redirect('tenant/dashboard');
    }

    public function disputes() {
        $this->checkVerification();
        RbacMiddleware::check(['Tenant']);

        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];

        $stmt = $db->prepare("
            SELECT t.*, p.title as property_title, p.address as property_address, p.landlord_id, lu.username as landlord_name, rr.status as request_status
            FROM transactions t
            JOIN rental_requests rr ON t.request_id = rr.id
            JOIN properties p ON rr.property_id = p.id
            JOIN users lu ON p.landlord_id = lu.id
            WHERE t.user_id = ? AND t.status IN ('escrow_hold', 'released', 'refunded', 'completed')
        ");
        $stmt->execute([$userId]);
        $escrowItems = $stmt->fetchAll();

        require_once "../views/layouts/tenant_layout_start.php";
        $this->view('tenant/disputes', ['escrowItems' => $escrowItems], false);
        require_once "../views/layouts/tenant_layout_end.php";
    }
}

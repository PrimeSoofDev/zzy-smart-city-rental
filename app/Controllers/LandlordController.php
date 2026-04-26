<?php
class LandlordController extends Controller {

    private function checkVerification() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->redirect('auth/login');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT verification_status FROM landlord_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $status = $stmt->fetchColumn();

        // Use trim() to avoid issues with whitespace and handle null/false
        $status = ($status !== false) ? trim($status) : null;

        if ($status !== 'approved') {
            $this->redirect('landlord/verify');
        }
    }

    public function dashboard() {
        $this->checkVerification();

        $userId = $_SESSION['user_id'];
        $db = Database::getInstance()->getConnection();

        $properties = $db->prepare("SELECT * FROM properties WHERE landlord_id = ?");
        $properties->execute([$userId]);
        $myProperties = $properties->fetchAll();

        // Check if bank details are set
        $bankStmt = $db->prepare("SELECT subaccount_code FROM landlord_profiles WHERE user_id = ?");
        $bankStmt->execute([$userId]);
        $bankInfo = $bankStmt->fetch();

        // Fetch pending payouts (escrow holds)
        $payoutStmt = $db->prepare("
            SELECT t.*, p.title as property_title, p.address as property_address, tu.username as tenant_name, rr.status as request_status
            FROM transactions t
            JOIN rental_requests rr ON t.request_id = rr.id
            JOIN properties p ON rr.property_id = p.id
            JOIN users tu ON t.user_id = tu.id
            WHERE p.landlord_id = ? AND t.status IN ('escrow_hold', 'released')
            ORDER BY t.created_at DESC
        ");
        $payoutStmt->execute([$userId]);
        $payoutItems = $payoutStmt->fetchAll();

        require_once "../views/layouts/landlord_layout_start.php";
        $this->view('landlord/dashboard', [
            'properties' => $myProperties,
            'bankSetupRequired' => empty($bankInfo['subaccount_code']),
            'payoutItems' => $payoutItems
        ]);
        require_once "../views/layouts/landlord_layout_end.php";
    }

    public function verify() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) $this->redirect('auth/login');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT verification_status FROM landlord_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $status = $stmt->fetchColumn() ?: 'unverified';

        require_once "../views/layouts/landlord_layout_start.php";
        $this->view('landlord/verification', ['status' => $status]);
        require_once "../views/layouts/landlord_layout_end.php";
    }

    public function submitVerification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('landlord/verify');

        $userId = $_SESSION['user_id'];
        $bvn = $this->sanitize($_POST['bvn_nin']);
        $address = $this->sanitize($_POST['address']);

        $db = Database::getInstance()->getConnection();

        $targetDir = "../public/uploads/kyc/landlord/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . '_' . basename($_FILES["id_doc"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["id_doc"]["tmp_name"], $targetFilePath)) {
            $stmt = $db->prepare("INSERT INTO landlord_profiles (user_id, bvn, address, verification_status)
                                 VALUES (?, ?, ?, 'pending')
                                 ON DUPLICATE KEY UPDATE bvn = ?, address = ?");
            $stmt->execute([$userId, $bvn, $address, $bvn, $address]);

            $_SESSION['success'] = "Verification documents submitted. Please wait for admin approval.";
            $this->redirect('landlord/verify');
        } else {
            $_SESSION['error'] = "File upload failed.";
            $this->redirect('landlord/verify');
        }
    }

    public function addProperty() {
        $this->checkVerification();
        require_once "../views/layouts/landlord_layout_start.php";
        $this->view('landlord/add-property');
        require_once "../views/layouts/landlord_layout_end.php";
    }

    public function saveProperty() {
        $this->checkVerification();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('landlord/dashboard');

        $userId = $_SESSION['user_id'];
        $title = $this->sanitize($_POST['title']);
        $description = $this->sanitize($_POST['description']);
        $price = floatval($_POST['price']);
        $address = $this->sanitize($_POST['address']);
        $lat = floatval($_POST['latitude']);
        $lng = floatval($_POST['longitude']);
        $type = $_POST['property_type'];

        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO properties (landlord_id, title, description, address, price, latitude, longitude, property_type, status)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending_verification')");
            $stmt->execute([$userId, $title, $description, $address, $price, $lat, $lng, $type]);
            $propertyId = $db->lastInsertId();

            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = "../public/uploads/properties/";
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

                foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                    $fileName = time() . "_" . $_FILES['images']['name'][$key];
                    $targetPath = $uploadDir . $fileName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $imgStmt = $db->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
                        $imgStmt->execute([$propertyId, 'uploads/properties/' . $fileName]);
                    }
                }
            }

            $db->commit();
            $_SESSION['success'] = "Property listed successfully! Awaiting admin verification.";
            $this->redirect('landlord/dashboard');

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "Error saving property: " . $e->getMessage();
            $this->redirect('landlord/add-property');
        }
    }

    public function editProperty() {
        $this->checkVerification();
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('landlord/dashboard');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM properties WHERE id = ? AND landlord_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $property = $stmt->fetch();

        if (!$property) $this->redirect('landlord/dashboard');

        $imgStmt = $db->prepare("SELECT * FROM property_images WHERE property_id = ?");
        $imgStmt->execute([$id]);
        $images = $imgStmt->fetchAll();

        require_once "../views/layouts/landlord_layout_start.php";
        $this->view('landlord/edit-property', ['property' => $property, 'images' => $images]);
        require_once "../views/layouts/landlord_layout_end.php";
    }

    public function updateProperty() {
        $this->checkVerification();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('landlord/dashboard');

        $propertyId = $_POST['property_id'];
        $userId = $_SESSION['user_id'];
        $title = $this->sanitize($_POST['title']);
        $description = $this->sanitize($_POST['description']);
        $price = floatval($_POST['price']);
        $address = $this->sanitize($_POST['address']);
        $lat = floatval($_POST['latitude'] ?? 0);
        $lng = floatval($_POST['longitude'] ?? 0);
        $type = $_POST['property_type'];

        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            // Verify ownership
            $stmt = $db->prepare("SELECT id FROM properties WHERE id = ? AND landlord_id = ?");
            $stmt->execute([$propertyId, $userId]);
            if (!$stmt->fetch()) throw new Exception("Unauthorized access.");

            // Update property
            $updateStmt = $db->prepare("UPDATE properties SET title = ?, description = ?, address = ?, price = ?, latitude = ?, longitude = ?, property_type = ?, status = 'pending_verification' WHERE id = ?");
            $updateStmt->execute([$title, $description, $address, $price, $lat, $lng, $type, $propertyId]);

            // Handle Image Deletions
            if (!empty($_POST['delete_images'])) {
                foreach ($_POST['delete_images'] as $imgId) {
                    $imgDataStmt = $db->prepare("SELECT image_url FROM property_images WHERE id = ? AND property_id = ?");
                    $imgDataStmt->execute([$imgId, $propertyId]);
                    $imgData = $imgDataStmt->fetch();
                    if ($imgData) {
                        $fullPath = "../public/" . $imgData['image_url'];
                        if (file_exists($fullPath)) @unlink($fullPath);
                        $db->prepare("DELETE FROM property_images WHERE id = ?")->execute([$imgId]);
                    }
                }
            }

            // Handle New Image Uploads
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = "../public/uploads/properties/";
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

                foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $fileName = time() . "_" . $key . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['images']['name'][$key]);
                        $targetPath = $uploadDir . $fileName;
                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $imgStmt = $db->prepare("INSERT INTO property_images (property_id, image_url) VALUES (?, ?)");
                            $imgStmt->execute([$propertyId, 'uploads/properties/' . $fileName]);
                        }
                    }
                }
            }

            $db->commit();
            $_SESSION['success'] = "Property updated successfully and is now awaiting re-verification.";
            $this->redirect('landlord/dashboard');

        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $_SESSION['error'] = "Error updating property: " . $e->getMessage();
            $this->redirect('landlord/edit-property?id=' . $propertyId);
        }
    }

    public function bankDetails() {
        $this->checkVerification();
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];

        // Get current profile
        $stmt = $db->prepare("SELECT * FROM landlord_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch();

        // Get bank list from Paystack
        $paymentService = new PaymentService();
        $bankResponse = $paymentService->getBanks();
        $banks = ($bankResponse['status']) ? $bankResponse['data'] : [];

        require_once "../views/layouts/landlord_layout_start.php";
        $this->view('landlord/bank_details', ['profile' => $profile, 'banks' => $banks]);
        require_once "../views/layouts/landlord_layout_end.php";
    }

    public function saveBankDetails() {
        $this->checkVerification();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('landlord/bank-details');

        $userId = $_SESSION['user_id'];
        $bankCode = $_POST['bank_code'];
        $accountNumber = $_POST['account_number'];

        $db = Database::getInstance()->getConnection();
        $paymentService = new PaymentService();

        try {
            // Get profile
            $stmt = $db->prepare("SELECT subaccount_code FROM landlord_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $profile = $stmt->fetch();

            $businessName = $_SESSION['username'] . " Rental";
            $bankList = $paymentService->getBanks();
            $bankName = '';
            foreach ($bankList['data'] as $b) {
                if ($b['code'] == $bankCode) {
                    $bankName = $b['name'];
                    break;
                }
            }

            $subaccountData = [
                'business_name' => $businessName,
                'settlement_bank' => $bankCode,
                'account_number' => $accountNumber,
                'percentage_charge' => PLATFORM_FEE_PERCENT
            ];

            if (empty($profile['subaccount_code'])) {
                // Create new subaccount
                $response = $paymentService->createSubaccount($subaccountData);
                if (!$response['status']) throw new Exception("Paystack Error: " . $response['message']);
                $subaccountCode = $response['data']['subaccount_code'];
            } else {
                // Update existing subaccount
                $response = $paymentService->updateSubaccount($profile['subaccount_code'], $subaccountData);
                if (!$response['status']) throw new Exception("Paystack Error: " . $response['message']);
                $subaccountCode = $profile['subaccount_code'];
            }

            // Save to DB
            $updateStmt = $db->prepare("UPDATE landlord_profiles SET subaccount_code = ?, bank_name = ?, account_number = ?, bank_code = ? WHERE user_id = ?");
            $updateStmt->execute([$subaccountCode, $bankName, $accountNumber, $bankCode, $userId]);

            $_SESSION['success'] = "Bank details updated and subaccount synchronized successfully.";
            $this->redirect('landlord/bank-details');

        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            $this->redirect('landlord/bank-details');
        }
    }

    public function disputes() {
        $this->checkVerification();
        RbacMiddleware::check(['Landlord']);

        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];

        $stmt = $db->prepare("
            SELECT t.*, p.title as property_title, u.username as tenant_name, rr.status as request_status
            FROM transactions t
            JOIN rental_requests rr ON t.request_id = rr.id
            JOIN properties p ON rr.property_id = p.id
            JOIN users u ON t.user_id = u.id
            WHERE p.landlord_id = ? AND t.status IN ('escrow_hold', 'released', 'refunded', 'completed')
        ");
        $stmt->execute([$userId]);
        $payoutItems = $stmt->fetchAll();

        require_once "../views/layouts/landlord_layout_start.php";
        $this->view('landlord/disputes', ['payoutItems' => $payoutItems], false);
        require_once "../views/layouts/landlord_layout_end.php";
    }
}

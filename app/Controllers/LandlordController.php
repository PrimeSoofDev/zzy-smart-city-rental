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

        $this->view('landlord/dashboard', ['properties' => $myProperties]);
    }

    public function verify() {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) $this->redirect('auth/login');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT verification_status FROM landlord_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $status = $stmt->fetchColumn();

        if ($status === 'approved') {
            $this->redirect('landlord/dashboard');
        }

        $this->view('landlord/verification');
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
            $stmt = $db->prepare("INSERT INTO landlord_profiles (user_id, bvn_nin, address, verification_status)
                                 VALUES (?, ?, ?, 'pending')
                                 ON DUPLICATE KEY UPDATE bvn_nin = ?, address = ?");
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
        $this->view('landlord/add-property');
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
}

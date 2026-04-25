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

        require_once "../views/layouts/landlord_layout_start.php";
        $this->view('landlord/dashboard', ['properties' => $myProperties]);
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
}

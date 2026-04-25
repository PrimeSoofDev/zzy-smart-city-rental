<?php
class StaffController extends Controller {

    private function requireStaff() {
        RbacMiddleware::check(['Staff']);
    }

    private function renderStaffView($view, $data = []) {
        require_once "../views/layouts/staff_layout_start.php";
        $this->view('staff/' . $view, $data);
        require_once "../views/layouts/staff_layout_end.php";
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────
    public function dashboard() {
        $this->requireStaff();
        $db   = Database::getInstance()->getConnection();
        $uid  = $_SESSION['user_id'];

        $totalPending  = $db->query("SELECT COUNT(*) FROM properties WHERE status = 'pending_verification'")->fetchColumn();
        $totalApproved = $db->query("SELECT COUNT(*) FROM property_verifications WHERE result = 'approved' AND staff_id = $uid")->fetchColumn();
        $totalRejected = $db->query("SELECT COUNT(*) FROM property_verifications WHERE result = 'rejected' AND staff_id = $uid")->fetchColumn();
        $totalDone     = (int)$totalApproved + (int)$totalRejected;

        // Recent 5 pending properties
        $recentPending = $db->query("
            SELECT p.*, u.username AS landlord_name
            FROM properties p
            JOIN users u ON p.landlord_id = u.id
            WHERE p.status = 'pending_verification'
            ORDER BY p.created_at DESC
            LIMIT 5
        ")->fetchAll();

        // My recent verifications
        $recentActivity = $db->prepare("
            SELECT pv.*, p.title, p.address, p.price, p.property_type
            FROM property_verifications pv
            JOIN properties p ON pv.property_id = p.id
            WHERE pv.staff_id = ?
            ORDER BY pv.verified_at DESC
            LIMIT 5
        ");
        $recentActivity->execute([$uid]);
        $recentActivity = $recentActivity->fetchAll();

        $this->renderStaffView('dashboard', [
            'totalPending'   => $totalPending,
            'totalApproved'  => $totalApproved,
            'totalRejected'  => $totalRejected,
            'totalDone'      => $totalDone,
            'recentPending'  => $recentPending,
            'recentActivity' => $recentActivity,
        ]);
    }

    // ─── Pending Properties List ──────────────────────────────────────────────
    public function pending() {
        $this->requireStaff();
        $db = Database::getInstance()->getConnection();

        $type   = $_GET['type']   ?? 'all';
        $search = $_GET['search'] ?? '';

        $query = "
            SELECT p.*, u.username AS landlord_name
            FROM properties p
            JOIN users u ON p.landlord_id = u.id
            WHERE p.status = 'pending_verification'
        ";
        $params = [];

        if ($type !== 'all') {
            $query   .= " AND p.property_type = ?";
            $params[] = $type;
        }
        if ($search !== '') {
            $query   .= " AND (p.title LIKE ? OR p.address LIKE ? OR u.username LIKE ?)";
            $s        = '%' . $search . '%';
            $params   = array_merge($params, [$s, $s, $s]);
        }

        $query .= " ORDER BY p.created_at ASC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $properties = $stmt->fetchAll();

        $this->renderStaffView('pending', [
            'properties' => $properties,
            'type'       => $type,
            'search'     => $search,
        ]);
    }

    // ─── View Single Property for Verification ────────────────────────────────
    public function viewProperty() {
        $this->requireStaff();
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('staff/pending');

        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT p.*, u.username AS landlord_name, u.email AS landlord_email,
                   u.phone AS landlord_phone,
                   lp.bvn, lp.address AS landlord_address, lp.verification_status AS landlord_verified
            FROM properties p
            JOIN users u ON p.landlord_id = u.id
            LEFT JOIN landlord_profiles lp ON lp.user_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $property = $stmt->fetch();

        if (!$property) {
            $_SESSION['error'] = "Property not found.";
            $this->redirect('staff/pending');
        }

        // Images
        $imgStmt = $db->prepare("SELECT * FROM property_images WHERE property_id = ?");
        $imgStmt->execute([$id]);
        $images = $imgStmt->fetchAll();

        // Past verifications for this property
        $histStmt = $db->prepare("
            SELECT pv.*, u.username AS verified_by
            FROM property_verifications pv
            JOIN users u ON pv.staff_id = u.id
            WHERE pv.property_id = ?
            ORDER BY pv.verified_at DESC
        ");
        $histStmt->execute([$id]);
        $verificationHistory = $histStmt->fetchAll();

        $this->renderStaffView('view-property', [
            'property'            => $property,
            'images'              => $images,
            'verificationHistory' => $verificationHistory,
        ]);
    }

    // ─── Submit Verification (Approve / Reject) ───────────────────────────────
    public function submitVerification() {
        $this->requireStaff();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('staff/pending');

        $propertyId = $_POST['property_id'] ?? null;
        $result     = $_POST['result']      ?? null;
        $notes      = $this->sanitize($_POST['notes'] ?? '');
        $staffId    = $_SESSION['user_id'];

        if (!$propertyId || !in_array($result, ['approved', 'rejected'])) {
            $_SESSION['error'] = "Invalid submission. Please try again.";
            $this->redirect('staff/pending');
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            // Log to property_verifications
            $stmt = $db->prepare("
                INSERT INTO property_verifications (property_id, staff_id, notes, result)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$propertyId, $staffId, $notes, $result]);

            // Update property status
            $newStatus = ($result === 'approved') ? 'approved' : 'rejected';
            $db->prepare("UPDATE properties SET status = ? WHERE id = ?")->execute([$newStatus, $propertyId]);

            // Notify Landlord
            $propStmt = $db->prepare("SELECT landlord_id, title FROM properties WHERE id = ?");
            $propStmt->execute([$propertyId]);
            $prop = $propStmt->fetch();
            if ($prop) {
                $statusMsg = $newStatus === 'approved' ? 'has been APPROVED and is now live' : 'has been REJECTED. Please check the notes';
                $msg = "Your property '{$prop['title']}' {$statusMsg}.";
                Notification::send($prop['landlord_id'], $msg);
            }

            $db->commit();

            $label = $result === 'approved' ? 'approved' : 'rejected';
            $_SESSION['success'] = "Property has been {$label} successfully.";
            $this->redirect('staff/pending');

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "Error processing verification: " . $e->getMessage();
            $this->redirect('staff/view-property?id=' . $propertyId);
        }
    }

    // ─── My Verification History ──────────────────────────────────────────────
    public function history() {
        $this->requireStaff();
        $db  = Database::getInstance()->getConnection();
        $uid = $_SESSION['user_id'];

        $filter = $_GET['filter'] ?? 'all';

        $query = "
            SELECT pv.*, p.title, p.address, p.price, p.property_type, p.status AS property_status,
                   u.username AS landlord_name
            FROM property_verifications pv
            JOIN properties p ON pv.property_id = p.id
            JOIN users u ON p.landlord_id = u.id
            WHERE pv.staff_id = ?
        ";
        $params = [$uid];

        if ($filter !== 'all') {
            $query   .= " AND pv.result = ?";
            $params[] = $filter;
        }

        $query .= " ORDER BY pv.verified_at DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $records = $stmt->fetchAll();

        $this->renderStaffView('history', [
            'records' => $records,
            'filter'  => $filter,
        ]);
    }
}

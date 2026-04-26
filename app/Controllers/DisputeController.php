<?php
// app/Controllers/DisputeController.php

class DisputeController extends Controller {

    public function raise() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }

        $userId = $_SESSION['user_id'];
        $requestId = $_POST['request_id'] ?? null;
        $reason = trim($_POST['reason'] ?? '');

        if (!$requestId || !$reason) {
            header('Location: ' . APP_URL . '/tenant/dashboard?error=invalid_request');
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // 1. Verify that the user is actually part of this rental request
        $stmt = $db->prepare("SELECT tenant_id, landlord_id FROM rental_requests WHERE id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();

        if (!$request || ($request['tenant_id'] != $userId && $request['landlord_id'] != $userId)) {
            header('Location: ' . APP_URL . '/tenant/dashboard?error=unauthorized');
            exit;
        }

        // 2. Check if a dispute already exists
        $stmt = $db->prepare("SELECT id FROM disputes WHERE request_id = ? AND status IN ('open', 'resolving')");
        $stmt->execute([$requestId]);
        if ($stmt->fetch()) {
            header('Location: ' . APP_URL . '/tenant/dashboard?error=dispute_exists');
            exit;
        }

        // 3. Lock the transaction status to escrow_hold to prevent automatic/manual release
        $stmt = $db->prepare("UPDATE transactions SET status = 'escrow_hold' WHERE request_id = ?");
        $stmt->execute([$requestId]);

        // 4. Create the dispute record
        $stmt = $db->prepare("INSERT INTO disputes (request_id, raised_by, reason, status) VALUES (?, ?, ?, 'open')");
        $stmt->execute([$requestId, $userId, $reason]);

        header('Location: ' . APP_URL . '/dispute/portal?request_id=' . $requestId . '&success=raised');
    }

    public function uploadEvidence() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $disputeId = $_POST['dispute_id'] ?? null;
        $description = trim($_POST['description'] ?? '');

        if (!$disputeId) {
            header('Location: ' . APP_URL . '/dispute/portal?error=no_dispute');
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // Verify the user is a party to this dispute
        $stmt = $db->prepare("
            SELECT d.id FROM disputes d
            JOIN rental_requests rr ON d.request_id = rr.id
            WHERE d.id = ? AND (rr.tenant_id = ? OR rr.landlord_id = ?)
        ");
        $stmt->execute([$disputeId, $userId, $userId]);
        if (!$stmt->fetch()) {
            header('Location: ' . APP_URL . '/dispute/portal?error=unauthorized');
            exit;
        }

        if (!isset($_FILES['evidence'])) {
            header('Location: ' . APP_URL . '/dispute/portal?error=no_file');
            exit;
        }

        $file = $_FILES['evidence'];
        $uploadDir = 'public/uploads/disputes/' . $disputeId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Validate file type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($mimeType, $allowedTypes)) {
            header('Location: ' . APP_URL . '/dispute/portal?error=invalid_file');
            exit;
        }

        $uniqueName = uniqid('ev_', true) . '_' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $destination = $uploadDir . $uniqueName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $stmt = $db->prepare("INSERT INTO dispute_evidence (dispute_id, uploaded_by, file_path, file_type, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$disputeId, $userId, $destination, $mimeType, $description]);
            header('Location: ' . APP_URL . '/dispute/portal?request_id=' . $_POST['request_id'] . '&success=uploaded');
        } else {
            header('Location: ' . APP_URL . '/dispute/portal?error=upload_failed');
        }
    }

    public function portal() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }

        $userId = $_SESSION['user_id'];
        $requestId = $_GET['request_id'] ?? null;

        if (!$requestId) {
            header('Location: ' . APP_URL . '/tenant/dashboard');
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // Fetch dispute details
        $stmt = $db->prepare("SELECT d.*, r.amount FROM disputes d JOIN rental_requests r ON d.request_id = r.id WHERE d.request_id = ?");
        $stmt->execute([$requestId]);
        $dispute = $stmt->fetch();

        if (!$dispute) {
            header('Location: ' . APP_URL . '/tenant/dashboard?error=no_dispute');
            exit;
        }

        // Fetch evidence
        $stmt = $db->prepare("SELECT e.*, u.username as user_name FROM dispute_evidence e JOIN users u ON e.uploaded_by = u.id WHERE e.dispute_id = ? ORDER BY e.created_at ASC");
        $stmt->execute([$dispute['id']]);
        $evidence = $stmt->fetchAll();

        $this->view('dispute/portal', [
            'dispute' => $dispute,
            'evidence' => $evidence,
            'requestId' => $requestId
        ]);
    }
}

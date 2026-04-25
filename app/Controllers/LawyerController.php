<?php
class LawyerController extends Controller {

    private function requireLawyer() {
        RbacMiddleware::check(['Lawyer']);
    }

    private function renderLawyerView($view, $data = []) {
        require_once "../views/layouts/lawyer_layout_start.php";
        $this->view('lawyer/' . $view, $data);
        require_once "../views/layouts/lawyer_layout_end.php";
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────
    public function dashboard() {
        $this->requireLawyer();
        $db  = Database::getInstance()->getConnection();
        $uid = $_SESSION['user_id'];

        // Counts
        $totalPaidRequests = $db->query("SELECT COUNT(*) FROM rental_requests WHERE status = 'paid'")->fetchColumn();
        $myDrafts    = $db->prepare("SELECT COUNT(*) FROM agreements WHERE lawyer_id = ? AND status = 'draft'");
        $myDrafts->execute([$uid]);
        $myDrafts    = $myDrafts->fetchColumn();

        $mySigned    = $db->prepare("SELECT COUNT(*) FROM agreements WHERE lawyer_id = ? AND status = 'signed'");
        $mySigned->execute([$uid]);
        $mySigned    = $mySigned->fetchColumn();

        $myExpired   = $db->prepare("SELECT COUNT(*) FROM agreements WHERE lawyer_id = ? AND status = 'expired'");
        $myExpired->execute([$uid]);
        $myExpired   = $myExpired->fetchColumn();

        // Recent paid requests needing attention
        $recentRequests = $db->query("
            SELECT rr.*, p.title AS property_title, p.price, p.address,
                   tu.username AS tenant_name, lu.username AS landlord_name,
                   (SELECT status FROM agreements ag WHERE ag.request_id = rr.id ORDER BY ag.id DESC LIMIT 1) AS agreement_status
            FROM rental_requests rr
            JOIN properties p ON rr.property_id = p.id
            JOIN users tu ON rr.tenant_id = tu.id
            JOIN users lu ON p.landlord_id = lu.id
            WHERE rr.status = 'paid'
            ORDER BY rr.request_date DESC
            LIMIT 5
        ")->fetchAll();

        // My recent agreements
        $recentAgreements = $db->prepare("
            SELECT ag.*, rr.status AS request_status,
                   p.title AS property_title, p.price,
                   tu.username AS tenant_name, lu.username AS landlord_name
            FROM agreements ag
            JOIN rental_requests rr ON ag.request_id = rr.id
            JOIN properties p ON rr.property_id = p.id
            JOIN users tu ON rr.tenant_id = tu.id
            JOIN users lu ON p.landlord_id = lu.id
            WHERE ag.lawyer_id = ?
            ORDER BY ag.id DESC
            LIMIT 5
        ");
        $recentAgreements->execute([$uid]);
        $recentAgreements = $recentAgreements->fetchAll();

        $this->renderLawyerView('dashboard', [
            'totalPaidRequests' => $totalPaidRequests,
            'myDrafts'          => $myDrafts,
            'mySigned'          => $mySigned,
            'myExpired'         => $myExpired,
            'recentRequests'    => $recentRequests,
            'recentAgreements'  => $recentAgreements,
        ]);
    }

    // ─── Paid Rental Requests (needing agreement) ─────────────────────────────
    public function requests() {
        $this->requireLawyer();
        $db     = Database::getInstance()->getConnection();
        $filter = $_GET['filter'] ?? 'pending';

        // pending = no agreement drafted yet
        // drafted = agreement exists but not signed
        if ($filter === 'pending') {
            $stmt = $db->query("
                SELECT rr.*, p.title AS property_title, p.price, p.address, p.property_type,
                       tu.username AS tenant_name, tu.email AS tenant_email,
                       lu.username AS landlord_name, lu.email AS landlord_email
                FROM rental_requests rr
                JOIN properties p ON rr.property_id = p.id
                JOIN users tu ON rr.tenant_id = tu.id
                JOIN users lu ON p.landlord_id = lu.id
                WHERE rr.status = 'paid'
                AND NOT EXISTS (SELECT 1 FROM agreements ag WHERE ag.request_id = rr.id)
                ORDER BY rr.request_date DESC
            ");
        } else {
            $stmt = $db->query("
                SELECT rr.*, p.title AS property_title, p.price, p.address, p.property_type,
                       tu.username AS tenant_name, tu.email AS tenant_email,
                       lu.username AS landlord_name, lu.email AS landlord_email,
                       ag.status AS agreement_status
                FROM rental_requests rr
                JOIN properties p ON rr.property_id = p.id
                JOIN users tu ON rr.tenant_id = tu.id
                JOIN users lu ON p.landlord_id = lu.id
                JOIN agreements ag ON ag.request_id = rr.id
                WHERE rr.status = 'paid'
                ORDER BY rr.request_date DESC
            ");
        }
        $requests = $stmt->fetchAll();

        $this->renderLawyerView('requests', [
            'requests' => $requests,
            'filter'   => $filter,
        ]);
    }

    // ─── Draft Agreement for a Request ───────────────────────────────────────
    public function draftAgreement() {
        $this->requireLawyer();
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('lawyer/requests');

        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT rr.*, p.title AS property_title, p.price, p.address, p.property_type,
                   tu.username AS tenant_name, tu.email AS tenant_email, tu.phone AS tenant_phone,
                   lu.username AS landlord_name, lu.email AS landlord_email, lu.phone AS landlord_phone,
                   tp.address AS tenant_address, lp.address AS landlord_address
            FROM rental_requests rr
            JOIN properties p ON rr.property_id = p.id
            JOIN users tu ON rr.tenant_id = tu.id
            JOIN users lu ON p.landlord_id = lu.id
            LEFT JOIN tenant_profiles tp ON tp.user_id = tu.id
            LEFT JOIN landlord_profiles lp ON lp.user_id = lu.id
            WHERE rr.id = ? AND rr.status = 'paid'
        ");
        $stmt->execute([$id]);
        $request = $stmt->fetch();

        if (!$request) {
            $_SESSION['error'] = "Rental request not found or not yet paid.";
            $this->redirect('lawyer/requests');
        }

        // Check if agreement already drafted
        $existingAg = $db->prepare("SELECT * FROM agreements WHERE request_id = ?");
        $existingAg->execute([$id]);
        $existingAgreement = $existingAg->fetch();

        $this->renderLawyerView('draft-agreement', [
            'request'           => $request,
            'existingAgreement' => $existingAgreement,
        ]);
    }

    // ─── Save / Submit Agreement ──────────────────────────────────────────────
    public function saveAgreement() {
        $this->requireLawyer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('lawyer/requests');

        $requestId   = $_POST['request_id']   ?? null;
        $terms       = $this->sanitize($_POST['terms'] ?? '');
        $rentAmount  = floatval($_POST['rent_amount'] ?? 0);
        $duration    = $this->sanitize($_POST['duration'] ?? '');
        $startDate   = $_POST['start_date'] ?? null;
        $lawyerId    = $_SESSION['user_id'];

        if (!$requestId || !$terms) {
            $_SESSION['error'] = "Request ID and agreement terms are required.";
            $this->redirect('lawyer/requests');
        }

        // Build a document_path (we'll store the text content as a .txt file)
        $uploadDir = "../public/uploads/agreements/";
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName    = 'agreement_req' . $requestId . '_' . time() . '.txt';
        $filePath    = $uploadDir . $fileName;
        $docRelPath  = 'uploads/agreements/' . $fileName;

        // Build the text document
        $content  = "=== ZZY SMART RENTAL AGREEMENT ===\n\n";
        $content .= "Request ID : #" . $requestId . "\n";
        $content .= "Rent Amount: ₦" . number_format($rentAmount, 2) . " per annum\n";
        $content .= "Duration   : " . $duration . "\n";
        $content .= "Start Date : " . $startDate . "\n";
        $content .= "Drafted by : Lawyer ID " . $lawyerId . "\n";
        $content .= "Date Drafted: " . date('Y-m-d H:i:s') . "\n\n";
        $content .= "--- TERMS & CONDITIONS ---\n\n";
        $content .= $terms;

        file_put_contents($filePath, $content);

        $db = Database::getInstance()->getConnection();

        try {
            // Upsert: insert or update if already exists
            $existing = $db->prepare("SELECT id FROM agreements WHERE request_id = ?");
            $existing->execute([$requestId]);
            $agId = $existing->fetchColumn();

            if ($agId) {
                $db->prepare("UPDATE agreements SET document_path = ?, status = 'draft', lawyer_id = ? WHERE id = ?")
                   ->execute([$docRelPath, $lawyerId, $agId]);
            } else {
                $db->prepare("INSERT INTO agreements (request_id, lawyer_id, document_path, status) VALUES (?, ?, ?, 'draft')")
                   ->execute([$requestId, $lawyerId, $docRelPath]);
                $agId = $db->lastInsertId();
            }

            $_SESSION['success'] = "Agreement draft saved successfully.";
            $this->redirect('lawyer/view-agreement?id=' . $agId);

        } catch (Exception $e) {
            $_SESSION['error'] = "Error saving agreement: " . $e->getMessage();
            $this->redirect('lawyer/draft-agreement?id=' . $requestId);
        }
    }

    // ─── View a Single Agreement ──────────────────────────────────────────────
    public function viewAgreement() {
        $this->requireLawyer();
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('lawyer/agreements');

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT ag.*, rr.status AS request_status, rr.tenant_id,
                   p.title AS property_title, p.price, p.address, p.property_type,
                   tu.username AS tenant_name, tu.email AS tenant_email,
                   lu.username AS landlord_name, lu.email AS landlord_email,
                   la.username AS lawyer_name
            FROM agreements ag
            JOIN rental_requests rr ON ag.request_id = rr.id
            JOIN properties p ON rr.property_id = p.id
            JOIN users tu ON rr.tenant_id = tu.id
            JOIN users lu ON p.landlord_id = lu.id
            JOIN users la ON ag.lawyer_id = la.id
            WHERE ag.id = ?
        ");
        $stmt->execute([$id]);
        $agreement = $stmt->fetch();

        if (!$agreement) {
            $_SESSION['error'] = "Agreement not found.";
            $this->redirect('lawyer/agreements');
        }

        // Load document text
        $docContent = '';
        $docPath = "../public/" . $agreement['document_path'];
        if (file_exists($docPath)) {
            $docContent = file_get_contents($docPath);
        }

        $this->renderLawyerView('view-agreement', [
            'agreement'  => $agreement,
            'docContent' => $docContent,
        ]);
    }

    // ─── Mark Agreement as Signed ─────────────────────────────────────────────
    public function signAgreement() {
        $this->requireLawyer();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('lawyer/agreements');

        $agId = $_POST['agreement_id'] ?? null;
        if (!$agId) {
            $_SESSION['error'] = "Agreement ID is required.";
            $this->redirect('lawyer/agreements');
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();

        try {
            $db->prepare("UPDATE agreements SET status = 'signed', signed_at = NOW() WHERE id = ?")
               ->execute([$agId]);

            // Mark rental request as completed
            $reqStmt = $db->prepare("SELECT request_id FROM agreements WHERE id = ?");
            $reqStmt->execute([$agId]);
            $requestId = $reqStmt->fetchColumn();

            $db->prepare("UPDATE rental_requests SET status = 'completed' WHERE id = ?")
               ->execute([$requestId]);

            $db->commit();
            $_SESSION['success'] = "Agreement signed! Rental request marked as completed.";
            $this->redirect('lawyer/view-agreement?id=' . $agId);

        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "Error signing agreement: " . $e->getMessage();
            $this->redirect('lawyer/view-agreement?id=' . $agId);
        }
    }

    // ─── All Agreements ───────────────────────────────────────────────────────
    public function agreements() {
        $this->requireLawyer();
        $db     = Database::getInstance()->getConnection();
        $uid    = $_SESSION['user_id'];
        $filter = $_GET['filter'] ?? 'all';

        $query = "
            SELECT ag.*, rr.status AS request_status,
                   p.title AS property_title, p.price,
                   tu.username AS tenant_name, lu.username AS landlord_name
            FROM agreements ag
            JOIN rental_requests rr ON ag.request_id = rr.id
            JOIN properties p ON rr.property_id = p.id
            JOIN users tu ON rr.tenant_id = tu.id
            JOIN users lu ON p.landlord_id = lu.id
            WHERE ag.lawyer_id = ?
        ";
        $params = [$uid];

        if ($filter !== 'all') {
            $query   .= " AND ag.status = ?";
            $params[] = $filter;
        }
        $query .= " ORDER BY ag.id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $agreements = $stmt->fetchAll();

        $this->renderLawyerView('agreements', [
            'agreements' => $agreements,
            'filter'     => $filter,
        ]);
    }
}

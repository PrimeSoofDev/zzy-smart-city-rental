<?php
class StaffController extends Controller {
    public function dashboard() {
        RbacMiddleware::check(['Staff']);
        $db = Database::getInstance()->getConnection();
        $properties = $db->query("SELECT * FROM properties WHERE status = 'pending_verification'")->fetchAll();
        $this->view('staff/dashboard', ['properties' => $properties]);
    }

    public function verify() {
        RbacMiddleware::check(['Staff']);
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'rejected';

        if ($id) {
            $propModel = new Property();
            $propModel->updateStatus($id, $status);
        }
        $this->redirect('staff/dashboard');
    }
}

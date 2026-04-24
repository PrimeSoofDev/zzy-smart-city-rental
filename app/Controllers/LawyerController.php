<?php
class LawyerController extends Controller {
    public function dashboard() {
        RbacMiddleware::check(['Lawyer']);
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT rr.*, p.title FROM rental_requests rr JOIN properties p ON rr.property_id = p.id WHERE rr.status = 'paid'");
        $stmt->execute();
        $requests = $stmt->fetchAll();
        $this->view('lawyer/dashboard', ['requests' => $requests]);
    }
}

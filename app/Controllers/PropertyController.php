<?php
class PropertyController extends Controller {
    public function index() {
        $propModel = new Property();
        $properties = $propModel->getAllApproved();
        $this->view('tenant/dashboard', ['properties' => $properties]);
    }

    public function add() {
        RbacMiddleware::check(['Landlord']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $propModel = new Property();
            $data = [
                'landlord_id' => $_SESSION['user_id'],
                'title' => $this->sanitize($_POST['title']),
                'description' => $this->sanitize($_POST['description']),
                'address' => $this->sanitize($_POST['address']),
                'price' => $_POST['price'],
                'rooms' => $_POST['rooms'],
                'bathrooms' => $_POST['bathrooms'],
                'type' => $_POST['type']
            ];
            if ($propModel->create($data)) {
                $this->redirect('landlord/dashboard');
            }
        }
        $this->view('landlord/add_property');
    }

    public function detail() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('tenant/dashboard');

        $propModel = new Property();
        $property = $propModel->findById($id);
        $this->view('tenant/property_detail', ['property' => $property]);
    }
}

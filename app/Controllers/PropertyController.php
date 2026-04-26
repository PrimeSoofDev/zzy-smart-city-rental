<?php
class PropertyController extends Controller {
    public function index() {
        $userId = $_SESSION['user_id'] ?? null;
        $role = $_SESSION['role'] ?? null;

        if ($userId && $role) {
            if ($role === 'Landlord') {
                $this->redirect('landlord/dashboard');
            } elseif ($role === 'Tenant') {
                $this->redirect('tenant/dashboard');
            } elseif ($role === 'Admin') {
                $this->redirect('admin/dashboard');
            }
        }

        $propModel = new Property();
        $properties = $propModel->getAllApproved();
        $content = PageContent::getPage('home');

        $this->view('home/index', [
            'properties' => $properties,
            'content' => $content
        ]);
    }

    public function findHomes() {
        $propModel = new Property();
        $properties = $propModel->getAllApproved();
        $content = PageContent::getPage('find_homes');
        $this->view('home/find_homes', [
            'properties' => $properties,
            'content' => $content
        ]);
    }

    public function howItWorks() {
        $content = PageContent::getPage('how_it_works');
        $this->view('home/how_it_works', ['content' => $content]);
    }

    public function pricing() {
        $content = PageContent::getPage('pricing');
        $this->view('home/pricing', ['content' => $content]);
    }

    public function support() {
        $content = PageContent::getPage('support');
        $this->view('home/support', ['content' => $content]);
    }

    public function searchMap() {
        $north = $_GET['north'] ?? null;
        $south = $_GET['south'] ?? null;
        $east = $_GET['east'] ?? null;
        $west = $_GET['west'] ?? null;
        $query = $_GET['q'] ?? null;

        if (!$north || !$south || !$east || !$west) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid map boundaries provided']);
            return;
        }

        $propModel = new Property();
        $properties = $propModel->getPropertiesInBounds($north, $south, $east, $west, $query);

        header('Content-Type: application/json');
        echo json_encode($properties);
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

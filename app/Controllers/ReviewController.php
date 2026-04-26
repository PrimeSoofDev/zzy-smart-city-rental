<?php

class ReviewController extends Controller {
    public function submit() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'reviewer_id' => $_SESSION['user_id'],
                'reviewee_id' => $_POST['reviewee_id'],
                'request_id' => $_POST['request_id'],
                'rating' => $_POST['rating'],
                'comment' => $this->sanitize($_POST['comment'])
            ];

            if (Review::create($data)) {
                $_SESSION['success'] = "Review submitted successfully!";
            } else {
                $_SESSION['error'] = "Failed to submit review.";
            }

            $redirect = $_SESSION['role'] === 'Landlord' ? 'landlord/disputes' : 'tenant/disputes';
            $this->redirect($redirect);
        }
    }

    public function adminIndex() {
        RbacMiddleware::check(['Admin', 'Staff']);
        $reviews = Review::getAll();
        
        require_once "../views/layouts/admin_layout_start.php";
        $this->view('admin/pages/reviews', ['reviews' => $reviews]);
        require_once "../views/layouts/admin_layout_end.php";
    }

    public function toggleStatus() {
        RbacMiddleware::check(['Admin', 'Staff']);
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? null;

        if ($id && $status) {
            if (Review::updateStatus($id, $status)) {
                $_SESSION['success'] = "Review status updated!";
            }
        }

        $this->redirect('admin/reviews');
    }
}

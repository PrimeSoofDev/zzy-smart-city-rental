<?php
require_once 'app/Models/Review.php';

class ReviewController {
    public function submit() {
        Auth::requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'reviewer_id' => $_SESSION['user_id'],
                'reviewee_id' => $_POST['reviewee_id'],
                'request_id' => $_POST['request_id'],
                'rating' => $_POST['rating'],
                'comment' => $_POST['comment']
            ];

            if (Review::create($data)) {
                $_SESSION['success'] = "Review submitted successfully!";
            } else {
                $_SESSION['error'] = "Failed to submit review.";
            }

            $redirect = $_SESSION['user_role'] === 'landlord' ? '/landlord/disputes' : '/tenant/disputes';
            header("Location: " . APP_URL . $redirect);
            exit;
        }
    }

    public function adminIndex() {
        Auth::requireRole('admin', 'staff');
        $reviews = Review::getAll();
        
        $view = 'views/admin/pages/reviews.php';
        include 'views/layouts/admin_layout.php';
    }

    public function toggleStatus() {
        Auth::requireRole('admin', 'staff');
        $id = $_GET['id'];
        $status = $_GET['status'];

        if (Review::updateStatus($id, $status)) {
            $_SESSION['success'] = "Review status updated!";
        }

        header("Location: " . APP_URL . "/admin/reviews");
        exit;
    }
}

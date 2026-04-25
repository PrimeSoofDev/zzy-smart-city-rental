<?php
class Controller {
    public function view($view, $data = []) {
        extract($data);
        // Only use default layout if NOT in admin section and NOT a standalone guest page
        $isAdmin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
        $guestViews = ['home/index', 'home/find_homes', 'home/how_it_works', 'home/pricing', 'home/support'];
        $isStandaloneGuest = in_array($view, $guestViews);

        if (!$isAdmin && !$isStandaloneGuest) {
            require_once "../views/layouts/header.php";
            require_once "../views/$view.php";
            require_once "../views/layouts/footer.php";
        } else {
            // For admin views and standalone guest pages, load only the view file
            require_once "../views/$view.php";
        }
    }

    protected function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }

    protected function redirect($url) {
        header("Location: " . APP_URL . "/" . $url);
        exit;
    }
}

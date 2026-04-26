<?php
class Controller {
    public function view($view, $data = []) {
        extract($data);
        // Only use default layout if NOT in admin section and NOT a standalone guest page
        $isAdmin = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
        // Portals usually have 'landlord', 'tenant', 'staff', 'lawyer' etc. in the URL
        $isPortal = preg_match('/(landlord|tenant|staff|lawyer|messages|notifications|profile)/i', $_SERVER['REQUEST_URI']);
        $guestViews = ['home/index', 'home/find_homes', 'home/how_it_works', 'home/pricing', 'home/support'];
        $isStandaloneGuest = in_array($view, $guestViews);

        if (!$isAdmin && !$isPortal && !$isStandaloneGuest) {
            require_once "../views/layouts/header.php";
            require_once "../views/$view.php";
            require_once "../views/layouts/footer.php";
        } else {
            // For portals and admin, we manually wrap layouts or they provide their own
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

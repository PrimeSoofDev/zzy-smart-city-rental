<?php
class Controller {
    public function view($view, $data = []) {
        extract($data);
        // Only use default layout if NOT in admin section
        if (strpos($_SERVER['REQUEST_URI'], '/admin/') === false) {
            require_once "../views/layouts/header.php";
            require_once "../views/$view.php";
            require_once "../views/layouts/footer.php";
        } else {
            // For admin views, we just load the file since the Controller/Router handles the layout
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

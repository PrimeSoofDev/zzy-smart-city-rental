<?php
class Router {
    protected $routes = [];

    public function add($route, $controller, $method) {
        $this->routes[$route] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch($url) {
        // 1. Remove query string from URL
        if (strpos($url, '?') !== false) {
            $url = explode('?', $url)[0];
        }

        // 2. Log the raw URI for debugging (you can check this in Apache logs)
        // error_log("Raw URI: " . $url);

        // 3. Handle the base directory manually for XAMPP subfolder or custom domains
        // We remove the base folder prefix dynamically
        if (defined('APP_BASE_PATH') && !empty(APP_BASE_PATH)) {
            $url = str_replace(APP_BASE_PATH, '', $url);
        }

        // 4. Clean the URL
        $url = trim($url, '/');

        // Map empty strings (root) to '/'
        $url = ($url === '') ? '/' : $url;

        // DEBUG: Uncomment the line below if you still see 404 to see exactly what is being matched
        // echo "<!-- Matching route: " . $url . " -->";

        if (array_key_exists($url, $this->routes)) {
            $controllerName = $this->routes[$url]['controller'];
            $method = $this->routes[$url]['method'];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $method)) {
                    $controller->$method();
                    return;
                }
            }
        }

        // Map admin page requests to a generic admin view handler
        if (strpos($url, 'admin/pages/') === 0) {
             $page = str_replace('admin/pages/', '', $url);
             // This is a simplification; in a real app, you'd use a Controller
             // For now we call the view directly to show the UI
             require_once "../views/layouts/admin_layout_start.php";
             require_once "../views/admin/pages/" . $page . ".php";
             require_once "../views/layouts/admin_layout_end.php";
             return;
        }

        // 5. Proper 404 Response
        http_response_code(404);
        header("HTTP/1.1 404 Not Found");
        echo "<div style='font-family: sans-serif; padding: 20px;'>";
        echo "<h1 style='color: #c00;'>404 - Page Not Found</h1>";
        echo "<p>The system is looking for the route: <b style='background: #eee; padding: 2px 5px;'>" . htmlspecialchars($url) . "</b></p>";
        echo "<p>This means your URL <code>" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</code> was processed into the route above.</p>";
        echo "<hr><small>Try visiting <a href='" . APP_URL . "/'>Home</a> or <a href='" . APP_URL . "/auth/login'>Login</a></small>";
        echo "</div>";
        exit;
    }
}

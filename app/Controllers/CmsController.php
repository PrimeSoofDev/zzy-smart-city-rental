<?php
class CmsController extends Controller {
    public function index() {
        RbacMiddleware::check(['Admin', 'Staff']);
        
        $role = $_SESSION['role'];
        $pages = ['home', 'how_it_works', 'pricing', 'support'];
        $contents = [];
        foreach ($pages as $p) {
            $contents[$p] = PageContent::getPage($p);
        }

        $settings = null;
        if ($role === 'Admin') {
            $settings = SiteSetting::getAll();
        }

        $this->view($role === 'Admin' ? 'admin/cms' : 'staff/cms', [
            'contents' => $contents,
            'settings' => $settings
        ]);
    }

    public function updatePage() {
        RbacMiddleware::check(['Admin', 'Staff']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $page = $_POST['page_name'];
            $section = $_POST['section_name'];
            $key = $_POST['content_key'];
            $value = $_POST['content_value']; // No sanitize here because we might want HTML

            $model = new PageContent();
            if ($model->update($page, $section, $key, $value)) {
                $role = strtolower($_SESSION['role']);
                $this->redirect("$role/cms?success=Page content updated");
            }
        }
    }

    public function updateSettings() {
        RbacMiddleware::check(['Admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new SiteSetting();
            
            // Handle logo upload
            if (!empty($_FILES['logo']['name'])) {
                $logoPath = $this->uploadFile($_FILES['logo'], 'uploads/branding/');
                if ($logoPath) $model->update('logo_url', 'public/' . $logoPath);
            }

            // Handle favicon upload
            if (!empty($_FILES['favicon']['name'])) {
                $faviconPath = $this->uploadFile($_FILES['favicon'], 'uploads/branding/');
                if ($faviconPath) $model->update('favicon_url', 'public/' . $faviconPath);
            }

            // Other settings
            if (isset($_POST['site_name'])) {
                $model->update('site_name', $this->sanitize($_POST['site_name']));
            }

            $this->redirect('admin/cms?success=Site settings updated');
        }
    }

    private function uploadFile($file, $dir) {
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $target = $dir . $filename;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $target;
        }
        return false;
    }

    public function trackVisitor() {
        // This is a public endpoint
        $db = Database::getInstance()->getConnection();
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $url = $_POST['url'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;
        $action = $_POST['action'] ?? 'visit';

        if ($action === 'scroll') {
            $stmt = $db->prepare("UPDATE visitor_logs SET has_scrolled = 1 WHERE ip_address = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ORDER BY id DESC LIMIT 1");
            $stmt->execute([$ip]);
            echo json_encode(['status' => 'success']);
            return;
        }

        // Prevent spam logs for same session in last 30 mins
        $check = $db->prepare("SELECT id FROM visitor_logs WHERE ip_address = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
        $check->execute([$ip]);
        if ($check->fetch()) {
            echo json_encode(['status' => 'already_tracked']);
            return;
        }

        // Try to get location
        $location = null;
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}");
            if ($response) {
                $data = json_decode($response, true);
                if ($data && $data['status'] === 'success') {
                    $location = json_encode([
                        'city' => $data['city'] ?? 'Unknown',
                        'country' => $data['country'] ?? 'Unknown',
                        'region' => $data['regionName'] ?? 'Unknown'
                    ]);
                }
            }
        } catch (Exception $e) {}

        $stmt = $db->prepare("INSERT INTO visitor_logs (ip_address, user_agent, location_data, user_id, referer, page_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ip, $ua, $location, $userId, $referer, $url]);

        echo json_encode(['status' => 'logged']);
    }
}

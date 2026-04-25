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
}

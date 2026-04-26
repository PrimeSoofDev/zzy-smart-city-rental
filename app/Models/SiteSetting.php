<?php
class SiteSetting {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function get($key, $default = '') {
        $db = Database::getInstance()->getConnection();
        
        // 1. Check system_settings first (Preferred)
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        if ($result && !empty($result['setting_value'])) {
            return $result['setting_value'];
        }

        // 2. Mapping old site_settings keys to system_settings keys
        $mappings = [
            'site_name' => 'platform_name',
            'support_email' => 'platform_email',
            'support_phone' => 'platform_phone'
        ];

        if (isset($mappings[$key])) {
            $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
            $stmt->execute([$mappings[$key]]);
            $result = $stmt->fetch();
            if ($result && !empty($result['setting_value'])) {
                return $result['setting_value'];
            }
        }

        // 3. Fallback to site_settings table (Legacy)
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        return ($result && !empty($result['setting_value'])) ? $result['setting_value'] : $default;
    }

    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM system_settings");
        $results = $stmt->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function update($key, $value) {
        $stmt = $this->db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
        return $stmt->execute([$value, $key]);
    }
}

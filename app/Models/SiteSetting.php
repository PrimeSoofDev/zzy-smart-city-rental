<?php
class SiteSetting {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function get($key, $default = '') {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return ($result && !empty($result['setting_value'])) ? $result['setting_value'] : $default;
    }

    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM site_settings");
        $results = $stmt->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function update($key, $value) {
        $stmt = $this->db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        return $stmt->execute([$value, $key]);
    }
}

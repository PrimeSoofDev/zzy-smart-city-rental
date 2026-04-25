<?php
class PageContent {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public static function getPage($pageName) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT section_name, content_key, content_value FROM page_contents WHERE page_name = ?");
        $stmt->execute([$pageName]);
        $results = $stmt->fetchAll();
        
        $content = [];
        foreach ($results as $row) {
            $content[$row['section_name']][$row['content_key']] = $row['content_value'];
        }
        return $content;
    }

    public function update($pageName, $sectionName, $key, $value) {
        $stmt = $this->db->prepare("UPDATE page_contents SET content_value = ? WHERE page_name = ? AND section_name = ? AND content_key = ?");
        return $stmt->execute([$value, $pageName, $sectionName, $key]);
    }
}

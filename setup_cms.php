<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

$db = Database::getInstance()->getConnection();

// Create tables
$db->query("CREATE TABLE IF NOT EXISTS site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$db->query("CREATE TABLE IF NOT EXISTS page_contents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_name VARCHAR(50) NOT NULL,
    section_name VARCHAR(50) NOT NULL,
    content_key VARCHAR(50) NOT NULL,
    content_value TEXT,
    UNIQUE KEY unique_content (page_name, section_name, content_key)
)");

// Seed initial settings
$settings = [
    ['site_name', 'ZZY Smart Rental'],
    ['logo_url', ''],
    ['favicon_url', ''],
    ['primary_color', '#2563eb']
];

foreach ($settings as $s) {
    $stmt = $db->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
    $stmt->execute($s);
}

// Seed Home Page content
$contents = [
    ['home', 'hero', 'title', 'Discover your <br> <span class="text-gradient">Future Home</span> <br> using Intelligence.'],
    ['home', 'hero', 'subtitle', 'ZZY Smart Rental uses advanced AI matching to find the perfect living space based on your lifestyle, budget, and location preferences.'],
    ['how_it_works', 'intro', 'title', 'The Future of Renting is <span class="text-gradient">Simple</span>.'],
    ['how_it_works', 'intro', 'subtitle', 'We\'ve automated the entire rental lifecycle, from discovery to legal agreements and payments.'],
    ['pricing', 'intro', 'title', 'Transparent <span class="text-gradient">Pricing</span>.'],
    ['pricing', 'intro', 'subtitle', 'No hidden fees. We believe in complete transparency for both tenants and landlords.'],
    ['support', 'intro', 'title', 'We\'re here to <br><span class="text-gradient">Help</span>.'],
    ['support', 'intro', 'subtitle', 'Whether you\'re a tenant looking for a home or a landlord needing assistance with a listing, our team is ready to support you.']
];

foreach ($contents as $c) {
    $stmt = $db->prepare("INSERT IGNORE INTO page_contents (page_name, section_name, content_key, content_value) VALUES (?, ?, ?, ?)");
    $stmt->execute($c);
}

echo "CMS Tables created and seeded successfully!";

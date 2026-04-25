<?php
require 'config/config.php';
require 'app/Core/Database.php';

$db = Database::getInstance()->getConnection();

echo "--- Current Pending Tenants ---\n";
$tenants = $db->query("SELECT u.id, u.username, tp.verification_status FROM users u JOIN tenant_profiles tp ON u.id = tp.user_id WHERE tp.verification_status = 'pending'")->fetchAll();
foreach ($tenants as $t) {
    echo "ID: {$t['id']} | User: {$t['username']} | Status: {$t['verification_status']}\n";
}

echo "\n--- Current Pending Landlords ---\n";
$landlords = $db->query("SELECT u.id, u.username, lp.verification_status FROM users u JOIN landlord_profiles lp ON u.id = lp.user_id WHERE lp.verification_status = 'pending'")->fetchAll();
foreach ($landlords as $l) {
    echo "ID: {$l['id']} | User: {$l['username']} | Status: {$l['verification_status']}\n";
}
?>

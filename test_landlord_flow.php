<?php
require 'config/config.php';
require 'app/Core/Database.php';

$db = Database::getInstance()->getConnection();

try {
    // 1. Create Test User
    $username = 'test_landlord_' . time();
    $email = $username . '@example.com';
    $password = password_hash('password123', PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO users (username, email, password, status, is_active) VALUES (?, ?, ?, 'pending', 1)");
    $stmt->execute([$username, $email, $password]);
    $userId = $db->lastInsertId();

    echo "Created Test User: $username (ID: $userId)\n";

    // 2. Create Pending Landlord Profile
    $stmt = $db->prepare("INSERT INTO landlord_profiles (user_id, verification_status) VALUES (?, 'pending')");
    $stmt->execute([$userId]);
    echo "Created Pending Landlord Profile for User ID: $userId\n";

    // 3. Simulation: Test Access (Stage 1: Pending)
    echo "\n--- Testing Access (Pending) ---\n";
    $stmt = $db->prepare("SELECT verification_status FROM landlord_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $status = $stmt->fetchColumn();

    if ($status !== 'approved') {
        echo "SUCCESS: Access Blocked. Landlord is redirected to /landlord/verify (Status: $status)\n";
    } else {
        echo "FAILURE: Access granted unexpectedly!\n";
    }

    // 4. Simulation: Admin Approval (Stage 2)
    echo "\n--- Simulating Admin Approval ---\n";
    $stmt = $db->prepare("UPDATE landlord_profiles SET verification_status = 'approved' WHERE user_id = ?");
    $stmt->execute([$userId]);

    $stmt = $db->prepare("UPDATE users SET status = 'verified' WHERE id = ?");
    $stmt->execute([$userId]);

    echo "Admin approved Landlord User ID: $userId\n";

    // 5. Simulation: Test Access (Stage 3: Approved)
    echo "\n--- Testing Access (Approved) ---\n";
    $stmt = $db->prepare("SELECT verification_status FROM landlord_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $status = $stmt->fetchColumn();

    if ($status === 'approved') {
        echo "SUCCESS: Access Granted. Landlord can now enter dashboard (Status: $status)\n";
    } else {
        echo "FAILURE: Access blocked unexpectedly! (Status: $status)\n";
    }

} catch (Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
}
?>

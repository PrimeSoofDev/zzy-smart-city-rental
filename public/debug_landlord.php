<?php
require '../config/config.php';
require '../app/Core/Database.php';

session_start();

echo "<h1>Landlord Debug Page</h1>";

$userId = $_SESSION['user_id'] ?? null;
echo "<strong>Session User ID:</strong> " . ($userId ?? 'NOT SET') . "<br>";

if ($userId) {
    $db = Database::getInstance()->getConnection();

    // Check landlord_profiles
    $stmt = $db->prepare("SELECT * FROM landlord_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch();

    if ($profile) {
        echo "<strong>Profile Found!</strong><br>";
        echo "Verification Status: '" . $profile['verification_status'] . "'<br>";
        echo "Status Length: " . strlen($profile['verification_status']) . " characters<br>";

        if (trim($profile['verification_status']) === 'approved') {
            echo "<span style='color:green'>✓ Status is 'approved' (after trim)</span><br>";
        } else {
            echo "<span style='color:red'>✗ Status is NOT 'approved'</span><br>";
        }
    } else {
        echo "<span style='color:red'><strong>No profile found in landlord_profiles for this User ID!</strong></span><br>";
    }

    // Check users table
    $stmt = $db->prepare("SELECT status, is_active FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    echo "<br><strong>User Table Info:</strong><br>";
    echo "Status: " . ($user['status'] ?? 'N/A') . "<br>";
    echo "Is Active: " . ($user['is_active'] ?? 'N/A') . "<br>";
} else {
    echo "<p style='color:red'>You are not logged in. Please log in first.</p>";
}
?>

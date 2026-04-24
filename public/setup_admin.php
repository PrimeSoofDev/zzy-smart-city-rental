<?php
require_once '../config/config.php';
require_once '../app/Core/Database.php';

$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$db = Database::getInstance()->getConnection();

try {
    // 1. Create Admin User
    $stmt = $db->prepare("INSERT INTO users (username, email, password, status) VALUES (?, ?, ?, 'verified')
                          ON DUPLICATE KEY UPDATE password = ?");
    $stmt->execute(['admin', 'admin@zzyrental.com', $hashedPassword, $hashedPassword]);

    // Get the user ID safely
    $userIdStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $userIdStmt->execute(['admin@zzyrental.com']);
    $userId = $userIdStmt->fetchColumn();

    if (!$userId) {
        throw new Exception("Could not retrieve User ID after creation.");
    }

    // 2. Find Admin Role ID
    $roleStmt = $db->prepare("SELECT id FROM roles WHERE role_name = 'Admin'");
    $roleStmt->execute();
    $roleId = $roleStmt->fetchColumn();

    if (!$roleId) {
        // EMERGENCY: If Admin role doesn't exist, create it now so the script doesn't fail
        echo "Admin role missing. Creating it now...<br>";
        $db->prepare("INSERT IGNORE INTO roles (role_name) VALUES ('Admin')")->execute();
        $roleStmt->execute();
        $roleId = $roleStmt->fetchColumn();
    }

    // 3. Assign Role (Clear existing roles first to avoid duplicates)
    $db->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$userId]);
    $assignStmt = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
    $assignStmt->execute([$userId, $roleId]);

    echo "✅ Default Admin created and role assigned successfully!<br>";
    echo "<b>Username:</b> admin<br>";
    echo "<b>Email:</b> admin@zzyrental.com<br>";
    echo "<b>Password:</b> $password<br>";
    echo "<b>Login URL:</b> <a href='auth/login'>Click here to login</a><br>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>

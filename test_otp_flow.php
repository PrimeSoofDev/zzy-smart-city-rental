<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';
require_once 'app/Core/Model.php';
require_once 'app/Models/Otp.php';
require_once 'app/Services/EmailService.php';
require_once 'app/Services/SmsService.php';
require_once 'app/Services/OtpService.php';

// Mocking environment for CLI
if (!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'w'));

function logTest($msg) {
    echo "[TEST] " . $msg . "\n";
}

$otpService = new OtpService();
$testEmail = "test@example.com";
$testChannel = "email";

logTest("Starting OTP Flow Test for {$testEmail} via {$testChannel}");

// 1. Send OTP
logTest("Sending OTP...");
$sent = $otpService->sendOtp($testEmail, $testChannel);
if ($sent) {
    logTest("OTP sent successfully (Note: If no SMTP/Brevo config, it used mail() which might fail in CLI but logic continues)");
} else {
    logTest("OTP send failed (Expected if no config)");
}

// 2. Fetch the OTP from DB to verify (since we can't see the email)
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM otps WHERE identifier = ? AND channel = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$testEmail, $testChannel]);
$otpData = $stmt->fetch();

if (!$otpData) {
    logTest("FAIL: No OTP found in database.");
    exit(1);
}

logTest("Found OTP in DB. ID: {$otpData['id']}");

// We need the plain text OTP to verify, but it's hashed in DB.
// For testing purposes, let's create a known OTP manually or just trust the hashing logic.
// Actually, let's modify the OtpService briefly to return the plain OTP for this test script if needed, 
// or just use a manual insert for a known code.

$plainCode = "123456";
$hashedCode = password_hash($plainCode, PASSWORD_DEFAULT);
$expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

logTest("Manually inserting known OTP '{$plainCode}' for verification test...");
$db->prepare("INSERT INTO otps (identifier, otp_code, channel, expires_at, status) VALUES (?, ?, ?, ?, 'pending')")
   ->execute([$testEmail, $hashedCode, $testChannel, $expiresAt]);

// 3. Verify Correct OTP
logTest("Verifying correct OTP '{$plainCode}'...");
$result = $otpService->verifyOtp($testEmail, $plainCode, $testChannel);
if ($result['status'] === 'success') {
    logTest("SUCCESS: OTP verified correctly.");
} else {
    logTest("FAIL: Could not verify correct OTP. Message: " . $result['message']);
}

// 4. Verify Incorrect OTP
logTest("Verifying incorrect OTP '000000'...");
$result = $otpService->verifyOtp($testEmail, "000000", $testChannel);
if ($result['status'] === 'error') {
    logTest("SUCCESS: Incorrect OTP rejected correctly.");
} else {
    logTest("FAIL: Incorrect OTP was accepted.");
}

// 5. Check Attempt Limiting
logTest("Testing attempt limiting...");
$otpService->verifyOtp($testEmail, "111111", $testChannel);
$otpService->verifyOtp($testEmail, "222222", $testChannel);
$result = $otpService->verifyOtp($testEmail, "333333", $testChannel);

if ($result['message'] === 'Too many failed attempts' || $result['message'] === 'No pending OTP found') {
    logTest("SUCCESS: Attempt limiting worked.");
} else {
    logTest("FAIL: Attempt limiting failed. Message: " . $result['message']);
}

logTest("Test Suite Completed.");

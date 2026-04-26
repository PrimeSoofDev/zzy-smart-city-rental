<?php

class OtpService {
    private $otpModel;
    private $emailService;
    private $smsService;

    public function __construct() {
        $this->otpModel = new Otp();
        $this->emailService = new EmailService();
        $this->smsService = new SmsService();
    }

    public function sendOtp($identifier, $channel, $userId = null) {
        // Invalidate any existing pending OTPs for this identifier/channel
        $this->otpModel->invalidatePrevious($identifier, $channel);

        // Generate 6-digit OTP
        $otpCode = sprintf("%06d", mt_rand(0, 999999));
        
        // Hash for storage
        $hashedOtp = password_hash($otpCode, PASSWORD_DEFAULT);
        
        // Expiry in 24 hours
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Save to DB
        if ($this->otpModel->create($userId, $identifier, $hashedOtp, $channel, $expiresAt)) {
            // Send via channel
            if ($channel === 'email') {
                return $this->emailService->sendOtp($identifier, $otpCode);
            } elseif ($channel === 'phone') {
                return $this->smsService->sendOtp($identifier, $otpCode);
            }
        }

        return false;
    }

    public function verifyOtp($identifier, $otpCode, $channel) {
        $otpData = $this->otpModel->findLatestPending($identifier, $channel);

        if (!$otpData) {
            return ['status' => 'error', 'message' => 'No pending OTP found'];
        }

        // Check expiry
        if (strtotime($otpData['expires_at']) < time()) {
            $this->otpModel->updateStatus($otpData['id'], 'expired');
            return ['status' => 'error', 'message' => 'OTP has expired'];
        }

        // Check attempts
        if ($otpData['attempts'] >= 3) {
            $this->otpModel->updateStatus($otpData['id'], 'expired');
            return ['status' => 'error', 'message' => 'Too many failed attempts'];
        }

        // Verify hash
        if (password_verify($otpCode, $otpData['otp_code'])) {
            $this->otpModel->updateStatus($otpData['id'], 'verified');
            return ['status' => 'success', 'message' => 'OTP verified successfully'];
        } else {
            $this->otpModel->incrementAttempts($otpData['id']);
            return ['status' => 'error', 'message' => 'Invalid OTP code'];
        }
    }
}

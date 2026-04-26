<?php

class OtpController extends Controller {
    private $otpService;

    public function __construct() {
        parent::__construct();
        $this->otpService = new OtpService();
    }

    public function sendOtp() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $identifier = $input['identifier'] ?? null;
        $channel = $input['channel'] ?? null; // 'email' or 'phone'
        $userId = $input['user_id'] ?? null;

        if (!$identifier || !$channel) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Identifier and channel are required'], 400);
            return;
        }

        if (!in_array($channel, ['email', 'phone'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid channel'], 400);
            return;
        }

        $success = $this->otpService->sendOtp($identifier, $channel, $userId);

        if ($success) {
            $this->jsonResponse(['status' => 'success', 'message' => "OTP sent to {$channel}"]);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Failed to send OTP'], 500);
        }
    }

    public function verifyOtp() {
        $input = json_decode(file_get_contents('php://input'), true);

        $identifier = $input['identifier'] ?? null;
        $otpCode = $input['otp'] ?? null;
        $channel = $input['channel'] ?? null;

        if (!$identifier || !$otpCode || !$channel) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Identifier, OTP, and channel are required'], 400);
            return;
        }

        $result = $this->otpService->verifyOtp($identifier, $otpCode, $channel);

        if ($result['status'] === 'success') {
            $this->jsonResponse($result);
        } else {
            $this->jsonResponse($result, 400);
        }
    }

    private function jsonResponse($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }
}

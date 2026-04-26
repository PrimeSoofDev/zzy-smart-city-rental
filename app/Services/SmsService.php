<?php

class SmsService {
    private $brevoApiKey;
    private $senderName;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'brevo_api_key'");
        $stmt->execute();
        $this->brevoApiKey = $stmt->fetchColumn();

        // Optional: Get a custom sender name if configured
        $this->senderName = "ZZY Rental";
    }

    public function sendOtp($to, $otp) {
        $message = "Your ZZY Rental OTP is: {$otp}. Expires in 24 hours.";
        
        if ($this->brevoApiKey) {
            return $this->sendViaBrevo($to, $message);
        }
        
        error_log("SMS Service: Brevo API key missing.");
        return false;
    }

    private function sendViaBrevo($to, $message) {
        $url = "https://api.brevo.com/v3/transactionalSMS/sms";
        
        $data = [
            'sender' => $this->senderName,
            'recipient' => $to,
            'content' => $message,
            'type' => 'transactional'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "api-key: " . $this->brevoApiKey,
            "Content-Type: application/json"
        ]);
        
        // Fix for common local development SSL issues
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("Brevo SMS CURL Error: " . $error);
            return false;
        }

        $result = json_decode($response, true);
        
        if (!isset($result['messageId'])) {
            error_log("Brevo SMS API Error Response: " . $response);
            return false;
        }

        return true;
    }
}

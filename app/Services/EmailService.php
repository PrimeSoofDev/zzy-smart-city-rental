<?php

class EmailService {
    private $platformEmail;
    private $brevoApiKey;

    public function __construct() {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'platform_email'");
        $stmt->execute();
        $this->platformEmail = $stmt->fetchColumn() ?: 'support@zzyrental.com';

        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'brevo_api_key'");
        $stmt->execute();
        $this->brevoApiKey = $stmt->fetchColumn();
    }

    public function sendOtp($to, $otp) {
        $subject = "Your OTP Code";
        $body = "Your verification code is: <b>{$otp}</b>\n\nThis code will expire in 24 hours.";
        
        if ($this->brevoApiKey) {
            return $this->sendViaBrevo($to, $subject, $body);
        } else {
            return $this->sendViaMail($to, $subject, strip_tags($body));
        }
    }

    private function sendViaMail($to, $subject, $message) {
        $headers = "From: " . $this->platformEmail;
        return mail($to, $subject, $message, $headers);
    }

    private function sendViaBrevo($to, $subject, $content) {
        $url = "https://api.brevo.com/v3/smtp/email";
        $data = [
            "sender" => ["email" => $this->platformEmail, "name" => "ZZY Rental"],
            "to" => [["email" => $to]],
            "subject" => $subject,
            "htmlContent" => $content
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
            error_log("Brevo API CURL Error: " . $error);
            return false;
        }

        $result = json_decode($response, true);
        
        if (!isset($result['messageId'])) {
            error_log("Brevo API Error Response: " . $response);
            return false;
        }

        return true;
    }
}

<?php

class PaymentService {
    private $secretKey;
    private $baseUrl = "https://api.paystack.co";

    public function __construct() {
        $this->secretKey = PAYSTACK_SECRET_KEY;
    }

    /**
     * Get list of Nigerian banks
     */
    public function getBanks() {
        return $this->curlRequest("/bank?country=nigeria");
    }

    /**
     * Create a Paystack subaccount for a landlord
     */
    public function createSubaccount($data) {
        // Data should include: business_name, settlement_bank (code), account_number, percentage_charge
        return $this->curlRequest("/subaccount", "POST", $data);
    }

    /**
     * Update a Paystack subaccount
     */
    public function updateSubaccount($subaccountCode, $data) {
        return $this->curlRequest("/subaccount/{$subaccountCode}", "PUT", $data);
    }

    /**
     * Initialize a transaction with split payment
     */
    public function initializeTransaction($email, $amount, $reference, $callbackUrl, $subaccountCode) {
        $data = [
            'email' => $email,
            'amount' => $amount * 100, // Paystack expects kobo
            'reference' => $reference,
            'callback_url' => $callbackUrl,
            'subaccount' => $subaccountCode,
            'bearer' => 'subaccount' // Subaccount bears the Paystack charge
        ];

        return $this->curlRequest("/transaction/initialize", "POST", $data);
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction($reference) {
        return $this->curlRequest("/transaction/verify/" . rawurlencode($reference));
    }

    /**
     * Helper for CURL requests
     */
    private function curlRequest($endpoint, $method = "GET", $data = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->secretKey,
            "Content-Type: application/json"
        ]);

        if ($method === "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === "PUT") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("CURL Error: " . $error);
        }

        return json_decode($response, true);
    }
}

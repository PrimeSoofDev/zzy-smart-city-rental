<?php
class Otp extends Model {
    public function create($userId, $identifier, $otpCode, $channel, $expiresAt) {
        $stmt = $this->db->prepare("INSERT INTO otps (user_id, identifier, otp_code, channel, expires_at, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        return $stmt->execute([$userId, $identifier, $otpCode, $channel, $expiresAt]);
    }

    public function findLatestPending($identifier, $channel) {
        $stmt = $this->db->prepare("SELECT * FROM otps WHERE identifier = ? AND channel = ? AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$identifier, $channel]);
        return $stmt->fetch();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE otps SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function incrementAttempts($id) {
        $stmt = $this->db->prepare("UPDATE otps SET attempts = attempts + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function invalidatePrevious($identifier, $channel) {
        $stmt = $this->db->prepare("UPDATE otps SET status = 'expired' WHERE identifier = ? AND channel = ? AND status = 'pending'");
        return $stmt->execute([$identifier, $channel]);
    }
}

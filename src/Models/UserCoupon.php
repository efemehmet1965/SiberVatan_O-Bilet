<?php

class UserCoupon {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    private function generateUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function recordUsage($userId, $couponId) {
        $uuid = $this->generateUuid();
        $stmt = $this->pdo->prepare("INSERT INTO User_Coupons (id, user_id, coupon_id) VALUES (?, ?, ?)");
        if ($stmt->execute([$uuid, $userId, $couponId])) {
            return $uuid;
        }
        return false;
    }

    public function getUsageCount($couponId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM User_Coupons WHERE coupon_id = ?");
        $stmt->execute([$couponId]);
        return $stmt->fetchColumn();
    }
}
?>
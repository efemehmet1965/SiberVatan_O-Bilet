<?php

class Coupon {
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

    public function create($code, $discount, $limit, $expireDate, $companyId = null) {
        $uuid = $this->generateUuid();
        $sql = "INSERT INTO Coupons (id, code, discount, usage_limit, expire_date, company_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$uuid, $code, $discount, $limit, $expireDate, $companyId])) {
            return $uuid;
        }
        return false;
    }

    public function getByCompanyId($companyId) {
        $stmt = $this->pdo->prepare("SELECT * FROM Coupons WHERE company_id = ? ORDER BY expire_date DESC");
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGlobal() {
        $stmt = $this->pdo->query("SELECT * FROM Coupons WHERE company_id IS NULL ORDER BY expire_date DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCode($code) {
        $stmt = $this->pdo->prepare("SELECT * FROM Coupons WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function decrementUsage($couponId) {
        $sql = "UPDATE Coupons SET usage_limit = usage_limit - 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$couponId]);
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Coupons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $code, $discount, $limit, $expireDate) {
        $sql = "UPDATE Coupons SET code = ?, discount = ?, usage_limit = ?, expire_date = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$code, $discount, $limit, $expireDate, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Coupons WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByCompanyId($companyId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Coupons WHERE company_id = ?");
        $stmt->execute([$companyId]);
        return $stmt->fetchColumn();
    }

    public function isCouponValid($coupon, $tripCompanyId) {
        if (!$coupon) {
            return false;
        }
        // Check if expired
        if (strtotime($coupon['expire_date']) < time()) {
            return false;
        }
        // Check usage limit
        if ($coupon['usage_limit'] <= 0) {
            return false;
        }
        // Check company validity
        // If coupon company_id is null, it's a global coupon, so it's valid for any company.
        // Otherwise, the coupon's company_id must match the trip's company_id.
        if ($coupon['company_id'] !== null && $coupon['company_id'] !== $tripCompanyId) {
            return false;
        }
        return true;
    }

    public function decrementUsageLimit($couponId) {
        $sql = "UPDATE Coupons SET usage_limit = usage_limit - 1 WHERE id = ? AND usage_limit > 0";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$couponId]);
    }
}
?>
<?php

class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // UUID v4 Generator
    private function generateUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare('SELECT * FROM Users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($fullName, $email, $password) {
        $uuid = $this->generateUuid();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO Users (id, full_name, email, password) VALUES (?, ?, ?, ?)');
        if ($stmt->execute([$uuid, $fullName, $email, $hashedPassword])) {
            return $uuid;
        }
        return false;
    }
    
    public function findById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM Users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBalance($userId, $newBalance) {
        $stmt = $this->pdo->prepare('UPDATE Users SET balance = ? WHERE id = ?');
        return $stmt->execute([$newBalance, $userId]);
    }

    public function createCompanyAdmin($fullName, $email, $password, $companyId) {
        $uuid = $this->generateUuid();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare('INSERT INTO Users (id, full_name, email, password, role, company_id) VALUES (?, ?, ?, ?, \'Firma Admin\', ?)');
        if ($stmt->execute([$uuid, $fullName, $email, $hashedPassword, $companyId])) {
            return $uuid;
        }
        return false;
    }

    public function getCompanyAdmins() {
        $sql = "SELECT u.id, u.full_name, u.email, u.created_at, c.name as company_name 
                FROM Users u 
                LEFT JOIN Bus_Company c ON u.company_id = c.id
                WHERE u.role = ? ORDER BY u.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['Firma Admin']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCompanyAdmin($id, $fullName, $email, $companyId, $password = null) {
        $params = [$fullName, $email, $companyId];
        $sql = "UPDATE Users SET full_name = ?, email = ?, company_id = ?";

        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[] = $hashedPassword;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByCompanyId($companyId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Users WHERE company_id = ?");
        $stmt->execute([$companyId]);
        return $stmt->fetchColumn();
    }
}
?>
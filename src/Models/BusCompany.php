<?php

class BusCompany {
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

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM Bus_Company ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($name) {
        $uuid = $this->generateUuid();
        $stmt = $this->pdo->prepare("INSERT INTO Bus_Company (id, name) VALUES (?, ?)");
        if ($stmt->execute([$uuid, $name])) {
            return $uuid;
        }
        return false;
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM Bus_Company WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name) {
        $stmt = $this->pdo->prepare("UPDATE Bus_Company SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Bus_Company WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
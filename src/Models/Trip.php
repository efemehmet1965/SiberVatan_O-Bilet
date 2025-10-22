<?php

class Trip {
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

    public function search($departure, $destination, $date) {
        // Temel sorgu
        $sql = "SELECT Trips.*, Bus_Company.name as company_name 
                FROM Trips 
                JOIN Bus_Company ON Trips.company_id = Bus_Company.id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($departure)) {
            $sql .= " AND departure_city LIKE ?";
            $params[] = '%' . $departure . '%';
        }

        if (!empty($destination)) {
            $sql .= " AND destination_city LIKE ?";
            $params[] = '%' . $destination . '%';
        }

        if (!empty($date)) {
            $sql .= " AND DATE(departure_time) = ?";
            $params[] = $date;
        }
        
        $sql .= " ORDER BY departure_time ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findByIdWithCompany($id) {
        $sql = "SELECT Trips.*, Bus_Company.name as company_name, Bus_Company.logo_path 
                FROM Trips 
                JOIN Bus_Company ON Trips.company_id = Bus_Company.id 
                WHERE Trips.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByCompanyId($companyId) {
        $sql = "SELECT * FROM Trips WHERE company_id = ? ORDER BY departure_time DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($companyId, $departureCity, $destinationCity, $departureTime, $arrivalTime, $price, $capacity) {
        $uuid = $this->generateUuid();
        $sql = "INSERT INTO Trips (id, company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$uuid, $companyId, $departureCity, $destinationCity, $departureTime, $arrivalTime, $price, $capacity])) {
            return $uuid;
        }
        return false;
    }

    public function update($id, $departureCity, $destinationCity, $departureTime, $arrivalTime, $price, $capacity) {
        $sql = "UPDATE Trips SET 
                    departure_city = ?,
                    destination_city = ?,
                    departure_time = ?,
                    arrival_time = ?,
                    price = ?,
                    capacity = ?
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$departureCity, $destinationCity, $departureTime, $arrivalTime, $price, $capacity, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM Trips WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByCompanyId($companyId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Trips WHERE company_id = ?");
        $stmt->execute([$companyId]);
        return $stmt->fetchColumn();
    }

    public function getCompanyIdForTrip($tripId) {
        $stmt = $this->pdo->prepare("SELECT company_id FROM Trips WHERE id = ?");
        $stmt->execute([$tripId]);
        return $stmt->fetchColumn();
    }
}
?>
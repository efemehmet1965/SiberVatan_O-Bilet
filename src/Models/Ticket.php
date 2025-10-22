<?php

class Ticket {
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

    public function create($userId, $tripId, $totalPrice) {
        $uuid = $this->generateUuid();
        $stmt = $this->pdo->prepare(
            'INSERT INTO Tickets (id, user_id, trip_id, total_price) VALUES (?, ?, ?, ?)'
        );
        if ($stmt->execute([$uuid, $userId, $tripId, $totalPrice])) {
            return $uuid;
        }
        return false;
    }

    public function getTicketsByUserId($userId) {
        $sql = "SELECT 
                    t.id, t.total_price, t.status, t.created_at as purchase_date,
                    tr.departure_city, tr.destination_city, tr.departure_time,
                    bc.name as company_name
                FROM Tickets t
                JOIN Trips tr ON t.trip_id = tr.id
                JOIN Bus_Company bc ON tr.company_id = bc.id
                WHERE t.user_id = ?
                ORDER BY tr.departure_time DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findTicketById($ticketId) {
        $sql = "SELECT t.*, tr.departure_time FROM Tickets t JOIN Trips tr ON t.trip_id = tr.id WHERE t.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ticketId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($ticketId, $status) {
        $stmt = $this->pdo->prepare('UPDATE Tickets SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $ticketId]);
    }

    public function countByTripId($tripId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Tickets WHERE trip_id = ?");
        $stmt->execute([$tripId]);
        return $stmt->fetchColumn();
    }

    public function expireOldTickets($userId) {
        $sql = "UPDATE Tickets 
                SET status = 'expired' 
                WHERE user_id = ? AND status = 'active' AND trip_id IN (
                    SELECT id FROM Trips WHERE departure_time < CURRENT_TIMESTAMP
                )";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId]);
    }
}
?>
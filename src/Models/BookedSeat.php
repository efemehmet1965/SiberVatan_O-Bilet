<?php

class BookedSeat {
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

    public function getBookedSeatNumbersByTripId($tripId) {
        $stmt = $this->pdo->prepare(
            'SELECT bs.seat_number FROM Booked_Seats bs ' . 
            'JOIN Tickets t ON bs.ticket_id = t.id ' . 
            'WHERE t.trip_id = ? AND t.status = \'active\''
        );
        $stmt->execute([$tripId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function getSeatNumbersByTicketId($ticketId) {
        $stmt = $this->pdo->prepare("SELECT seat_number FROM Booked_Seats WHERE ticket_id = ? ORDER BY seat_number");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function bookSeats($ticketId, $seatNumbers) {
        $stmt = $this->pdo->prepare(
            'INSERT INTO Booked_Seats (id, ticket_id, seat_number) VALUES (?, ?, ?)'
        );
        foreach ($seatNumbers as $seat) {
            $uuid = $this->generateUuid();
            $stmt->execute([$uuid, $ticketId, $seat]);
        }
    }

    public function deleteByTicketId($ticketId) {
        $stmt = $this->pdo->prepare("DELETE FROM Booked_Seats WHERE ticket_id = ?");
        return $stmt->execute([$ticketId]);
    }
}
?>
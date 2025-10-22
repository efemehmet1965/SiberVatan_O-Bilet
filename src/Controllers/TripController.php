<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Trip.php';

class TripController extends BaseController {

    public function search() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tripModel = new Trip($this->pdo);
            
            $departure = $_POST['departure_city'] ?? '';
            $destination = $_POST['destination_city'] ?? '';
            $date = $_POST['trip_date'] ?? '';

            $trips = $tripModel->search($departure, $destination, $date);

            $this->view('trips/search_results', [
                'trips' => $trips,
                'searchParams' => ['departure' => $departure, 'destination' => $destination, 'date' => $date]
            ]);
        } else {
            // Post olmadan gelinirse anasayfaya yönlendir
            $this->redirect('');
        }
    }
    
    public function show($id = null) {
        if ($id === null) {
            http_response_code(404);
            echo "Sefer kimliği belirtilmedi.";
            return;
        }
        $tripModel = new Trip($this->pdo);
        $trip = $tripModel->findByIdWithCompany($id);
        
        if (!$trip) {
            http_response_code(404);
            echo "Sefer bulunamadı.";
            return;
        }
        
        require_once __DIR__ . '/../Models/BookedSeat.php';
        $bookedSeatModel = new BookedSeat($this->pdo);
        $bookedSeats = $bookedSeatModel->getBookedSeatNumbersByTripId($id);

        $this->view('trips/show', ['trip' => $trip, 'bookedSeats' => $bookedSeats]);
    }
}
?>
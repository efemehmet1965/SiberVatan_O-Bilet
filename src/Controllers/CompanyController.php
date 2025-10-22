<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/Trip.php';
require_once __DIR__ . '/../Models/Ticket.php';
require_once __DIR__ . '/../Models/Coupon.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Lib/cities.php';

class CompanyController extends BaseController {

    public function __construct($pdo) {
        parent::__construct($pdo);
        // Bu denetleyicideki tüm metodlar için Firma Admin yetkisi gerekli
        $this->authorize(['Firma Admin']);
    }

    public function dashboard() {
        $this->view('company/dashboard');
    }

    public function trips() {
        $tripModel = new Trip($this->pdo);
        $userModel = new User($this->pdo);
        $user = $userModel->findById($_SESSION['user_id']);

        $trips = $tripModel->findByCompanyId($user['company_id']); 
        $this->view('company/trips', ['trips' => $trips]);
    }

    public function createTrip() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- VALIDATION ---
            $departureCity = $_POST['departure_city'];
            $destinationCity = $_POST['destination_city'];
            $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);

            if ($departureCity === $destinationCity) {
                $this->setFlashMessage('Kalkış ve varış şehri aynı olamaz.', 'danger');
                $this->redirect('company/createTrip');
            }

            if ($capacity === false || $capacity < 10 || $capacity > 55) {
                $this->setFlashMessage('Koltuk sayısı 10 ile 55 arasında geçerli bir sayı olmalıdır.', 'danger');
                $this->redirect('company/createTrip');
            }
            // --- END VALIDATION ---

            $userModel = new User($this->pdo);
            $user = $userModel->findById($_SESSION['user_id']);
            $companyId = $user['company_id'];

            $tripModel = new Trip($this->pdo);
            $tripModel->create(
                $companyId,
                $departureCity,
                $destinationCity,
                $_POST['departure_time'],
                $_POST['arrival_time'],
                $_POST['price'],
                $capacity
            );

            $this->setFlashMessage('Sefer başarıyla oluşturuldu!');
            $this->redirect('company/trips');
        } else {
            $cities = getTurkishCities();
            sort($cities);
            $this->view('company/create_trip', ['cities' => $cities]);
        }
    }

    public function coupons() {
        $userModel = new User($this->pdo);
        $user = $userModel->findById($_SESSION['user_id']);

        $couponModel = new Coupon($this->pdo);
        $coupons = $couponModel->getByCompanyId($user['company_id']);
        $this->view('company/coupons', ['coupons' => $coupons]);
    }

    public function createCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User($this->pdo);
            $user = $userModel->findById($_SESSION['user_id']);

            $couponModel = new Coupon($this->pdo);
            $couponModel->create(
                $_POST['code'],
                $_POST['discount'],
                $_POST['usage_limit'],
                $_POST['expire_date'],
                $user['company_id']
            );
            $this->setFlashMessage('Firmaya özel kupon başarıyla oluşturuldu.');
            $this->redirect('company/coupons');
        } else {
            $this->view('company/create_coupon');
        }
    }

    public function editCoupon($id) {
        $couponModel = new Coupon($this->pdo);
        $userModel = new User($this->pdo);
        $user = $userModel->findById($_SESSION['user_id']);
        $coupon = $couponModel->findById($id);

        // Yetki kontrolü: Kupon bu firmaya mı ait?
        if (!$coupon || $coupon['company_id'] != $user['company_id']) {
            $this->setFlashMessage('Bu işlemi yapma yetkiniz yok.', 'danger');
            $this->redirect('company/coupons');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $couponModel->update(
                $id,
                $_POST['code'],
                $_POST['discount'],
                $_POST['usage_limit'],
                $_POST['expire_date']
            );
            $this->setFlashMessage('Kupon başarıyla güncellendi.');
            $this->redirect('company/coupons');
        } else {
            $this->view('company/edit_coupon', ['coupon' => $coupon]);
        }
    }

    public function deleteCoupon($id) {
        $couponModel = new Coupon($this->pdo);
        $userModel = new User($this->pdo);
        $user = $userModel->findById($_SESSION['user_id']);
        $coupon = $couponModel->findById($id);

        // Yetki kontrolü: Kupon bu firmaya mı ait?
        if (!$coupon || $coupon['company_id'] != $user['company_id']) {
            $this->setFlashMessage('Bu işlemi yapma yetkiniz yok.', 'danger');
            $this->redirect('company/coupons');
            return;
        }

        $couponModel->delete($id);
        $this->setFlashMessage('Kupon başarıyla silindi.', 'danger');
        $this->redirect('company/coupons');
    }
    
    public function editTrip($id) {
        $tripModel = new Trip($this->pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- VALIDATION ---
            $departureCity = $_POST['departure_city'];
            $destinationCity = $_POST['destination_city'];
            $capacity = filter_input(INPUT_POST, 'capacity', FILTER_VALIDATE_INT);

            if ($departureCity === $destinationCity) {
                $this->setFlashMessage('Kalkış ve varış şehri aynı olamaz.', 'danger');
                $this->redirect('company/editTrip/' . $id);
            }

            if ($capacity === false || $capacity < 10 || $capacity > 55) {
                $this->setFlashMessage('Koltuk sayısı 10 ile 55 arasında geçerli bir sayı olmalıdır.', 'danger');
                $this->redirect('company/editTrip/' . $id);
            }
            // --- END VALIDATION ---

            $tripModel->update(
                $id,
                $departureCity,
                $destinationCity,
                $_POST['departure_time'],
                $_POST['arrival_time'],
                $_POST['price'],
                $capacity
            );
            $this->setFlashMessage('Sefer başarıyla güncellendi.');
            $this->redirect('company/trips');
        } else {
            $trip = $tripModel->findByIdWithCompany($id);
            // Yetki kontrolü: Bu sefere bu firma admini erişebilir mi?
            $user = (new User($this->pdo))->findById($_SESSION['user_id']);
            if (!$trip || $trip['company_id'] != $user['company_id']) {
                $this->setFlashMessage('Bu işlemi yapma yetkiniz yok.', 'danger');
                $this->redirect('company/trips');
            }
            $cities = getTurkishCities();
            sort($cities);
            $this->view('company/edit_trip', ['trip' => $trip, 'cities' => $cities]);
        }
    }

        public function deleteTrip($id)

        {

            $tripModel = new Trip($this->pdo);

            $ticketModel = new Ticket($this->pdo);

    

            // Yetki kontrolü: Bu sefere bu firma admini erişebilir mi?

            $trip = $tripModel->findByIdWithCompany($id);

            $user = (new User($this->pdo))->findById($_SESSION['user_id']);

            if (!$trip || $trip['company_id'] != $user['company_id']) {

                $this->setFlashMessage('Bu işlemi yapma yetkiniz yok.', 'danger');

                $this->redirect('company/trips');

                return;

            }

    

            // Bu sefere ait satılmış bilet var mı?

            $ticketCount = $ticketModel->countByTripId($id);

            if ($ticketCount > 0) {

                $this->setFlashMessage('Bu sefere ait biletler bulunduğu için sefer silinemez.', 'danger');

                $this->redirect('company/trips');

                return;

            }

    

            $tripModel->delete($id);

            $this->setFlashMessage('Sefer başarıyla silindi.', 'danger');

            $this->redirect('company/trips');

        }

    }
?>
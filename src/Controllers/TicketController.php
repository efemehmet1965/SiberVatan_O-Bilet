<?php
declare(strict_types=1);

require_once 'BaseController.php';
require_once __DIR__ . '/../Models/User.php';
require_once __DIR__ . '/../Models/Trip.php';
require_once __DIR__ . '/../Models/Ticket.php';
require_once __DIR__ . '/../Models/BookedSeat.php';
require_once __DIR__ . '/../Models/Coupon.php';

class TicketController extends BaseController
{
    public function buy()
    {
        $this->authorize(['User']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('');
            return;
        }

        $this->verifyCsrfToken();

        // Güvenli parametre okuma
        $tripId = isset($_POST['trip_id']) ? (string)$_POST['trip_id'] : '';

        // Koltuk numaralarını temizle (int'e çevir, negatif/0 olanları at, tekrarı kaldır)
        $selectedSeatsRaw = $_POST['seats'] ?? [];
        $selectedSeats = array_values(array_unique(array_filter(array_map(static function ($v) {
            $n = (int)$v;
            return $n > 0 ? $n : null;
        }, (array)$selectedSeatsRaw))));

        $couponCode = trim((string)($_POST['coupon_code'] ?? ''));

        if (empty($tripId) || empty($selectedSeats)) {
            $this->setFlashMessage('Lütfen koltuk seçimi yapın.', 'danger');
            $this->redirect($tripId ? ('trip/show/' . $tripId) : '');
            return;
        }

        $userModel       = new User($this->pdo);
        $tripModel       = new Trip($this->pdo);
        $ticketModel     = new Ticket($this->pdo);
        $bookedSeatModel = new BookedSeat($this->pdo);
        $couponModel     = new Coupon($this->pdo);

        $user = $userModel->findById((string)$_SESSION['user_id']);
        $trip = $tripModel->findByIdWithCompany($tripId);

        if (!$trip) {
            $this->setFlashMessage('Sefer bilgileri alınamadı.', 'danger');
            $this->redirect('');
            return;
        }

        $seatCount  = count($selectedSeats);
        $unitPrice  = (float)$trip['price'];
        $totalPrice = $seatCount * $unitPrice;
        $finalPrice = $totalPrice;
        $discountAmount = 0.0;
        $coupon = null;

        if ($couponCode !== '') {
            $coupon = $couponModel->findByCode($couponCode);

            if ($coupon && $couponModel->isCouponValid($coupon, (string)$trip['company_id'])) {
                $discountAmount = ($totalPrice * (float)$coupon['discount']) / 100.0;
                $finalPrice = max(0.0, $totalPrice - $discountAmount);
                $this->setFlashMessage(
                    sprintf("'%s' kuponu uygulandı! İndirim: %.2f TL", htmlspecialchars($couponCode, ENT_QUOTES, 'UTF-8'), $discountAmount)
                );
            } else {
                $this->setFlashMessage('Geçersiz veya bu sefer için kullanılamayacak bir kupon girdiniz.', 'danger');
                $coupon = null;
            }
        }

        try {
            $this->pdo->beginTransaction();

            if (((float)$user['balance']) < $finalPrice) {
                throw new Exception('Yetersiz bakiye.');
            }

            // Koltuk çakışma kontrolü (yarış durumlarına karşı)
            $alreadyBooked = $bookedSeatModel->getBookedSeatNumbersByTripId($tripId);
            foreach ($selectedSeats as $seat) {
                if (in_array($seat, $alreadyBooked, true)) {
                    throw new Exception("Seçtiğiniz koltuklardan bazıları siz işlem yaparken satın alındı: Koltuk #{$seat}");
                }
            }

            $ticketId = $ticketModel->create((string)$user['id'], $tripId, $finalPrice);
            if (!$ticketId) {
                throw new Exception('Bilet oluşturulamadı.');
            }

            if ($coupon) {
                $couponModel->decrementUsageLimit((string)$coupon['id']);
            }

            $bookedSeatModel->bookSeats($ticketId, $selectedSeats);

            $userModel->updateBalance((string)$user['id'], (float)$user['balance'] - $finalPrice);

            $this->pdo->commit();
            $this->setFlashMessage('Biletiniz başarıyla satın alındı!');
            $this->redirect('user/account');
            return;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->setFlashMessage('Bilet alımı sırasında bir hata oluştu: ' . $e->getMessage(), 'danger');
            $this->redirect('trip/show/' . $tripId);
            return;
        }
    }

    public function applyCoupon()
    {
        $this->authorizeApi(['User']);
        $this->verifyCsrfTokenApi();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $couponCode = trim($input['coupon_code'] ?? '');
        $tripId = $input['trip_id'] ?? '';
        $totalPrice = (float)($input['total_price'] ?? 0.0);

        if (empty($couponCode) || empty($tripId) || $totalPrice <= 0) {
            echo json_encode(['success' => false, 'message' => 'Kupon kodu, sefer ID ve toplam tutar gereklidir.']);
            return;
        }

        $couponModel = new Coupon($this->pdo);
        $tripModel = new Trip($this->pdo);

        $coupon = $couponModel->findByCode($couponCode);
        $trip = $tripModel->findByIdWithCompany($tripId);

        if (!$trip) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz sefer.']);
            return;
        }

        if ($coupon && $couponModel->isCouponValid($coupon, (string)$trip['company_id'])) {
            $discountAmount = ($totalPrice * (float)$coupon['discount']) / 100.0;
            $finalPrice = max(0.0, $totalPrice - $discountAmount);

            echo json_encode([
                'success' => true,
                'message' => sprintf("Kupon uygulandı! İndirim: %.2f TL", $discountAmount),
                'discountAmount' => $discountAmount,
                'discountPercentage' => (float)$coupon['discount'],
                'finalPrice' => $finalPrice
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Geçersiz veya bu sefer için kullanılamayan bir kupon girdiniz.']);
        }
    }

    public function cancel($ticketId)
    {
        $this->authorize(['User']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->setFlashMessage('Geçersiz istek.', 'danger');
            $this->redirect('user/account');
            return;
        }

        $this->verifyCsrfToken();

        $ticketId = (string)$ticketId;

        $ticketModel     = new Ticket($this->pdo);
        $userModel       = new User($this->pdo);
        $bookedSeatModel = new BookedSeat($this->pdo);

        $ticket = $ticketModel->findTicketById($ticketId);

        if (!$ticket || (string)$ticket['user_id'] !== (string)$_SESSION['user_id']) {
            $this->setFlashMessage('Geçersiz bilet veya yetkisiz işlem.', 'danger');
            $this->redirect('user/account');
            return;
        }

        if (($ticket['status'] ?? '') !== 'active') {
            $this->setFlashMessage('Bu bilet zaten iptal edilmiş veya işlem görmüş.', 'warning');
            $this->redirect('user/account');
            return;
        }

        $departure_timestamp = strtotime((string)$ticket['departure_time']);
        if (($departure_timestamp - time()) <= 3600) {
            $this->setFlashMessage('Sefer saatine 1 saatten az kaldığı için bilet iptal edilemez.', 'danger');
            $this->redirect('user/account');
            return;
        }

        try {
            $this->pdo->beginTransaction();

            $ticketModel->updateStatus($ticketId, 'canceled');
            $bookedSeatModel->deleteByTicketId($ticketId);

            $user = $userModel->findById((string)$_SESSION['user_id']);
            $refund = (float)$ticket['total_price'];
            $userModel->updateBalance((string)$_SESSION['user_id'], (float)$user['balance'] + $refund);

            $this->pdo->commit();
            $this->setFlashMessage('Biletiniz başarıyla iptal edildi ve ücret hesabınıza iade edildi.');
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->setFlashMessage('İptal işlemi sırasında bir hata oluştu: ' . $e->getMessage(), 'danger');
        }

        $this->redirect('user/account');
        return;
    }

    public function generatePdf($ticketId)
    {
        $this->authorize(['User']);
        $ticketId = (string)$ticketId;

        $ticketModel = new Ticket($this->pdo);
        $ticket = $ticketModel->findTicketById($ticketId);

        if (!$ticket || (string)$ticket['user_id'] !== (string)$_SESSION['user_id']) {
            $this->setFlashMessage('Geçersiz bilet veya yetkisiz işlem.', 'danger');
            $this->redirect('user/account');
            return;
        }

        try {
            require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';

            $userModel = new User($this->pdo);
            $user = $userModel->findById((string)$ticket['user_id']);

            $tripModel = new Trip($this->pdo);
            $trip = $tripModel->findByIdWithCompany((string)$ticket['trip_id']);

            $bookedSeatModel = new BookedSeat($this->pdo);
            $seats = $bookedSeatModel->getSeatNumbersByTicketId((string)$ticket['id']);

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Siber Vatan O-Bilet');
            $pdf->SetTitle('Bilet - ' . $ticketId);
            $pdf->SetSubject('Yolcu Bilet Bilgileri');

            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->AddPage();

            // Üst şerit
            $headerHeight = 28;
            $pdf->SetFillColor(37, 42, 52);
            $pdf->Rect(0, 0, $pdf->getPageWidth(), $headerHeight, 'F');

            // LOGO: Windows yollarını normalize et
            $rootNormalized = rtrim(str_replace('\\', '/', (string)ROOT_PATH), '/');
            $logoPath = $rootNormalized . '/public/assets/img/logo.png';
            if (is_string($logoPath) && file_exists($logoPath)) {
                $pdf->Image($logoPath, 15, 5, 35, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }

            // Başlık
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('dejavusans', 'B', 16);
            $pdf->SetXY(-70, 8);
            $pdf->Cell(0, 10, 'Yolcu Bilet Belgesi', 0, 1, 'R', 0, '', 0, false, 'T', 'M');

            $pdf->SetFont('dejavusans', '', 10);
            $pdf->SetXY(-70, 16);
            $companyName = isset($trip['company_name']) ? (string)$trip['company_name'] : '';
            $pdf->Cell(0, 10, htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'), 0, 1, 'R', 0, '', 0, false, 'T', 'M');

            // İçerik
            $pdf->SetY($headerHeight + 10);
            $pdf->SetTextColor(0, 0, 0);

            $pnr = 'SVB-' . substr(strtoupper(md5((string)$ticket['id'])), 0, 6);

            $depCity  = htmlspecialchars((string)$trip['departure_city'], ENT_QUOTES, 'UTF-8');
            $destCity = htmlspecialchars((string)$trip['destination_city'], ENT_QUOTES, 'UTF-8');
            $depTime  = date('d.m.Y H:i', strtotime((string)$trip['departure_time']));
            $seatList = implode(', ', array_map('strval', (array)$seats));
            $totalPaid = number_format((float)$ticket['total_price'], 2);

            $html  = '<p><strong>PNR:</strong> ' . $pnr . '</p>';
            $html .= '<h3>Yolcu ve Sefer Bilgileri</h3>';
            $html .= '<table border="1" cellpadding="5" style="border-collapse: collapse;">';
            $html .= '<tr><td style="width: 30%;"><strong>Yolcu Adı Soyadı</strong></td><td style="width: 70%;">' . htmlspecialchars((string)$user['full_name'], ENT_QUOTES, 'UTF-8') . '</td></tr>';
            $html .= '<tr><td><strong>Güzergah</strong></td><td>' . $depCity . ' - ' . $destCity . '</td></tr>';
            $html .= '<tr><td><strong>Kalkış Zamanı</strong></td><td>' . $depTime . '</td></tr>';
            $html .= '<tr><td><strong>Koltuk Numaraları</strong></td><td>' . $seatList . '</td></tr>';
            $html .= '<tr><td><strong>Ödenen Toplam Tutar</strong></td><td>' . $totalPaid . ' TL</td></tr>';
            $html .= '</table>';

            $html .= '<br><br><p style="font-size:8px;"><i>Bu bilet, ibraz edilmesi halinde geçerlidir. Sefer saatinden en az 1 saat öncesine kadar iptal edilebilir. İptal işlemleri web sitemiz üzerinden yapılabilir. Keyifli bir yolculuk geçirmenizi dileriz.</i></p>';

            $pdf->writeHTML($html, true, false, true, false, '');

            // Tamponu temizle, PDF çıktısını gönder
            if (ob_get_length()) {
                ob_end_clean();
            }
            $pdf->Output('bilet-' . $ticketId . '.pdf', 'I');
            exit;

        } catch (Exception $e) {
            $this->setFlashMessage('PDF oluşturulurken bir hata oluştu: ' . $e->getMessage(), 'danger');
            $this->redirect('user/account');
            return;
        }
    }
}
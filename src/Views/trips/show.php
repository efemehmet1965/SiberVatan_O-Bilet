<?php
$departure_time = new DateTime($trip['departure_time']);
$arrival_time = new DateTime($trip['arrival_time']);
$duration = $departure_time->diff($arrival_time);
?>

<style>
    /* Bu sayfaya özel stiller - CSS önbellek sorununu aşmak için eklendi */
    .bus-container {
        background: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 15px;
        padding: 20px;
    }
    .seat-map {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        max-width: 250px;
        margin: 0 auto;
    }
    .seat {
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 2px solid #D9232D;
        border-radius: 7px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .seat.corridor { /* Koridor boşluğu için görünmez eleman */
        border: none;
        cursor: default;
    }
    .seat.booked {
        background-color: #252A34;
        border-color: #252A34;
        color: white;
        cursor: not-allowed;
    }
    .seat.selected {
        background-color: #198754;
        border-color: #198754;
        color: white;
        transform: scale(1.1);
    }
    .seat:not(.booked):not(.corridor):hover {
        background-color: #f8d7da;
    }
</style>

<form action="/sibervatanbilet/public/ticket/buy" method="POST" id="buyTicketForm">
    <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($trip['id']); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <div id="seats-input-container"></div>

    <div class="row g-4">
        <!-- Sol Taraf: Koltuk Seçimi -->
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <i class="bi bi-ui-checks-grid me-2"></i>Koltuk Seçimi
                </div>
                <div class="card-body">
                    <div class="bus-container">
                        <div class="d-flex justify-content-end mb-3">
                            <img src="/sibervatanbilet/public/assets/img/steering-wheel.svg" alt="Direksiyon" style="width: 40px; opacity: 0.5;">
                        </div>
                        <div class="seat-map" id="seat-map">
                            <!-- Koltuklar JavaScript ile burada oluşturulacak -->
                        </div>
                    </div>
                    <div class="d-flex justify-content-center align-items-center mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center me-3"><div class="seat selected" style="cursor: default; transform: none; width: 25px; height: 25px;"></div><span class="ms-2 small">Seçili</span></div>
                        <div class="d-flex align-items-center me-3"><div class="seat" style="cursor: default; width: 25px; height: 25px;"></div><span class="ms-2 small">Boş</span></div>
                        <div class="d-flex align-items-center"><div class="seat booked" style="cursor: default; width: 25px; height: 25px;"></div><span class="ms-2 small">Dolu</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sağ Taraf: Sefer ve Ödeme Bilgileri -->
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <i class="bi bi-info-circle me-2"></i>Sefer Bilgileri
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($trip['departure_city']); ?> <i class="bi bi-arrow-right"></i> <?php echo htmlspecialchars($trip['destination_city']); ?></h5>
                    <p class="card-text text-muted"><strong><?php echo htmlspecialchars($trip['company_name']); ?></strong></p>
                    <div class="row">
                        <div class="col-6"><p><strong>Tarih:</strong> <?php echo $departure_time->format('d.m.Y'); ?></p></div>
                        <div class="col-6"><p><strong>Kalkış:</strong> <?php echo $departure_time->format('H:i'); ?></p></div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-light"><i class="bi bi-credit-card me-2"></i>Ödeme Özeti</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Seçilen Koltuklar:</strong>
                            <span id="selected-seats-display" class="fw-bold">-</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Birim Fiyat:</strong>
                            <span><?php echo number_format($trip['price'], 2); ?> TL</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fs-4">
                            <strong>Toplam Tutar:</strong>
                            <strong id="total-price">0.00 TL</strong>
                        </li>
                    </ul>
                    
                    <div class="mt-3">
                        <label for="coupon_code" class="form-label">İndirim Kuponu</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="coupon_code" name="coupon_code" placeholder="Kupon kodunuzu girin">
                            <button class="btn btn-outline-secondary" type="button" id="apply-coupon-btn">Uygula</button>
                        </div>
                        <div id="coupon-result" class="mt-2"></div>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button type="submit" class="btn btn-success btn-lg mt-3 w-100" id="buy-button" disabled>Güvenli Ödeme Yap</button>
                    <?php else: ?>
                        <div class="alert alert-danger mt-3">Bilet alabilmek için lütfen <a href="/sibervatanbilet/public/user/login" class="alert-link">giriş yapın</a>.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- DATA --- //
    const tripId = "<?php echo htmlspecialchars($trip['id']); ?>";
    const companyId = "<?php echo htmlspecialchars($trip['company_id']); ?>";
    const capacity = <?php echo (int)$trip['capacity']; ?>;
    const bookedSeats = <?php echo json_encode($bookedSeats); ?>.map(String);
    const unitPrice = <?php echo (float)$trip['price']; ?>;
    const seatMap = document.getElementById('seat-map');
    let selectedSeats = new Set();
    let appliedCoupon = null; // Holds applied coupon data

    // --- UI ELEMENTS --- //
    const selectedSeatsDisplay = document.getElementById('selected-seats-display');
    const totalPriceDisplay = document.getElementById('total-price');
    const buyButton = document.getElementById('buy-button');
    const seatsInputContainer = document.getElementById('seats-input-container');

    // --- FUNCTIONS --- //
    function renderSeats() {
        seatMap.innerHTML = '';
        for (let i = 1; i <= capacity; i++) {
            const seat = document.createElement('div');
            seat.classList.add('seat');
            seat.dataset.seatNumber = i;
            seat.textContent = i;

            if (i % 4 === 3) { 
                const corridor = document.createElement('div');
                corridor.classList.add('seat', 'corridor');
                seatMap.appendChild(corridor);
            }

            if (bookedSeats.includes(String(i))) {
                seat.classList.add('booked');
            } else {
                seat.addEventListener('click', () => toggleSeat(i));
            }

            seatMap.appendChild(seat);
        }
    }

    function toggleSeat(seatNumber) {
        const seatNumberStr = String(seatNumber);
        const seatElement = seatMap.querySelector(`[data-seat-number='${seatNumberStr}']`);
        
        if (selectedSeats.has(seatNumberStr)) {
            selectedSeats.delete(seatNumberStr);
            seatElement.classList.remove('selected');
        } else {
            selectedSeats.add(seatNumberStr);
            seatElement.classList.add('selected');
        }
        updateSummary();
    }

    function updateSummary() {
        const selectedArray = Array.from(selectedSeats).sort((a, b) => a - b);
        selectedSeatsDisplay.textContent = selectedArray.length > 0 ? selectedArray.join(', ') : '-';

        let totalPrice = selectedArray.length * unitPrice;
        let finalPrice = totalPrice;

        if (appliedCoupon) {
            const discountAmount = (totalPrice * appliedCoupon.discountPercentage) / 100.0;
            finalPrice = Math.max(0.0, totalPrice - discountAmount);
        }

        totalPriceDisplay.textContent = `${finalPrice.toFixed(2)} TL`;

        seatsInputContainer.innerHTML = '';
        selectedArray.forEach(seatNum => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'seats[]';
            input.value = seatNum;
            seatsInputContainer.appendChild(input);
        });

        if(buyButton) buyButton.disabled = selectedArray.length === 0;
    }

    // --- INITIALIZATION --- //
    renderSeats();
    updateSummary();

    // --- AJAX for Coupon --- //
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const couponCodeInput = document.getElementById('coupon_code');
    const couponResultDiv = document.getElementById('coupon-result');

    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    if(applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            const couponCode = couponCodeInput.value;
            const currentTotalPrice = selectedSeats.size * unitPrice;

            if (!couponCode) {
                couponResultDiv.innerHTML = '<div class="alert alert-warning p-2">Lütfen bir kupon kodu girin.</div>';
                return;
            }

            fetch('/sibervatanbilet/public/ticket/applyCoupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    coupon_code: couponCode,
                    trip_id: tripId,
                    total_price: currentTotalPrice,
                    csrf_token: csrfToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    couponResultDiv.innerHTML = `<div class="alert alert-success p-2">${data.message}</div>`;
                    appliedCoupon = { 
                        code: couponCode, 
                        discountPercentage: data.discountPercentage 
                    };
                    updateSummary(); // Re-calculate price with discount
                } else {
                    couponResultDiv.innerHTML = `<div class="alert alert-danger p-2">${data.message}</div>`;
                    appliedCoupon = null; // Clear coupon if invalid
                    updateSummary(); // Re-calculate price without discount
                }
            })
            .catch(error => {
                console.error('Coupon AJAX Error:', error);
                couponResultDiv.innerHTML = '<div class="alert alert-danger p-2">Kupon uygulanırken bir hata oluştu.</div>';
                appliedCoupon = null;
                updateSummary();
            });
        });
    }
});
</script>
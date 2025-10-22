<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-compass me-2"></i>Arama Sonuçları</h3>
    <a href="/sibervatanbilet/public/" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Yeni Arama</a>
</div>

<div class="card shadow-sm mb-4 bg-light border-0">
    <div class="card-body">
        <div class="row align-items-center text-center">
            <div class="col-md-4">
                <span class="fw-bold fs-5"><?php echo htmlspecialchars($searchParams['departure']); ?></span>
            </div>
            <div class="col-md-1 text-muted">
                <i class="bi bi-arrow-right-circle fs-4"></i>
            </div>
            <div class="col-md-4">
                <span class="fw-bold fs-5"><?php echo htmlspecialchars($searchParams['destination']); ?></span>
            </div>
            <div class="col-md-3 border-start">
                <i class="bi bi-calendar-check me-2"></i>
                <span class="fw-bold fs-5"><?php echo date('d F Y', strtotime($searchParams['date'])); ?></span>
            </div>
        </div>
    </div>
</div>

<?php if (empty($trips)): ?>
    <div class="alert alert-warning text-center mt-5">
        <h4><i class="bi bi-exclamation-triangle-fill me-2"></i>Uygun Sefer Bulunamadı</h4>
        <p class="mb-0">Lütfen arama kriterlerinizi değiştirerek veya farklı bir tarih seçerek tekrar deneyin.</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($trips as $trip): ?>
            <div class="col-12">
                <div class="card trip-card shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-bus-front-fill text-danger me-3 fs-2"></i>
                                    <span class="fw-bold fs-6"><?php echo htmlspecialchars($trip['company_name']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="row align-items-center text-center">
                                    <div class="col-5">
                                        <div class="fw-bold fs-4"><?php echo date('H:i', strtotime($trip['departure_time'])); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($trip['departure_city']); ?></div>
                                    </div>
                                    <div class="col-2">
                                        <i class="bi bi-arrow-right text-muted"></i>
                                    </div>
                                    <div class="col-5">
                                        <div class="fw-bold fs-4"><?php echo date('H:i', strtotime($trip['arrival_time'])); ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($trip['destination_city']); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <div class="price-info fw-bolder fs-4 text-success"><?php echo number_format($trip['price'], 2); ?> TL</div>
                            </div>
                            <div class="col-md-2 text-center text-md-end">
                                <a href="/sibervatanbilet/public/trip/show/<?php echo $trip['id']; ?>" class="btn btn-danger w-100">Koltuk Seç</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
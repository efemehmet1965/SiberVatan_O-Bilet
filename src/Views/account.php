<h3><i class="bi bi-person-circle"></i> Hesabım</h3>
<hr class="mb-4">

<div class="row g-4">
    <!-- Sol Sütun: Profil ve Bakiye -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-person-badge"></i> Profil Bilgileri</div>
            <div class="card-body">
                <p class="card-text mb-2"><strong class="me-2">Ad Soyad:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                <p class="card-text mb-0"><strong class="me-2">E-posta:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <div class="card text-white text-center wallet-card">
            <div class="card-body p-4">
                <h5 class="card-title"><i class="bi bi-wallet2"></i> SiberVatan Cüzdan</h5>
                <p class="display-5 fw-bold"><?php echo number_format($user['balance'], 2); ?> TL</p>
                <small>Kullanılabilir Sanal Bakiyeniz</small>
            </div>
        </div>
    </div>

    <!-- Sağ Sütun: Biletlerim -->
    <div class="col-lg-8">
        <h4><i class="bi bi-ticket-detailed"></i> Biletlerim</h4>
        <?php if (empty($tickets)):
         ?>
            <div class="alert alert-info text-center">Henüz hiç bilet satın almamışsınız.</div>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
                <div class="card ticket-card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><?php echo htmlspecialchars($ticket['departure_city']); ?> <i class="bi bi-arrow-right"></i> <?php echo htmlspecialchars($ticket['destination_city']); ?></h6>
                        <?php 
                            $status_class = 'secondary';
                            $status_text = 'İptal Edildi';
                            if ($ticket['status'] === 'active') {
                                $status_class = 'success';
                                $status_text = 'Aktif';
                            } elseif ($ticket['status'] === 'expired') {
                                $status_class = 'warning';
                                $status_text = 'Geçmiş';
                            }
                        ?>
                        <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Firma:</strong> <?php echo htmlspecialchars($ticket['company_name']); ?></p>
                                <p class="mb-1"><strong>Tarih:</strong> <?php echo date('d.m.Y H:i', strtotime($ticket['departure_time'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Ödenen Tutar:</strong> <?php echo number_format($ticket['total_price'], 2); ?> TL</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end bg-light">
                        <?php
                        $departure_timestamp = strtotime($ticket['departure_time']);
                        $can_cancel = ($ticket['status'] === 'active') && (($departure_timestamp - time()) > 3600);
                        ?>
                        <?php if ($can_cancel): ?>
                            <form action="/sibervatanbilet/public/ticket/cancel/<?php echo $ticket['id']; ?>" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu bileti iptal etmek istediğinizden emin misiniz? Ücret hesabınıza iade edilecektir.');">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-x-circle"></i> İptal Et</button>
                            </form>
                        <?php endif; ?>
                        <a href="/sibervatanbilet/public/ticket/generatePdf/<?php echo $ticket['id']; ?>" class="btn btn-primary btn-sm"><i class="bi bi-file-earmark-pdf"></i> PDF Görüntüle</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
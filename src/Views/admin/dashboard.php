<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-speedometer2 me-2"></i>Yönetim Paneli</h3>
    <span class="text-muted">Hoş geldiniz, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></span>
</div>
<p class="text-muted mb-4">Bu merkezi panel üzerinden otobüs firmalarını, firma yöneticilerini ve global indirim kuponlarını yönetebilirsiniz.</p>

<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-start border-danger border-4">
            <div class="card-body text-center">
                <i class="bi bi-building-gear fs-1 text-danger mb-3"></i>
                <h5 class="card-title">Firma Yönetimi</h5>
                <p class="card-text text-muted small">Yeni otobüs firmaları ekleyin, mevcutları düzenleyin veya sistemden kaldırın.</p>
                <a href="/sibervatanbilet/public/admin/companies" class="btn btn-danger">Firmaları Yönet</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-start border-primary border-4">
            <div class="card-body text-center">
                <i class="bi bi-person-plus fs-1 text-primary mb-3"></i>
                <h5 class="card-title">Firma Adminleri</h5>
                <p class="card-text text-muted small">Firmalara özel yönetici hesapları oluşturun ve yetkili atamalarını yapın.</p>
                <a href="/sibervatanbilet/public/admin/users" class="btn btn-primary">Adminleri Yönet</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-start border-warning border-4">
            <div class="card-body text-center">
                <i class="bi bi-ticket-detailed fs-1 text-warning mb-3"></i>
                <h5 class="card-title">Global Kuponlar</h5>
                <p class="card-text text-muted small">Tüm firmalarda geçerli olacak genel indirim kuponları tanımlayın.</p>
                <a href="/sibervatanbilet/public/admin/coupons" class="btn btn-warning">Kuponları Yönet</a>
            </div>
        </div>
    </div>
</div>

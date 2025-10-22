<h3><i class="bi bi-briefcase-fill"></i> Firma Yönetim Paneli</h3>
<p class="text-muted">Hoş geldiniz, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>. Bu panel üzerinden firmanıza ait seferleri ve kuponları yönetebilirsiniz.</p>
<hr class="mb-4">

<div class="row g-4">
    <div class="col-md-6">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-signpost-split fs-1 text-primary"></i>
                <h5 class="card-title mt-3">Seferleri Yönet</h5>
                <p class="card-text">Yeni seferler oluşturun, mevcut seferlerin bilgilerini güncelleyin veya seferleri kaldırın.</p>
                <a href="/sibervatanbilet/public/company/trips" class="btn btn-primary">Seferlere Git</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-center h-100">
            <div class="card-body">
                <i class="bi bi-ticket-percent fs-1 text-success"></i>
                <h5 class="card-title mt-3">Kuponları Yönet</h5>
                <p class="card-text">Firmanıza özel indirim kuponları oluşturarak müşteri sadakatini artırın.</p>
                <a href="/sibervatanbilet/public/company/coupons" class="btn btn-success">Kuponlara Git</a>
            </div>
        </div>
    </div>
</div>

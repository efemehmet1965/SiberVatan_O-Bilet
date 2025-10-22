<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-building me-2"></i>Otobüs Firmaları</h3>
    <a href="/sibervatanbilet/public/admin/createCompany" class="btn btn-danger"><i class="bi bi-plus-circle me-2"></i>Yeni Firma Ekle</a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-light">
        <p class="mb-0 text-muted">Sistemde kayıtlı tüm otobüs firmalarının listesi.</p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Firma Adı</th>
                        <th scope="col">Oluşturulma Tarihi</th>
                        <th scope="col" class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($companies)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Henüz hiç firma eklenmemiş.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($companies as $company): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-bus-front-fill me-3 text-danger"></i>
                                        <div class="fw-bold"><?php echo htmlspecialchars($company['name']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo date('d F Y, H:i', strtotime($company['created_at'])); ?></td>
                                <td class="text-end">
                                    <a href="/sibervatanbilet/public/admin/editCompany/<?php echo $company['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i> Düzenle</a>
                                    <a href="/sibervatanbilet/public/admin/deleteCompany/<?php echo $company['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bu firmayı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')"><i class="bi bi-trash"></i> Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

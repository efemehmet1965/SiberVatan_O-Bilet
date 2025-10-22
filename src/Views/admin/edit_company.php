<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-pencil-fill"></i> Firmayı Düzenle</h4>
            </div>
            <div class="card-body">
                <?php if (isset($company)): ?>
                    <form action="/sibervatanbilet/public/admin/editCompany/<?php echo $company['id']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Firma Adı</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($company['name']); ?>" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="/sibervatanbilet/public/admin/companies" class="btn btn-secondary me-2">İptal</a>
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">Firma bulunamadı.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
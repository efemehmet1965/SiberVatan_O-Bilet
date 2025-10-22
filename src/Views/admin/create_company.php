<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Yeni Firma Oluştur</h4>
            </div>
            <div class="card-body">
                <form action="/sibervatanbilet/public/admin/createCompany" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Firma Adı</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="/sibervatanbilet/public/admin/companies" class="btn btn-secondary me-2">İptal</a>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

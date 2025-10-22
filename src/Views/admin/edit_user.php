<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-pencil-fill"></i> Firma Adminini Düzenle</h4>
            </div>
            <div class="card-body">
                <?php if (isset($user)): ?>
                <form action="/sibervatanbilet/public/admin/editUser/<?php echo $user['id']; ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-posta Adresi</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company_id" class="form-label">Atanacak Firma</label>
                            <select class="form-select" id="company_id" name="company_id" required>
                                <option value="">Firma Seçin...</option>
                                <?php foreach ($companies as $company): ?>
                                    <option value="<?php echo $company['id']; ?>" <?php echo ($user['company_id'] == $company['id']) ? 'selected' : ''; ?> >
                                        <?php echo htmlspecialchars($company['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Yeni Şifre</label>
                            <input type="password" class="form-control" id="password" name="password" aria-describedby="passwordHelp">
                            <div id="passwordHelp" class="form-text">Değiştirmek istemiyorsanız boş bırakın.</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="/sibervatanbilet/public/admin/users" class="btn btn-secondary me-2">İptal</a>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-danger">Kullanıcı bulunamadı.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
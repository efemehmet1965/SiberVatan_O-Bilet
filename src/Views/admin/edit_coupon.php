<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-pencil-fill"></i> Global Kuponu Düzenle</h4>
            </div>
            <div class="card-body">
                <?php if (isset($coupon)): ?>
                <form action="/sibervatanbilet/public/admin/editCoupon/<?php echo $coupon['id']; ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Kupon Kodu</label>
                            <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($coupon['code']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="discount" class="form-label">İndirim Oranı (%)</label>
                            <input type="number" class="form-control" id="discount" name="discount" value="<?php echo htmlspecialchars($coupon['discount']); ?>" min="1" max="100" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="usage_limit" class="form-label">Kullanım Limiti</label>
                            <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="<?php echo htmlspecialchars($coupon['usage_limit']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expire_date" class="form-label">Son Geçerlilik Tarihi</label>
                            <input type="date" class="form-control" id="expire_date" name="expire_date" value="<?php echo date('Y-m-d', strtotime($coupon['expire_date'])); ?>" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="/sibervatanbilet/public/admin/coupons" class="btn btn-secondary me-2">İptal</a>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-danger">Kupon bulunamadı.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
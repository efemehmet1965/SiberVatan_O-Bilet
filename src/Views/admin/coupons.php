<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="bi bi-ticket-percent"></i> Global Kuponlar</h3>
    <a href="/sibervatanbilet/public/admin/createCoupon" class="btn btn-success"><i class="bi bi-plus-circle"></i> Yeni Kupon Ekle</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kod</th>
                        <th>İndirim (TL)</th>
                        <th>Kullanım Limiti</th>
                        <th>Son Geçerlilik</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($coupons)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Henüz hiç global kupon eklenmemiş.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($coupons as $coupon): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($coupon['code']); ?></span></td>
                                <td><?php echo number_format($coupon['discount'], 2); ?> TL</td>
                                <td><?php echo $coupon['usage_limit']; ?></td>
                                <td><?php echo date('d.m.Y', strtotime($coupon['expire_date'])); ?></td>
                                <td class="text-end">
                                    <a href="/sibervatanbilet/public/admin/editCoupon/<?php echo $coupon['id']; ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-fill"></i> Düzenle</a>
                                    <a href="/sibervatanbilet/public/admin/deleteCoupon/<?php echo $coupon['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu kuponu silmek istediğinizden emin misiniz?')"><i class="bi bi-trash-fill"></i> Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

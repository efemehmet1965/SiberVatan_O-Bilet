<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="bi bi-people"></i> Firma Adminleri</h3>
    <a href="/sibervatanbilet/public/admin/createUser" class="btn btn-success"><i class="bi bi-plus-circle"></i> Yeni Admin Ekle</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ad Soyad</th>
                        <th>Email</th>
                        <th>Atandığı Firma</th>
                        <th>Kayıt Tarihi</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)):
                     ?>
                        <tr>
                            <td colspan="5" class="text-center">Henüz hiç firma admini eklenmemiş.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($user['company_name'] ?? '-'); ?></span></td>
                                <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                                <td class="text-end">
                                    <a href="/sibervatanbilet/public/admin/editUser/<?php echo $user['id']; ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-fill"></i> Düzenle</a>
                                    <a href="/sibervatanbilet/public/admin/deleteUser/<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')"><i class="bi bi-trash-fill"></i> Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

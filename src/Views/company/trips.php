<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="bi bi-signpost-split"></i> Seferler</h3>
    <a href="/sibervatanbilet/public/company/createTrip" class="btn btn-success"><i class="bi bi-plus-circle"></i> Yeni Sefer Ekle</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Kalkış</th>
                        <th>Varış</th>
                        <th>Tarih</th>
                        <th>Fiyat</th>
                        <th>Kapasite</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($trips)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Henüz hiç sefer oluşturulmamış.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($trips as $trip): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($trip['departure_city']); ?></td>
                                <td><?php echo htmlspecialchars($trip['destination_city']); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($trip['departure_time'])); ?></td>
                                <td><?php echo number_format($trip['price'], 2); ?> TL</td>
                                <td><?php echo $trip['capacity']; ?></td>
                                <td class="text-end">
                                    <a href="/sibervatanbilet/public/company/editTrip/<?php echo $trip['id']; ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-fill"></i> Düzenle</a>
                                    <a href="/sibervatanbilet/public/company/deleteTrip/<?php echo $trip['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu seferi silmek istediğinizden emin misiniz?')"><i class="bi bi-trash-fill"></i> Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

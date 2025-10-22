<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-pencil-fill"></i> Seferi Düzenle</h4>
            </div>
            <div class="card-body">
                <?php if (isset($trip)): ?>
                <form action="/sibervatanbilet/public/company/editTrip/<?php echo $trip['id']; ?>" method="POST">
                    <div class="col-md-6">
                        <label for="departure_city" class="form-label">Kalkış Şehri</label>
                        <select id="departure_city" name="departure_city" class="form-select" required>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city; ?>" <?php echo ($trip['departure_city'] === $city) ? 'selected' : ''; ?>><?php echo $city; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="destination_city" class="form-label">Varış Şehri</label>
                        <select id="destination_city" name="destination_city" class="form-select" required>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city; ?>" <?php echo ($trip['destination_city'] === $city) ? 'selected' : ''; ?>><?php echo $city; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departure_time" class="form-label">Kalkış Zamanı</label>
                            <input type="datetime-local" class="form-control" id="departure_time" name="departure_time" value="<?php echo date('Y-m-d\TH:i', strtotime($trip['departure_time'])); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="arrival_time" class="form-label">Varış Zamanı</label>
                            <input type="datetime-local" class="form-control" id="arrival_time" name="arrival_time" value="<?php echo date('Y-m-d\TH:i', strtotime($trip['arrival_time'])); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Fiyat (TL)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($trip['price']); ?>" required>
                        </div>
                    <div class="col-md-6">
                        <label for="capacity" class="form-label">Koltuk Sayısı</label>
                        <input type="number" class="form-control" id="capacity" name="capacity" min="10" max="55" value="<?php echo htmlspecialchars($trip['capacity']); ?>" required>
                    </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="/sibervatanbilet/public/company/trips" class="btn btn-secondary me-2">İptal</a>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-danger">Sefer bulunamadı.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
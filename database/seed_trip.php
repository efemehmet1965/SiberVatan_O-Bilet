<?php

require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Firma ID'sini al
    $companyName = 'Siber Vatan Express';
    $stmt = $pdo->prepare("SELECT id FROM Bus_Company WHERE name = ?");
    $stmt->execute([$companyName]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        die("'Siber Vatan Express' firması bulunamadı. Lütfen önce database/seed.php dosyasını çalıştırın.\n");
    }
    $companyId = $company['id'];

    // 2. Mevcut seferi kontrol et
    $stmt = $pdo->prepare("SELECT id FROM Trips WHERE departure_city = 'Ankara' AND destination_city = 'İstanbul' AND company_id = ?");
    $stmt->execute([$companyId]);
    if ($stmt->fetch()) {
        echo "Ankara - İstanbul seferi zaten mevcut.\n";
        exit;
    }

    // 3. Yeni seferi ekle
    $departureTime = date('Y-m-d 10:00:00', strtotime('+1 day')); // Yarın sabah 10:00
    $arrivalTime = date('Y-m-d 16:30:00', strtotime('+1 day'));   // Yarın 16:30
    $price = 250.0;
    $capacity = 40;

    $stmt = $pdo->prepare(
        "INSERT INTO Trips (company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$companyId, 'Ankara', 'İstanbul', $departureTime, $arrivalTime, $price, $capacity]);

    echo "Başarılı: 'Siber Vatan Express' için yarın tarihli Ankara -> İstanbul seferi eklendi.\n";

} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

?>
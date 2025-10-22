<?php

require_once __DIR__ . '/../config.php';

// UUID v4 Generator
function generateUuid() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

echo "Veritabanı başlangıç verileri yükleniyor...\n";

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    $pdo->exec('DELETE FROM User_Coupons;');
    $pdo->exec('DELETE FROM Booked_Seats;');
    $pdo->exec('DELETE FROM Tickets;');
    $pdo->exec('DELETE FROM Trips;');
    $pdo->exec('DELETE FROM Coupons;');
    $pdo->exec('DELETE FROM Users;');
    $pdo->exec('DELETE FROM Bus_Company;');
    echo "- Mevcut veriler temizlendi.\n";

    // --- FİRMALAR ---
    $company_data = [
        'Pamukkale Turizm' => generateUuid(),
        'Kamil Koç' => generateUuid(),
        'Metro Turizm' => generateUuid(),
        'Siber Vatan Express' => generateUuid()
    ];
    $stmt = $pdo->prepare("INSERT INTO Bus_Company (id, name) VALUES (?, ?)");
    foreach ($company_data as $name => $uuid) {
        $stmt->execute([$uuid, $name]);
    }
    echo "- 4 adet firma eklendi.\n";

    // --- KULLANICILAR ---
    $users = [
        [generateUuid(), 'Süper Admin', 'admin@siber.vatan', password_hash('admin123', PASSWORD_DEFAULT), 'Admin', null, 10000],
        [generateUuid(), 'Test Kullanıcı', 'user@siber.vatan', password_hash('user123', PASSWORD_DEFAULT), 'User', null, 1500],
        [generateUuid(), 'Pamukkale Admin', 'pamukkale@firma.com', password_hash('firma123', PASSWORD_DEFAULT), 'Firma Admin', $company_data['Pamukkale Turizm'], 0],
        [generateUuid(), 'Kamil Koç Admin', 'kamilkoc@firma.com', password_hash('firma123', PASSWORD_DEFAULT), 'Firma Admin', $company_data['Kamil Koç'], 0],
        [generateUuid(), 'Metro Admin', 'metro@firma.com', password_hash('firma123', PASSWORD_DEFAULT), 'Firma Admin', $company_data['Metro Turizm'], 0],
        [generateUuid(), 'Siber Vatan Admin', 'sv@firma.com', password_hash('firma123', PASSWORD_DEFAULT), 'Firma Admin', $company_data['Siber Vatan Express'], 0]
    ];
    $stmt = $pdo->prepare("INSERT INTO Users (id, full_name, email, password, role, company_id, balance) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute($u);
    }
    echo "- 6 adet kullanıcı (Admin, User, Firma Adminleri) eklendi.\n";

    // --- SEFERLER ---
    $trips = [
        [generateUuid(), $company_data['Pamukkale Turizm'], 'İstanbul', 'Ankara', date('Y-m-d 09:00:00', strtotime('+1 day')), date('Y-m-d 15:30:00', strtotime('+1 day')), 450, 40],
        [generateUuid(), $company_data['Pamukkale Turizm'], 'İzmir', 'Antalya', date('Y-m-d 22:00:00', strtotime('+0 day')), date('Y-m-d 05:00:00', strtotime('+1 day')), 500, 40],
        [generateUuid(), $company_data['Kamil Koç'], 'Ankara', 'İstanbul', date('Y-m-d 10:00:00', strtotime('+1 day')), date('Y-m-d 16:30:00', strtotime('+1 day')), 480, 35],
        [generateUuid(), $company_data['Kamil Koç'], 'Bursa', 'Ankara', date('Y-m-d 14:00:00', strtotime('+2 day')), date('Y-m-d 19:00:00', strtotime('+2 day')), 350, 40],
        [generateUuid(), $company_data['Metro Turizm'], 'Trabzon', 'İstanbul', date('Y-m-d 18:00:00', strtotime('+1 day')), date('Y-m-d 10:00:00', strtotime('+2 day')), 800, 45],
        [generateUuid(), $company_data['Siber Vatan Express'], 'Siberşehir', 'Verikent', date('Y-m-d 07:00:00', strtotime('+3 day')), date('Y-m-d 08:00:00', strtotime('+3 day')), 99, 20]
    ];
    $stmt = $pdo->prepare("INSERT INTO Trips (id, company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($trips as $t) {
        $stmt->execute($t);
    }
    echo "- 6 adet sefer eklendi.\n";

    // --- KUPONLAR ---
    $coupons = [
        [generateUuid(), 'GLOBAL25', 25, 100, date('Y-m-d', strtotime('+1 month')), null],
        [generateUuid(), 'PAMUKKALE10', 10, 50, date('Y-m-d', strtotime('+1 month')), $company_data['Pamukkale Turizm']]
    ];
    $stmt = $pdo->prepare("INSERT INTO Coupons (id, code, discount, usage_limit, expire_date, company_id) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($coupons as $c) {
        $stmt->execute($c);
    }
    echo "- 2 adet kupon (global ve firmaya özel) eklendi.\n";

    $pdo->commit();

    echo "\nVeritabanı başarıyla hazırlandı!\n";
    echo "Örnek Kullanıcılar:\n";
    echo "- Admin: admin@siber.vatan / admin123\n";
    echo "- Normal Kullanıcı: user@siber.vatan / user123\n";
    echo "- Firma Admin: pamukkale@firma.com / firma123\n";

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Veritabanı hatası: " . $e->getMessage());
}

?>

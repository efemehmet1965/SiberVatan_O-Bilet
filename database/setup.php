<?php
require_once __DIR__ . '/../config.php';

echo "Veritabanı kurulumu başlıyor...\n";

try {
    // Eğer veritabanı dosyası varsa, tekrar oluşturmamak için sil
    if (file_exists(DB_PATH)) {
        unlink(DB_PATH);
        echo "Mevcut veritabanı dosyası silindi.\n";
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Veritabanı bağlantısı başarılı.\n";

    // Tabloları oluşturma sorguları
    $commands = [
        'CREATE TABLE Users (
            id TEXT PRIMARY KEY,
            full_name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT \'User\', -- User, Firma Admin, Admin
            balance REAL NOT NULL DEFAULT 800.0, -- Sanal kredi
            company_id TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE SET NULL
        )',
        'CREATE TABLE Bus_Company (
            id TEXT PRIMARY KEY,
            name TEXT UNIQUE NOT NULL,
            logo_path TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )',
        'CREATE TABLE Trips (
            id TEXT PRIMARY KEY,
            company_id TEXT NOT NULL,
            departure_city TEXT NOT NULL,
            destination_city TEXT NOT NULL,
            departure_time DATETIME NOT NULL,
            arrival_time DATETIME NOT NULL,
            price REAL NOT NULL,
            capacity INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE CASCADE
        )',
        'CREATE TABLE Tickets (
            id TEXT PRIMARY KEY,
            user_id TEXT NOT NULL,
            trip_id TEXT NOT NULL,
            total_price REAL NOT NULL,
            status TEXT NOT NULL DEFAULT \'active\', -- active, canceled, expired
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
            FOREIGN KEY (trip_id) REFERENCES Trips(id) ON DELETE CASCADE
        )',
        'CREATE TABLE Booked_Seats (
            id TEXT PRIMARY KEY,
            ticket_id TEXT NOT NULL,
            seat_number INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (ticket_id) REFERENCES Tickets(id) ON DELETE CASCADE,
            UNIQUE(ticket_id, seat_number)
        )',
        'CREATE TABLE Coupons (
            id TEXT PRIMARY KEY,
            code TEXT UNIQUE NOT NULL,
            discount REAL NOT NULL, -- Oransal (örn: 0.10 for 10%) veya sabit
            usage_limit INTEGER NOT NULL DEFAULT 1,
            expire_date DATETIME NOT NULL,
            company_id TEXT, -- NULL ise global kupon
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (company_id) REFERENCES Bus_Company(id) ON DELETE SET NULL
        )',
        'CREATE TABLE User_Coupons (
            id TEXT PRIMARY KEY,
            user_id TEXT NOT NULL,
            coupon_id TEXT NOT NULL,
            used_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE,
            FOREIGN KEY (coupon_id) REFERENCES Coupons(id) ON DELETE CASCADE
        )'
    ];

    foreach ($commands as $command) {
        $pdo->exec($command);
    }

    echo "Tüm tablolar başarıyla oluşturuldu.\n";

} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

?>

<?php

require_once __DIR__ . '/../config.php';

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $adminEmail = 'admin@siber.vatan';
    $stmt = $pdo->prepare("SELECT id FROM Users WHERE email = ?");
    $stmt->execute([$adminEmail]);

    if ($stmt->fetch()) {
        echo "Süper admin zaten mevcut.\n";
    } else {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Süper Admin', $adminEmail, $password, 'Admin']);
        echo "Süper admin (admin@siber.vatan / admin123) oluşturuldu.\n";
    }

} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

?>
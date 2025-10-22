<?php

// Oturumu başlat
session_start();

// Yapılandırma dosyasını dahil et
require_once __DIR__ . '/../config.php';

// Controller'ların temelini dahil et (diğerleri router'da çağrılacak)
require_once __DIR__ . '/../src/Controllers/BaseController.php';

// Yönlendiriciyi dahil et ve çalıştır
require_once __DIR__ . '/../router.php';

?>
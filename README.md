# Bilet Satın Alma Platformu

Bu proje, modern web teknolojileri kullanılarak geliştirilmiş, dinamik, veritabanı destekli ve çok kullanıcılı bir otobüs bileti satış platformudur. PHP ve SQLite teknolojileriyle inşa edilmiştir.

## Proje Genel Bakışı

Platform, otobüs bileti satış süreçlerini yönetmek üzere tasarlanmıştır ve farklı kullanıcı rolleri için özelleştirilmiş deneyimler sunar: Ziyaretçi, Kullanıcı (Yolcu), Firma Yöneticisi ve Sistem Yöneticisi. Kullanıcı dostu arayüzü ve sağlam arka uç yapısıyla, bilet arama, satın alma, iptal etme ve yönetim süreçlerini kolaylaştırmayı hedefler.

## Temel Özellikler

*   **Çoklu Kullanıcı Rolleri:**
    *   **Ziyaretçi:** Seferleri arayabilir ve detaylarını görüntüleyebilir.
    *   **Kullanıcı (Yolcu):** Kayıt olabilir, giriş yapabilir, bilet satın alabilir (sanal kredi ile), biletlerini yönetebilir, iptal edebilir ve PDF formatında indirebilir.
    *   **Firma Yöneticisi:** Kendi firmasına ait seferleri ve indirim kuponlarını yönetebilir (CRUD işlemleri).
    *   **Sistem Yöneticisi:** Otobüs firmalarını, firma yöneticisi hesaplarını ve genel indirim kuponlarını yönetebilir.
*   **Bilet Yönetimi:** Sefer arama, koltuk seçimi, kupon uygulama ve bilet iptali (kalkışa 1 saat kala kuralı ile).
*   **PDF Bilet Oluşturma:** Satın alınan biletlerin PDF formatında indirilmesi.
*   **Veritabanı Desteği:** SQLite veritabanı ile güvenilir veri depolama.

## Kullanılan Teknolojiler

*   **Backend:** PHP
*   **Veritabanı:** SQLite
*   **Frontend:** HTML, CSS (Esnek arayüz tasarımı)
*   **PDF Kütüphanesi:** TCPDF (PDF bilet oluşturma için)
*   **Bağımlılık Yönetimi:** Composer

## Kurulum ve Çalıştırma

Bu projeyi yerel ortamınızda çalıştırmak için Docker kullanılması şiddetle tavsiye edilir.

### Ön Gereksinimler

*   [Docker Desktop](https://www.docker.com/products/docker-desktop) yüklü ve çalışır durumda olmalıdır.

### Adımlar

1.  **Depoyu Klonlayın:**
    ```bash
    git clone [PROJE_DEPO_ADRESİ]
    cd SiberVatan_O-Bilet
    ```
    *(Lütfen `[PROJE_DEPO_ADRESİ]` kısmını projenizin gerçek GitHub depo URL'si ile değiştirin.)*

2.  **Docker Ortamını Başlatın:**
    Proje dizininin kökünden aşağıdaki komutu çalıştırın. Bu komut, gerekli Docker imajını oluşturacak, tüm bağımlılıkları kuracak, veritabanını başlatacak ve uygulamayı çalışır duruma getirecektir.
    ```bash
    docker compose up --build
    ```

3.  **Uygulamaya Erişin:**
    Docker container'ları başarıyla başlatıldıktan sonra, web tarayıcınızda aşağıdaki adrese giderek uygulamaya erişebilirsiniz:
    ```
    http://localhost
    ```

## Kullanım Kılavuzu

Uygulama arayüzü, farklı kullanıcı rolleri için sezgisel bir deneyim sunar.

*   **Ziyaretçiler:** Ana sayfadan sefer arayabilir ve detayları inceleyebilirler.
*   **Kullanıcılar:** Kayıt olabilir veya mevcut hesaplarıyla giriş yaparak bilet satın alma, bilet iptali ve hesap yönetimi gibi işlemleri gerçekleştirebilirler.
*   **Yöneticiler:** İlgili yönetici panellerine giriş yaparak sistemin farklı yönlerini (firma, kullanıcı, kupon, sefer yönetimi) kontrol edebilirler.

## Veritabanı Yapısı

Proje, `database/bilet_platformu.sqlite` yolunda bulunan bir SQLite veritabanı kullanmaktadır. Bu veritabanı, uygulamanın tüm dinamik verilerini (kullanıcılar, firmalar, seferler, biletler, kuponlar vb.) saklar.

**Docker Entegrasyonu:**
Docker Compose kurulumu, veritabanı dosyasının (`bilet_platformu.sqlite`) container'ın `database/` dizinine kalıcı bir Docker volume'ü (`db_data`) aracılığıyla bağlanmasını sağlar. Bu sayede, container'lar yeniden oluşturulsa veya silinse bile veritabanı verileriniz korunur.

**Başlangıç Verileri:**
Uygulama ilk kez başlatıldığında (veya `docker compose up --build` komutuyla yeniden oluşturulduğunda), `docker-entrypoint.sh` betiği aracılığıyla veritabanı şeması oluşturulur ve başlangıç verileri (örneğin, varsayılan yönetici hesabı, örnek seferler) otomatik olarak yüklenir.

## Katkıda Bulunma

Projenin geliştirilmesine katkıda bulunmak isteyen herkesi memnuniyetle karşılarız. Lütfen katkıda bulunmadan önce aşağıdaki yönergeleri dikkate alın:

1.  Bir özelliği uygulamadan veya bir hatayı düzeltmeden önce ilgili bir "issue" açın.
2.  Kodlama standartlarına uyun.
3.  Değişikliklerinizi ayrı bir branch'te yapın.
4.  Pull Request'lerinizi açık ve anlaşılır bir şekilde açıklayın.

## Lisans

Bu proje MIT Lisansı altında lisanslanmıştır. Daha fazla bilgi için lütfen `LICENSE` dosyasına bakın.

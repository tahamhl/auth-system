# PHP PDO ile Güvenli Login ve Register Sistemi

Bu proje, PHP PDO kullanarak güvenli bir kullanıcı kimlik doğrulama sistemi nasıl oluşturulacağını göstermektedir. Kullanıcı kayıt, giriş, oturum yönetimi ve parola sıfırlama işlemlerini güvenli bir şekilde gerçekleştiren örnek bir uygulamadır.

## Özellikler

- **Kullanıcı Kaydı (Register)**: Güvenli kullanıcı hesabı oluşturma
- **Kullanıcı Girişi (Login)**: Güvenli kullanıcı kimlik doğrulama
- **Oturum Yönetimi**: Güvenli oturum işlemleri
- **Parola Sıfırlama**: "Parolamı unuttum" işlevselliği
- **CSRF Koruması**: Cross-Site Request Forgery saldırılarına karşı koruma
- **XSS Koruması**: Cross-Site Scripting saldırılarına karşı koruma
- **SQL Enjeksiyon Koruması**: PDO prepared statements ile güvenlik
- **Güvenli Parola Saklama**: PHP'nin password_hash() fonksiyonu ile parola hashleme

## Kurulum

1. Proje dosyalarını web sunucunuza kopyalayın
2. Veritabanı oluşturun ve `auth_system.sql` dosyasını içe aktarın
3. `config/Database.php` dosyasında veritabanı bağlantı ayarlarını yapın
4. `config/config.php` dosyasında proje yolu ve diğer ayarları düzenleyin

```sql
-- Veritabanını oluştur
CREATE DATABASE IF NOT EXISTS auth_system;
USE auth_system;

-- Kullanıcılar tablosunu oluştur
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Token tablosunu oluştur
CREATE TABLE tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    selector VARCHAR(16) NOT NULL,
    token VARCHAR(255) NOT NULL,
    type VARCHAR(20) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Proje Yapısı

```
project/
├── config/             # Yapılandırma dosyaları
│   ├── Database.php    # Veritabanı bağlantı sınıfı
│   └── config.php      # Genel yapılandırma ayarları
├── includes/           # Sınıflar ve yardımcı dosyalar
│   ├── Session.php     # Oturum yönetimi sınıfı
│   ├── User.php        # Kullanıcı işlemleri sınıfı
│   └── Token.php       # Token yönetimi sınıfı
├── public/             # Genel erişime açık dosyalar
│   ├── css/            # CSS dosyaları
│   ├── js/             # JavaScript dosyaları
│   ├── index.php       # Ana sayfa
│   ├── login.php       # Giriş sayfası
│   ├── register.php    # Kayıt sayfası
│   ├── logout.php      # Çıkış işlemi
│   └── forgot-password.php # Parola sıfırlama sayfası
├── templates/          # Şablonlar
│   ├── header.php      # Üst bölüm şablonu
│   └── footer.php      # Alt bölüm şablonu
├── .htaccess           # Apache yapılandırması
└── README.md           # Proje açıklaması
```

## Kullanım

1. Kullanıcı Kaydı: `/register.php` sayfasını kullanarak yeni bir kullanıcı hesabı oluşturun.
2. Kullanıcı Girişi: `/login.php` sayfasını kullanarak hesabınıza giriş yapın.
3. Parola Sıfırlama: `/forgot-password.php` sayfasını kullanarak parolanızı sıfırlayın.

## Güvenlik Özellikleri

- **PDO Prepared Statements**: SQL enjeksiyon saldırılarına karşı koruma
- **CSRF Token Doğrulaması**: Her form gönderiminde güvenlik token kontrolü
- **Güvenli Oturum Yönetimi**: Session fixation ve session hijacking koruması
- **Güçlü Parola Politikası**: Minimum uzunluk ve karmaşıklık gereksinimleri
- **Brute Force Koruması**: Başarısız giriş denemelerini sınırlama
- **XSS Koruması**: Kullanıcı girdilerinin temizlenmesi

## Gereksinimler

- PHP 7.0 veya üzeri
- MySQL veritabanı
- PDO PHP eklentisi
- mod_rewrite (Apache için)

## İletişim

Daha fazla bilgi ve sorularınız için: [github.com/tahamhl](https://github.com/tahamhl) 

## Daha Detaylı Bilgi

Bu proje hakkında daha detaylı bilgi, kod açıklamaları ve örnekler için akademi sitemizi ziyaret edebilirsiniz:

[🔍 Detaylı Anlatım ve Örnekler](https://akademi.tahamehel.tr/details.php?id=21)

Akademi sitemizde PHP, MySQL, güvenlik ve web geliştirme konularında daha fazla kaynak bulabilirsiniz. 
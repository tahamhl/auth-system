<?php
// Konfigürasyon ve sınıfları dahil et
require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../includes/User.php';
require_once '../includes/Session.php';

// Oturum başlat
$session = new Session();

// Veritabanı bağlantısı oluştur
$database = new Database();
$db = $database->connect();

// Sayfa başlığı
$pageTitle = "Ana Sayfa";

// Header'ı dahil et
include_once '../templates/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron text-center">
                <h1 class="display-4">PHP PDO ile Güvenli Kimlik Doğrulama</h1>
                <p class="lead">Bu proje, PHP PDO kullanarak güvenli bir login ve kayıt sistemi oluşturmayı göstermektedir.</p>
                <hr class="my-4">
                <p>Projenin tüm kodlarına GitHub üzerinden erişebilirsiniz.</p>
                <p class="lead">
                    <a class="btn btn-primary btn-lg" href="https://github.com/tahamhl" target="_blank" role="button">GitHub'da İncele</a>
                </p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user-plus"></i> Kullanıcı Kaydı</h5>
                </div>
                <div class="card-body">
                    <p>Güvenli kayıt sistemi, kullanıcı adı ve e-posta benzersizlik kontrolü, güçlü parola politikası ve e-posta doğrulaması içerir.</p>
                    <a href="register.php" class="btn btn-outline-primary">Kayıt Ol</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-sign-in-alt"></i> Kullanıcı Girişi</h5>
                </div>
                <div class="card-body">
                    <p>Güvenli giriş sistemi, brute force koruması, "beni hatırla" seçeneği ve oturum çalma koruması içerir.</p>
                    <a href="login.php" class="btn btn-outline-primary">Giriş Yap</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-key"></i> Parola Sıfırlama</h5>
                </div>
                <div class="card-body">
                    <p>Güvenli parola sıfırlama, benzersiz token oluşturma, e-posta doğrulaması ve token süre sınırlaması içerir.</p>
                    <a href="forgot-password.php" class="btn btn-outline-primary">Parolamı Unuttum</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-shield-alt"></i> Güvenlik Özellikleri</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>PDO Prepared Statements:</strong> SQL enjeksiyon saldırılarına karşı koruma
                        </li>
                        <li class="list-group-item">
                            <strong>Parola Hashleme:</strong> Güvenli parola saklama için PHP'nin password_hash() fonksiyonu
                        </li>
                        <li class="list-group-item">
                            <strong>CSRF Koruması:</strong> Cross-Site Request Forgery saldırılarına karşı token doğrulaması
                        </li>
                        <li class="list-group-item">
                            <strong>XSS Koruması:</strong> Kullanıcı girdilerinin temizlenmesi ve güvenli çıktı kodlaması
                        </li>
                        <li class="list-group-item">
                            <strong>Güvenli Oturum Yönetimi:</strong> Oturum hırsızlığı ve sabitlemeye karşı koruma
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include_once '../templates/footer.php';
?> 
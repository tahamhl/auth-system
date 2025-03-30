<?php
// Konfigürasyon ve sınıfları dahil et
require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../includes/User.php';
require_once '../includes/Session.php';
require_once '../includes/Token.php';

// Oturum başlat
$session = new Session();

// Kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
if ($session->isLoggedIn()) {
    redirect(BASE_URL);
}

// Veritabanı bağlantısı oluştur
$database = new Database();
$db = $database->connect();

// User ve Token sınıflarını başlat
$user = new User($db);
$tokenHandler = new Token($db);

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token kontrolü
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.');
        redirect(BASE_URL . '/forgot-password.php');
    }
    
    // Form verilerini al
    $email = trim($_POST['email'] ?? '');
    
    // Alan kontrolü
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('error', 'Lütfen geçerli bir e-posta adresi girin.');
    } else {
        // Parola sıfırlama token'ı oluştur
        $token = $user->generatePasswordResetToken($email);
        
        if ($token) {
            // Token ve e-posta işlemleri
            // Not: Gerçek uygulamada e-posta gönderme kodu eklenmelidir
            // $resetLink = BASE_URL . '/reset-password.php?token=' . $token;
            // mail($email, 'Parola Sıfırlama', 'Parolanızı sıfırlamak için: ' . $resetLink);
            
            setFlashMessage('success', 'Parola sıfırlama talimatları e-posta adresinize gönderildi.');
            redirect(BASE_URL . '/login.php');
        } else {
            // Kullanıcıya bilgi sızdırmamak için aynı mesajı göster
            setFlashMessage('success', 'Parola sıfırlama talimatları e-posta adresinize gönderildi.');
            redirect(BASE_URL . '/login.php');
        }
    }
}

// Sayfa başlığı
$pageTitle = "Parolamı Unuttum";

// Header'ı dahil et
include_once '../templates/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-key"></i> Parolamı Unuttum</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">E-posta adresinizi girin, size parola sıfırlama talimatlarını göndereceğiz.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="form-group">
                            <label for="email">E-posta Adresi</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control" id="email" name="email" required autofocus>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Parola Sıfırlama Bağlantısı Gönder</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <div class="small"><a href="<?php echo BASE_URL; ?>/login.php">Giriş sayfasına dön</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include_once '../templates/footer.php';
?> 
<?php
// Konfigürasyon ve sınıfları dahil et
require_once '../config/config.php';
require_once '../config/Database.php';
require_once '../includes/User.php';
require_once '../includes/Session.php';

// Oturum başlat
$session = new Session();

// Kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
if ($session->isLoggedIn()) {
    redirect(BASE_URL);
}

// Veritabanı bağlantısı oluştur
$database = new Database();
$db = $database->connect();

// User sınıfını başlat
$user = new User($db);

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Token kontrolü
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Güvenlik doğrulaması başarısız. Lütfen tekrar deneyin.');
        redirect(BASE_URL . '/login.php');
    }
    
    // Form verilerini al
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Alan kontrolü
    if (empty($login) || empty($password)) {
        setFlashMessage('error', 'Lütfen tüm alanları doldurun.');
    } else {
        // Kullanıcı girişini dene
        if ($user->login($login, $password)) {
            // Kullanıcı oturumunu oluştur
            $session->createUserSession($user->id, $user->username);
            
            // "Beni hatırla" işlemi
            if ($remember) {
                $session->rememberMe($user->id);
            }
            
            setFlashMessage('success', 'Başarıyla giriş yaptınız.');
            redirect(BASE_URL . '/index.php');
        } else {
            setFlashMessage('error', 'Geçersiz kullanıcı adı/e-posta veya parola.');
        }
    }
}

// Sayfa başlığı
$pageTitle = "Giriş Yap";

// Header'ı dahil et
include_once '../templates/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-sign-in-alt"></i> Giriş Yap</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="form-group">
                            <label for="login">Kullanıcı Adı veya E-posta</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" id="login" name="login" required autofocus>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Parola</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Beni Hatırla</label>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <div class="small mb-2"><a href="<?php echo BASE_URL; ?>/forgot-password.php">Parolanızı mı unuttunuz?</a></div>
                    <div class="small">Hesabınız yok mu? <a href="<?php echo BASE_URL; ?>/register.php">Kayıt olun</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include_once '../templates/footer.php';
?> 
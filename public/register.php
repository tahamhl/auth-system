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
        redirect(BASE_URL . '/register.php');
    }
    
    // Form verilerini al
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Alan kontrolü
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        setFlashMessage('error', 'Lütfen tüm alanları doldurun.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlashMessage('error', 'Lütfen geçerli bir e-posta adresi girin.');
    } elseif (strlen($password) < 8) {
        setFlashMessage('error', 'Parola en az 8 karakter uzunluğunda olmalıdır.');
    } elseif ($password !== $confirmPassword) {
        setFlashMessage('error', 'Parolalar eşleşmiyor.');
    } else {
        // Kullanıcı adı ve e-posta kontrolü
        $user->username = $username;
        $user->email = $email;
        
        if ($user->usernameExists()) {
            setFlashMessage('error', 'Bu kullanıcı adı zaten kullanılıyor.');
        } elseif ($user->emailExists()) {
            setFlashMessage('error', 'Bu e-posta adresi zaten kullanılıyor.');
        } else {
            // Kullanıcı kayıt işlemi
            $user->password = $password;
            
            if ($user->register()) {
                // E-posta doğrulama için token oluştur
                // Not: Gerçek uygulamada e-posta doğrulama adımı eklenmelidir
                
                setFlashMessage('success', 'Kayıt işlemi başarılı! Şimdi giriş yapabilirsiniz.');
                redirect(BASE_URL . '/login.php');
            } else {
                setFlashMessage('error', 'Kayıt işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.');
            }
        }
    }
}

// Sayfa başlığı
$pageTitle = "Kayıt Ol";

// Header'ı dahil et
include_once '../templates/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user-plus"></i> Kayıt Ol</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="form-group">
                            <label for="username">Kullanıcı Adı</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control" id="username" name="username" required autofocus>
                            </div>
                            <small class="form-text text-muted">Kullanıcı adı benzersiz olmalıdır.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E-posta Adresi</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control" id="email" name="email" required>
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
                            <small class="form-text text-muted">Parola en az 8 karakter uzunluğunda olmalıdır.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Parola Tekrar</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Kayıt Ol</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <div class="small">Zaten bir hesabınız var mı? <a href="<?php echo BASE_URL; ?>/login.php">Giriş yapın</a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Footer'ı dahil et
include_once '../templates/footer.php';
?> 
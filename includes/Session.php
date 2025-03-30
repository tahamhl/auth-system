<?php
/**
 * Session sınıfı
 * Oturum yönetimi ve güvenliğini sağlar
 */
class Session {
    private $userId;
    private $username;
    private $lastActivity;
    private $ipAddress;
    private $userAgent;
    
    /**
     * Constructor
     * Oturumu başlatır veya devam ettirir
     */
    public function __construct() {
        // Oturumu başlat (eğer başlatılmamışsa)
        if (session_status() === PHP_SESSION_NONE) {
            // Güvenli oturum ayarları
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            
            session_start();
        }
        
        // Mevcut oturum bilgilerini al
        $this->userId = $_SESSION['user_id'] ?? null;
        $this->username = $_SESSION['username'] ?? null;
        $this->lastActivity = $_SESSION['last_activity'] ?? null;
        $this->ipAddress = $_SESSION['ip_address'] ?? null;
        $this->userAgent = $_SESSION['user_agent'] ?? null;
        
        // Oturum güvenliği ve zaman aşımı kontrollerini yap
        $this->validateSession();
    }
    
    /**
     * Kullanıcı oturumunu oluşturur
     * @param int $userId Kullanıcı ID
     * @param string $username Kullanıcı adı
     */
    public function createUserSession($userId, $username) {
        // Oturum kimliğini yenile (session fixation koruması)
        session_regenerate_id(true);
        
        // Oturum değişkenlerini ayarla
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['last_activity'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        // Sınıf özelliklerini güncelle
        $this->userId = $userId;
        $this->username = $username;
        $this->lastActivity = time();
        $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
    }
    
    /**
     * Oturumu doğrular
     * - Zaman aşımı kontrolü
     * - IP adresi ve user agent kontrolü
     */
    private function validateSession() {
        // Kullanıcı giriş yapmışsa
        if ($this->isLoggedIn()) {
            // Zaman aşımı kontrolü
            if ($this->lastActivity && (time() - $this->lastActivity > SESSION_TIMEOUT)) {
                $this->destroy();
                setFlashMessage('warning', 'Oturumunuz zaman aşımına uğradı. Lütfen tekrar giriş yapın.');
                redirect(BASE_URL . '/login.php');
            }
            
            // IP adresi veya tarayıcı değişmişse
            if (
                $this->ipAddress !== $_SERVER['REMOTE_ADDR'] || 
                $this->userAgent !== $_SERVER['HTTP_USER_AGENT']
            ) {
                $this->destroy();
                setFlashMessage('error', 'Güvenlik ihlali tespit edildi. Lütfen tekrar giriş yapın.');
                redirect(BASE_URL . '/login.php');
            }
            
            // Son aktivite zamanını güncelle
            $_SESSION['last_activity'] = time();
            $this->lastActivity = time();
        }
    }
    
    /**
     * Kullanıcının giriş yapmış olup olmadığını kontrol eder
     * @return boolean Giriş yapmışsa true, değilse false
     */
    public function isLoggedIn() {
        return !empty($this->userId);
    }
    
    /**
     * Oturumu sonlandırır
     */
    public function destroy() {
        // Oturum değişkenlerini temizle
        $_SESSION = array();
        
        // Oturum çerezini sil
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }
        
        // Oturumu yok et
        session_destroy();
        
        // Sınıf özelliklerini sıfırla
        $this->userId = null;
        $this->username = null;
        $this->lastActivity = null;
        $this->ipAddress = null;
        $this->userAgent = null;
    }
    
    /**
     * Kullanıcı ID'sini döndürür
     * @return int|null Kullanıcı ID'si
     */
    public function getUserId() {
        return $this->userId;
    }
    
    /**
     * Kullanıcı adını döndürür
     * @return string|null Kullanıcı adı
     */
    public function getUsername() {
        return $this->username;
    }
    
    /**
     * Kullanıcı için "beni hatırla" fonksiyonunu uygular
     * @param int $userId Kullanıcı ID'si
     */
    public function rememberMe($userId) {
        // Benzersiz token oluştur
        $selector = bin2hex(random_bytes(8));
        $validator = bin2hex(random_bytes(32));
        
        // Tanımlayıcı ve doğrulayıcı çiftini çerezde sakla
        $cookie = $selector . ':' . $validator;
        $cookieExpiry = time() + (30 * 24 * 60 * 60); // 30 gün
        
        setcookie(
            'remember_me',
            $cookie,
            $cookieExpiry,
            '/',
            '',
            isset($_SERVER['HTTPS']),
            true // httpOnly
        );
        
        // Doğrulayıcıyı hash'leyerek veritabanında sakla
        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);
        $expiry = date('Y-m-d H:i:s', $cookieExpiry);
        
        // Veritabanı işlemleri buraya eklenebilir
        // Örnek: saveRememberMeToken($userId, $selector, $hashedValidator, $expiry);
    }
    
    /**
     * "Beni hatırla" tokenını kontrol eder ve doğrularsa oturum açar
     * @param object $userObj User sınıfı nesnesi
     * @return boolean Başarılı ise true, değilse false
     */
    public function loginFromCookie($userObj) {
        if (isset($_COOKIE['remember_me'])) {
            list($selector, $validator) = explode(':', $_COOKIE['remember_me']);
            
            // Veritabanından bu seçici ile eşleşen token'ı al
            // Örnek: $token = getRememberMeToken($selector);
            
            // Eğer token bulunursa ve süresi dolmamışsa
            // if ($token && strtotime($token['expires_at']) > time()) {
            //     // Doğrulayıcıyı kontrol et
            //     if (password_verify($validator, $token['token'])) {
            //         // Kullanıcıyı al ve oturum aç
            //         $userObj->getById($token['user_id']);
            //         $this->createUserSession($token['user_id'], $userObj->username);
            //         return true;
            //     }
            // }
        }
        
        return false;
    }
    
    /**
     * Yönetici yetkilerini kontrol eder
     * @return boolean Yönetici ise true, değilse false
     */
    public function isAdmin() {
        // Yönetici kontrolü (örnek)
        return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
    }
    
    /**
     * CSRF token kontrolü için, kullanıcı giriş yapmışsa oturum kimliğini korur
     * @return void
     */
    public function maintainSessionId() {
        if ($this->isLoggedIn()) {
            // Oturum kimliğini değiştirmeden önce koruyalım
            $oldSessionId = session_id();
            
            // Form gönderimlerinde CSRF tokeni ile birlikte bu ID'yi de gönder
            $_SESSION['old_session_id'] = $oldSessionId;
        }
    }
} 
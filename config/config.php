<?php
/**
 * Genel yapılandırma ayarları
 */

// Oturum zaman aşımı süresi (saniye)
define('SESSION_TIMEOUT', 1800); // 30 dakika

// Web sitesi kök URL'si
define('BASE_URL', 'http://localhost/blog-auth-system');

// Klasör yolları
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Güvenlik ayarları
define('HASH_COST', 12); // BCrypt zorluk seviyesi

// E-posta ayarları
define('EMAIL_FROM', 'noreply@example.com');
define('EMAIL_FROM_NAME', 'PHP Auth System');

// URL yönlendirme fonksiyonu
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// CSRF token oluşturma ve doğrulama
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Güvenli çıktı fonksiyonu
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Hata ve başarı mesajları
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'error', 'warning', 'info'
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
} 
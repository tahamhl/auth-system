<?php
// Konfigürasyon ve sınıfları dahil et
require_once '../config/config.php';
require_once '../includes/Session.php';

// Oturum başlat
$session = new Session();

// Kullanıcının çıkış yapması için oturumu sonlandır
$session->destroy();

// Çıkış mesajı ve yönlendirme
setFlashMessage('success', 'Başarıyla çıkış yaptınız.');
redirect(BASE_URL);
?> 
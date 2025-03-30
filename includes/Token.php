<?php
/**
 * Token sınıfı
 * Güvenlik tokenleri oluşturma ve doğrulama işlemlerini yönetir
 */
class Token {
    // Veritabanı bağlantısı
    private $conn;
    
    /**
     * Constructor
     * @param PDO $db Veritabanı bağlantısı
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Parola sıfırlama tokeni oluşturur
     * @param int $userId Kullanıcı ID
     * @return array Token bilgileri ['token', 'selector', 'expires_at']
     */
    public function createPasswordResetToken($userId) {
        // Önceki tokenları temizle
        $this->deleteUserTokens($userId, 'password_reset');
        
        // Benzersiz token oluştur
        $selector = bin2hex(random_bytes(8));
        $validator = bin2hex(random_bytes(32));
        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);
        
        // Son kullanma tarihi (1 saat)
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);
        
        // Token'ı veritabanına kaydet
        $query = "INSERT INTO tokens 
                  SET user_id = :user_id, 
                      selector = :selector, 
                      token = :token, 
                      type = 'password_reset',
                      expires_at = :expires_at";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':selector', $selector);
        $stmt->bindParam(':token', $hashedValidator);
        $stmt->bindParam(':expires_at', $expiresAt);
        
        if ($stmt->execute()) {
            return [
                'token' => $selector . ':' . $validator,
                'selector' => $selector,
                'expires_at' => $expiresAt
            ];
        }
        
        return false;
    }
    
    /**
     * Parola sıfırlama tokenini doğrular
     * @param string $tokenString Format: selector:validator
     * @return int|boolean Başarılıysa kullanıcı ID, değilse false
     */
    public function verifyPasswordResetToken($tokenString) {
        // Token bileşenlerini ayır
        list($selector, $validator) = explode(':', $tokenString);
        
        // Veritabanından token bilgilerini al
        $query = "SELECT t.user_id, t.token, t.expires_at 
                  FROM tokens t 
                  WHERE t.selector = :selector 
                  AND t.type = 'password_reset' 
                  AND t.expires_at > NOW() 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':selector', $selector);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedValidator = $row['token'];
            
            // Validator'ı doğrula
            if (password_verify($validator, $hashedValidator)) {
                return $row['user_id'];
            }
        }
        
        return false;
    }
    
    /**
     * Email doğrulama tokeni oluşturur
     * @param int $userId Kullanıcı ID
     * @return array Token bilgileri ['token', 'selector', 'expires_at']
     */
    public function createEmailVerificationToken($userId) {
        // Önceki tokenları temizle
        $this->deleteUserTokens($userId, 'email_verification');
        
        // Benzersiz token oluştur
        $selector = bin2hex(random_bytes(8));
        $validator = bin2hex(random_bytes(32));
        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);
        
        // Son kullanma tarihi (24 saat)
        $expiresAt = date('Y-m-d H:i:s', time() + 86400);
        
        // Token'ı veritabanına kaydet
        $query = "INSERT INTO tokens 
                  SET user_id = :user_id, 
                      selector = :selector, 
                      token = :token, 
                      type = 'email_verification',
                      expires_at = :expires_at";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':selector', $selector);
        $stmt->bindParam(':token', $hashedValidator);
        $stmt->bindParam(':expires_at', $expiresAt);
        
        if ($stmt->execute()) {
            return [
                'token' => $selector . ':' . $validator,
                'selector' => $selector,
                'expires_at' => $expiresAt
            ];
        }
        
        return false;
    }
    
    /**
     * Email doğrulama tokenini doğrular
     * @param string $tokenString Format: selector:validator
     * @return int|boolean Başarılıysa kullanıcı ID, değilse false
     */
    public function verifyEmailVerificationToken($tokenString) {
        // Token bileşenlerini ayır
        list($selector, $validator) = explode(':', $tokenString);
        
        // Veritabanından token bilgilerini al
        $query = "SELECT t.user_id, t.token, t.expires_at 
                  FROM tokens t 
                  WHERE t.selector = :selector 
                  AND t.type = 'email_verification' 
                  AND t.expires_at > NOW() 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':selector', $selector);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedValidator = $row['token'];
            
            // Validator'ı doğrula
            if (password_verify($validator, $hashedValidator)) {
                return $row['user_id'];
            }
        }
        
        return false;
    }
    
    /**
     * "Beni hatırla" tokeni oluşturur
     * @param int $userId Kullanıcı ID
     * @return array Token bilgileri ['token', 'selector', 'expires_at']
     */
    public function createRememberMeToken($userId) {
        // Benzersiz token oluştur
        $selector = bin2hex(random_bytes(8));
        $validator = bin2hex(random_bytes(32));
        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);
        
        // Son kullanma tarihi (30 gün)
        $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));
        
        // Token'ı veritabanına kaydet
        $query = "INSERT INTO tokens 
                  SET user_id = :user_id, 
                      selector = :selector, 
                      token = :token, 
                      type = 'remember_me',
                      expires_at = :expires_at";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':selector', $selector);
        $stmt->bindParam(':token', $hashedValidator);
        $stmt->bindParam(':expires_at', $expiresAt);
        
        if ($stmt->execute()) {
            return [
                'token' => $selector . ':' . $validator,
                'selector' => $selector,
                'expires_at' => $expiresAt
            ];
        }
        
        return false;
    }
    
    /**
     * "Beni hatırla" tokenini doğrular
     * @param string $selector Token seçicisi
     * @return array|boolean Başarılıysa ['user_id', 'token'], değilse false
     */
    public function findRememberMeToken($selector) {
        // Veritabanından token bilgilerini al
        $query = "SELECT t.user_id, t.token, t.expires_at 
                  FROM tokens t 
                  WHERE t.selector = :selector 
                  AND t.type = 'remember_me' 
                  AND t.expires_at > NOW() 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':selector', $selector);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    /**
     * Belirli bir kullanıcının tüm (veya belirli tipteki) tokenlarını siler
     * @param int $userId Kullanıcı ID
     * @param string|null $type Token tipi (null ise tüm tipler)
     * @return boolean İşlem başarılı ise true, değilse false
     */
    public function deleteUserTokens($userId, $type = null) {
        $query = "DELETE FROM tokens WHERE user_id = :user_id";
        
        if ($type !== null) {
            $query .= " AND type = :type";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        
        if ($type !== null) {
            $stmt->bindParam(':type', $type);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Belirli bir token'ı siler
     * @param string $selector Token seçicisi
     * @param string $type Token tipi
     * @return boolean İşlem başarılı ise true, değilse false
     */
    public function deleteToken($selector, $type) {
        $query = "DELETE FROM tokens 
                  WHERE selector = :selector 
                  AND type = :type";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':selector', $selector);
        $stmt->bindParam(':type', $type);
        
        return $stmt->execute();
    }
    
    /**
     * Süresi dolmuş tüm tokenleri temizler
     * @return boolean İşlem başarılı ise true, değilse false
     */
    public function cleanExpiredTokens() {
        $query = "DELETE FROM tokens WHERE expires_at < NOW()";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
} 
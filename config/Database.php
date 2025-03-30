<?php
/**
 * Veritabanı bağlantı sınıfı
 * PHP PDO kullanarak veritabanı bağlantısını yönetir
 */
class Database {
    // Veritabanı parametreleri
    private $host = "localhost";
    private $db_name = "auth_system";
    private $username = "root";
    private $password = "";
    private $conn;
    
    /**
     * Veritabanı bağlantısını oluşturur
     * @return PDO|null Bağlantı başarılı ise PDO nesnesi, değilse null
     */
    public function connect() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Bağlantı hatası: " . $e->getMessage();
        }
        
        return $this->conn;
    }
} 
<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Ưu tiên lấy từ biến môi trường (cho Docker), nếu không có thì dùng giá trị mặc định (cho Local)
        $this->host = getenv('DB_HOST') ?: "127.0.0.1";
        $this->db_name = getenv('DB_NAME') ?: "school_management";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=3306;dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            $this->conn->exec("set names utf8mb4");
        } catch(PDOException $exception) {
            error_log("DB Connection Error: " . $exception->getMessage());
            $this->conn = null;
        }

        return $this->conn;
    }
}
?>
<?php
class Database {
    private static $host = 'localhost';
    private static $dbName = 'car_rental';
    private static $username = 'root';
    private static $password = '';
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$dbName,
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}

class Config {
    public static function JWT_SECRET() {
        return 'Golf 6 GTD 125kw > passat b8';
    }

    // JWT Token expiration time (in seconds)
    // 24 hours = 60 * 60 * 24 = 86400 seconds
    public static function JWT_EXPIRATION() {
        return 60 * 60 * 48; // 24 hours - easy to adjust
    }
}
?>
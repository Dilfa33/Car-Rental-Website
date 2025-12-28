<?php

class Config {
    public static function DB_NAME() {
        return Config::get_env("DB_NAME", "defaultdb");
    }
    public static function DB_PORT() {
        return Config::get_env("DB_PORT", "25060"); // DO Managed Databases usually use 25060
    }
    public static function DB_USER() {
        return Config::get_env("DB_USER", 'doadmin');
    }
    public static function DB_PASSWORD() {
        return Config::get_env("DB_PASSWORD", '');
    }
    public static function DB_HOST() {
        return Config::get_env("DB_HOST", 'db-mysql-fra1-56591-do-user-31113528-0.h.db.ondigitalocean.com');
    }
    public static function JWT_SECRET() {
        return Config::get_env("JWT_SECRET", 'Golf 6 GTD 125kw > passat b8');
    }
    public static function JWT_EXPIRATION() {
        return 60 * 60 * 48;
    }

    public static function get_env($name, $default) {
        return isset($_ENV[$name]) && trim($_ENV[$name]) != "" ? $_ENV[$name] : $default;
    }
}

class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                // We pull all settings from the Config class
                $dsn = "mysql:host=" . Config::DB_HOST() .
                    ";port=" . Config::DB_PORT() .
                    ";dbname=" . Config::DB_NAME();

                self::$connection = new PDO(
                    $dsn,
                    Config::DB_USER(),
                    Config::DB_PASSWORD(),
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        // DO Managed DBs often require SSL; this line ensures it works
                        PDO::MYSQL_ATTR_SSL_CA => true
                    ]
                );
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>
<?php
require_once __DIR__ . '/BaseDao.php';

class AuthDao extends BaseDao {
    public function __construct() {
        parent::__construct("users");
    }

    protected function getPrimaryKey() {
        return "user_id";
    }

    public function getUserByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
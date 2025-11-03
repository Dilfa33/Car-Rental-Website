<?php
require_once 'BaseDao.php';

class TransactionDao extends BaseDao {
    public function __construct() {
        parent::__construct("transactions");
    }

    protected function getPrimaryKey() {
        return "transaction_id";
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM transactions WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteTransaction($id) {
        return $this->delete($id);
    }
}
?>
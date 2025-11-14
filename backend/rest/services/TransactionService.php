<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TransactionDao.php';

class TransactionService extends BaseService {

    public function __construct() {
        parent::__construct(new TransactionDao());
    }

    public function get_transactions() {
        return $this->dao->getAll();
    }

    public function get_transaction_by_id($id) {
        return $this->dao->getById($id);
    }

    public function add_transaction($data) {
        $required = ['user_id', 'amount', 'type'];
        foreach ($required as $f) {
            if (!isset($data[$f]) || $data[$f] === '') {
                throw new Exception("Field '$f' is required.");
            }
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new Exception("Amount must be a positive number.");
        }

        // Optional: ensure type is a non-empty string
        if (!is_string($data['type']) || empty($data['type'])) {
            throw new Exception("Invalid transaction type.");
        }

        return $this->dao->insert($data);
    }

    public function update_transaction($id, $data) {
        if (isset($data['amount']) && (!is_numeric($data['amount']) || $data['amount'] <= 0)) {
            throw new Exception("Amount must be a positive number.");
        }
        return $this->dao->update($id, $data);
    }

    public function delete_transaction($id) {
        return $this->dao->delete($id);
    }
}
?>

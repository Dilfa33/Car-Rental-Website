<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/TransactionDao.php';

class UserService extends BaseService {
    private $user_dao;
    private $transaction_dao;

    public function __construct() {
        $this->user_dao = new UserDao();
        $this->transaction_dao = new TransactionDao();
        parent::__construct(new UserDao());
    }

    // Get all users
    public function get_all() {
        return $this->user_dao->getAll();
    }

    // Get user by ID
    public function get_by_id($id) {
        return $this->user_dao->getById($id);
    }

    // Add new user
    public function add($data) {
        // Check if email already exists
        $existing = $this->user_dao->getUserByEmail($data['email']);
        if ($existing) {
            throw new Exception('Email already exists');
        }

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'customer';
        }

        // Set default balance if not provided
        if (!isset($data['balance'])) {
            $data['balance'] = 0;
        }

        return $this->user_dao->insert($data);
    }

    // Update user
    public function update($id, $data) {
        // Hash password if it's being updated
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            // Don't update password if not provided
            unset($data['password']);
        }

        return $this->user_dao->update($id, $data);
    }

    // Delete user
    public function delete($id) {
        return $this->user_dao->delete($id);
    }

    // Update user balance
    public function update_balance($user_id, $amount) {
        $user = $this->user_dao->getById($user_id);

        if (!$user) {
            throw new Exception('User not found');
        }

        $new_balance = $user['balance'] + $amount;

        if ($new_balance < 0) {
            throw new Exception('Insufficient balance');
        }

        return $this->user_dao->update($user_id, ['balance' => $new_balance]);
    }

    // Add credits to user account
    public function add_credits($user_id, $data) {
        // Validate amount
        if (!isset($data['amount']) || !is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new Exception('Invalid amount. Amount must be a positive number.');
        }

        $amount = floatval($data['amount']);
        $payment_method = isset($data['payment_method']) ? $data['payment_method'] : 'unknown';

        // Get user
        $user = $this->user_dao->getById($user_id);
        if (!$user) {
            throw new Exception('User not found');
        }

        // Update balance
        $new_balance = $user['balance'] + $amount;
        $this->user_dao->update($user_id, ['balance' => $new_balance]);

        // Create transaction record
        $transaction_data = [
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => 'deposit',
            'description' => 'Credits added via ' . $payment_method
        ];
        $this->transaction_dao->insert($transaction_data);

        // Return updated user data
        return [
            'user_id' => $user_id,
            'new_balance' => $new_balance,
            'amount_added' => $amount,
            'payment_method' => $payment_method
        ];
    }
}
?>
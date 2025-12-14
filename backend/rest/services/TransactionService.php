<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/TransactionDao.php';
require_once __DIR__ . '/UserService.php';

class TransactionService extends BaseService {
    private $transaction_dao;
    private $user_service;

    public function __construct() {
        $this->transaction_dao = new TransactionDao();
        $this->user_service = new UserService();
        parent::__construct(new TransactionDao());
    }

    // Get all transactions
    public function get_all() {
        return $this->transaction_dao->getAll();
    }

    // Get transactions for specific user
    public function get_user_transactions($user_id) {
        return $this->transaction_dao->getByUserId($user_id);
    }

    // Get transaction by ID
    public function get_by_id($id) {
        return $this->transaction_dao->getById($id);
    }

    // Add credits (top up)
    public function add_credits($user_id, $amount, $payment_method, $description = null) {
        // Validate amount
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than 0');
        }

        // Minimum top-up amount
        if ($amount < 5) {
            throw new Exception('Minimum top-up amount is $5');
        }

        // Create transaction record
        $transaction_data = [
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => 'top_up',
            'description' => $description ?: "Credit top-up via $payment_method"
        ];

        $transaction = $this->transaction_dao->insert($transaction_data);

        // Update user balance
        $this->user_service->update_balance($user_id, $amount);

        return [
            'success' => true,
            'message' => 'Credits added successfully',
            'transaction' => $transaction,
            'new_balance' => $this->user_service->get_by_id($user_id)['balance']
        ];
    }

    // Create booking payment transaction
    public function create_booking_payment($user_id, $amount, $booking_id) {
        // Validate amount
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than 0');
        }

        // Get user to check balance
        $user = $this->user_service->get_by_id($user_id);

        if ($user['balance'] < $amount) {
            throw new Exception('Insufficient balance');
        }

        // Create negative transaction (deduct from balance)
        $transaction_data = [
            'user_id' => $user_id,
            'amount' => -$amount,
            'type' => 'booking_payment',
            'description' => "Payment for booking #$booking_id"
        ];

        $transaction = $this->transaction_dao->insert($transaction_data);

        // Deduct from user balance
        $this->user_service->update_balance($user_id, -$amount);

        return [
            'success' => true,
            'message' => 'Payment processed successfully',
            'transaction' => $transaction,
            'new_balance' => $this->user_service->get_by_id($user_id)['balance']
        ];
    }

    // Create refund transaction
    public function create_refund($user_id, $amount, $booking_id) {
        // Validate amount
        if ($amount <= 0) {
            throw new Exception('Amount must be greater than 0');
        }

        // Create positive transaction (add to balance)
        $transaction_data = [
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => 'refund',
            'description' => "Refund for booking #$booking_id"
        ];

        $transaction = $this->transaction_dao->insert($transaction_data);

        // Add to user balance
        $this->user_service->update_balance($user_id, $amount);

        return [
            'success' => true,
            'message' => 'Refund processed successfully',
            'transaction' => $transaction,
            'new_balance' => $this->user_service->get_by_id($user_id)['balance']
        ];
    }

    // Admin adjustment (can be positive or negative)
    public function admin_adjustment($user_id, $amount, $description) {
        // Create transaction
        $transaction_data = [
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => 'admin_adjustment',
            'description' => $description
        ];

        $transaction = $this->transaction_dao->insert($transaction_data);

        // Update user balance
        $this->user_service->update_balance($user_id, $amount);

        return [
            'success' => true,
            'message' => 'Balance adjusted successfully',
            'transaction' => $transaction,
            'new_balance' => $this->user_service->get_by_id($user_id)['balance']
        ];
    }

    // Generic add transaction (for backward compatibility)
    public function add($data) {
        return $this->transaction_dao->insert($data);
    }

    // Update transaction
    public function update($id, $data) {
        return $this->transaction_dao->update($id, $data);
    }

    // Delete transaction
    public function delete($id) {
        return $this->transaction_dao->delete($id);
    }
}
?>
<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/BookingDao.php';
require_once __DIR__ . '/TransactionService.php';
require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/CarService.php';

class BookingService extends BaseService {
    private $booking_dao;
    private $transaction_service;
    private $user_service;
    private $car_service;

    public function __construct() {
        $this->booking_dao = new BookingDao();
        $this->transaction_service = new TransactionService();
        $this->user_service = new UserService();
        $this->car_service = new CarService();
        parent::__construct($this->booking_dao);
    }

    // Get all bookings (admin only)
    public function get_all() {
        return $this->booking_dao->getAll();
    }

    // Get bookings for specific user
    public function get_user_bookings($user_id) {
        return $this->booking_dao->getByUserId($user_id);
    }

    // Get single booking by ID
    public function get_by_id($id) {
        return $this->booking_dao->getById($id);
    }

    // Add new booking
    public function add($data) {
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        return $this->booking_dao->insert($data);
    }

    // Update booking
    public function update($id, $data) {
        return $this->booking_dao->update($id, $data);
    }

    // Delete booking
    public function delete($id) {
        return $this->booking_dao->delete($id);
    }

    // Cancel booking
    public function cancel_booking($booking_id, $user_id) {
        // Get booking to verify ownership
        $booking = $this->booking_dao->getById($booking_id);

        if (!$booking) {
            throw new Exception('Booking not found');
        }

        // Verify user owns this booking
        if ($booking['user_id'] != $user_id) {
            throw new Exception('You can only cancel your own bookings');
        }

        // Don't allow canceling completed bookings
        if ($booking['status'] === 'completed') {
            throw new Exception('Cannot cancel completed bookings');
        }

        // Update status to cancelled
        return $this->booking_dao->update($booking_id, ['status' => 'cancelled']);
    }

    // Extend booking
    public function extend_booking($booking_id, $user_id, $new_end_date) {
        // Get booking to verify ownership
        $booking = $this->booking_dao->getById($booking_id);

        if (!$booking) {
            throw new Exception('Booking not found');
        }

        // Verify user owns this booking
        if ($booking['user_id'] != $user_id) {
            throw new Exception('You can only extend your own bookings');
        }

        // Validate new date is after current end date
        if (strtotime($new_end_date) <= strtotime($booking['end_date'])) {
            throw new Exception('New end date must be after current end date');
        }

        // Update end date and reset status to pending
        return $this->booking_dao->update($booking_id, [
            'end_date' => $new_end_date,
            'status' => 'pending'
        ]);
    }

    // Create booking with payment transaction
    public function create_booking_with_payment($data) {
        // Validate required fields
        $required = ['user_id', 'car_id', 'start_date', 'end_date', 'total_price'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Field '$field' is required.");
            }
        }

        $user_id = $data['user_id'];
        $total_price = floatval($data['total_price']);

        // Check user balance
        $user = $this->user_service->get_by_id($user_id);
        if (!$user) {
            throw new Exception('User not found');
        }

        if ($user['balance'] < $total_price) {
            throw new Exception('Insufficient balance. Please add credits to your account.');
        }

        // Set default status
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        // Create booking
        $booking_result = $this->booking_dao->insert($data);

        if (!$booking_result) {
            throw new Exception('Failed to create booking');
        }

        // Get the created booking ID
        $booking_id = $this->booking_dao->getLastInsertId();

        // Create payment transaction (deduct from balance)
        try {
            $transaction_result = $this->transaction_service->create_booking_payment(
                $user_id,
                $total_price,
                $booking_id
            );
        } catch (Exception $e) {
            // If payment fails, we should ideally rollback the booking
            // For now, just throw the error
            throw new Exception('Booking created but payment failed: ' . $e->getMessage());
        }


        return [
            'success' => true,
            'message' => 'Booking created and payment processed successfully',
            'booking_id' => $booking_id,
            'new_balance' => $transaction_result['new_balance']
        ];
    }
}
?>
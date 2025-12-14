<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/BookingDao.php';

class BookingService extends BaseService {
    private $booking_dao;

    public function __construct() {
        $this->booking_dao = new BookingDao();
        parent::__construct(new BookingDao());
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
}
?>
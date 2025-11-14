<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/BookingDao.php';

class BookingService extends BaseService {

    public function __construct() {
        parent::__construct(new BookingDao());
    }

    public function get_bookings() {
        return $this->dao->getAll();
    }

    public function get_booking_by_id($id) {
        return $this->dao->getById($id);
    }

    public function add_booking($data) {
        // Required fields
        $required = ['user_id', 'car_id', 'start_date', 'end_date'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Field '$field' is required.");
            }
        }

        // Date validations
        $start = strtotime($data['start_date']);
        $end = strtotime($data['end_date']);
        if ($start === false || $end === false) {
            throw new Exception("Invalid date format for start_date or end_date.");
        }
        if ($start >= $end) {
            throw new Exception("start_date must be before end_date.");
        }

        // Optional: ensure total_price if provided is non-negative
        if (isset($data['total_price']) && $data['total_price'] < 0) {
            throw new Exception("total_price cannot be negative.");
        }

        // Defaults
        if (!isset($data['status'])) $data['status'] = 'pending';

        return $this->dao->insert($data);
    }

    public function update_booking($id, $data) {
        if (isset($data['start_date']) || isset($data['end_date'])) {
            $start = isset($data['start_date']) ? strtotime($data['start_date']) : null;
            $end = isset($data['end_date']) ? strtotime($data['end_date']) : null;
            if ($start !== null && $start === false) throw new Exception("Invalid start_date format.");
            if ($end !== null && $end === false) throw new Exception("Invalid end_date format.");
            if ($start !== null && $end !== null && $start >= $end) throw new Exception("start_date must be before end_date.");
        }

        if (isset($data['total_price']) && $data['total_price'] < 0) {
            throw new Exception("total_price cannot be negative.");
        }

        return $this->dao->update($id, $data);
    }

    public function delete_booking($id) {
        return $this->dao->delete($id);
    }
}
?>

<?php
require_once 'BaseDao.php';

class BookingDao extends BaseDao {
    public function __construct() {
        parent::__construct("bookings");
    }

    protected function getPrimaryKey() {
        return "booking_id";
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM bookings WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteBooking($id) {
        return $this->delete($id);
    }
}
?>
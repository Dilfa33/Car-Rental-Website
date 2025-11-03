<?php
require_once 'BaseDao.php';

class CarReviewDao extends BaseDao {
    public function __construct() {
        parent::__construct("car_reviews");
    }

    protected function getPrimaryKey() {
        return "review_id";
    }

    public function getByCarId($car_id) {
        $stmt = $this->connection->prepare("SELECT * FROM car_reviews WHERE car_id = :car_id");
        $stmt->bindParam(':car_id', $car_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteReview($id) {
        return $this->delete($id);
    }
}
?>
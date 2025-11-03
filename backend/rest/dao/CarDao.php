<?php
require_once 'BaseDao.php';

class CarDao extends BaseDao {
    public function __construct() {
        parent::__construct("cars");
    }

    protected function getPrimaryKey() {
        return "car_id";
    }

    public function getAvailableCars() {
        $stmt = $this->connection->prepare("SELECT * FROM cars WHERE availability_status = 'available'");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function deleteCar($id) {
        return $this->delete($id);
    }
}
?>
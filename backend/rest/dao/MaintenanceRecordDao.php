<?php
require_once 'BaseDao.php';

class MaintenanceRecordDao extends BaseDao {
    public function __construct() {
        parent::__construct("maintenance_records");
    }

    protected function getPrimaryKey() {
        return "record_id";
    }

    public function getByCarId($car_id) {
        $stmt = $this->connection->prepare("SELECT * FROM maintenance_records WHERE car_id = :car_id ORDER BY service_date DESC");
        $stmt->bindParam(':car_id', $car_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>

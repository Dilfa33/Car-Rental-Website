<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CarDao.php';

class CarService extends BaseService {

    public function __construct() {
        $dao = new CarDao();
        parent::__construct($dao);
    }

    public function get_cars() {
        return $this->dao->getAll();
    }

    public function get_car_by_id($id) {
        return $this->dao->getById($id);
    }

    public function add_car($data) {
        // Business Logic: Validate daily rate
        if (!isset($data['daily_rate']) || $data['daily_rate'] <= 0) {
            throw new Exception('Daily rate must be a positive value.');
        }

        // Business Logic: Validate year
        if (!isset($data['year']) || $data['year'] < 1900 || $data['year'] > 2030) {
            throw new Exception('Year must be between 1900 and 2030.');
        }

        // Business Logic: Required fields
        $required = ['brand', 'model', 'year', 'transmission', 'fuel_type', 'daily_rate'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Field '$field' is required.");
            }
        }

        // Set defaults if not provided
        if (!isset($data['availability_status'])) {
            $data['availability_status'] = 'available';
        }
        if (!isset($data['mileage'])) {
            $data['mileage'] = 0;
        }

        $result = $this->dao->insert($data);
        if ($result) {
            return ['success' => true, 'message' => 'Car added successfully'];
        } else {
            throw new Exception('Failed to add car');
        }
    }

    public function update_car($id, $data) {
        // Validate if updating daily_rate
        if (isset($data['daily_rate']) && $data['daily_rate'] <= 0) {
            throw new Exception('Daily rate must be a positive value.');
        }

        // Validate if updating year
        if (isset($data['year']) && ($data['year'] < 1900 || $data['year'] > 2030)) {
            throw new Exception('Year must be between 1900 and 2030.');
        }

        $result = $this->dao->update($id, $data);
        if ($result) {
            return ['success' => true, 'message' => 'Car updated successfully'];
        } else {
            throw new Exception('Failed to update car');
        }
    }

    public function delete_car($id) {
        $result = $this->dao->delete($id);
        if ($result) {
            return ['success' => true, 'message' => 'Car deleted successfully'];
        } else {
            throw new Exception('Failed to delete car');
        }
    }
}
?>
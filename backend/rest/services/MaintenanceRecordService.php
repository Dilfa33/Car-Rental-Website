<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/MaintenanceRecordDao.php';

class MaintenanceRecordService extends BaseService {

    public function __construct() {
        parent::__construct(new MaintenanceRecordDao());
    }

    public function get_records() {
        return $this->dao->getAll();
    }

    public function get_record_by_id($id) {
        return $this->dao->getById($id);
    }

    public function add_record($data) {
        $required = ['car_id', 'service_date', 'description', 'cost'];
        foreach ($required as $f) {
            if (!isset($data[$f]) || $data[$f] === '') {
                throw new Exception("Field '$f' is required.");
            }
        }

        if (!is_numeric($data['cost']) || $data['cost'] < 0) {
            throw new Exception("Cost must be a non-negative number.");
        }

        // Validate date format
        $d = strtotime($data['service_date']);
        if ($d === false) {
            throw new Exception("Invalid service_date format.");
        }

        return $this->dao->insert($data);
    }

    public function update_record($id, $data) {
        if (isset($data['cost']) && (!is_numeric($data['cost']) || $data['cost'] < 0)) {
            throw new Exception("Cost must be a non-negative number.");
        }

        if (isset($data['service_date']) && strtotime($data['service_date']) === false) {
            throw new Exception("Invalid service_date format.");
        }

        return $this->dao->update($id, $data);
    }

    public function delete_record($id) {
        return $this->dao->delete($id);
    }
}
?>

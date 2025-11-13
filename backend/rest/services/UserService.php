<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

class UserService extends BaseService {

    public function __construct() {
        parent::__construct(new UserDao());
    }

    public function add_user($data) {
        // Required fields
        $required = ['first_name', 'last_name', 'email', 'password_hash'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Field '$field' is required.");
            }
        }

        // Email validation
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Default role
        if (!isset($data['role'])) $data['role'] = 'customer';

        return $this->dao->insert($data);
    }

    public function update_user($id, $data) {
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        return $this->dao->update($id, $data);
    }

    public function delete_user($id) {
        return $this->dao->delete($id);
    }
}
?>

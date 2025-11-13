<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CarReviewDao.php';

class CarReviewService extends BaseService {

    public function __construct() {
        parent::__construct(new CarReviewDao());
    }

    public function get_reviews() {
        return $this->dao->getAll();
    }

    public function get_review_by_id($id) {
        return $this->dao->getById($id);
    }

    public function add_review($data) {
        $required = ['car_id', 'user_id', 'rating'];
        foreach ($required as $f) {
            if (!isset($data[$f]) || $data[$f] === '') {
                throw new Exception("Field '$f' is required.");
            }
        }

        if (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            throw new Exception("Rating must be an integer between 1 and 5.");
        }

        if (!isset($data['comment'])) $data['comment'] = null;

        return $this->dao->insert($data);
    }

    public function update_review($id, $data) {
        if (isset($data['rating']) && (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5)) {
            throw new Exception("Rating must be an integer between 1 and 5.");
        }
        return $this->dao->update($id, $data);
    }

    public function delete_review($id) {
        return $this->dao->delete($id);
    }
}
?>

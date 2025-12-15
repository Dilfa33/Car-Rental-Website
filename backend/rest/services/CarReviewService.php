<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CarreviewDao.php';

class CarReviewService extends BaseService {
    private $review_dao;

    public function __construct() {
        $this->review_dao = new CarreviewDao();
        parent::__construct(new CarreviewDao());
    }

    // Get all reviews
    public function get_all() {
        return $this->review_dao->getAll();
    }

    // Get review by ID
    public function get_by_id($id) {
        return $this->review_dao->getById($id);
    }

    // Add new review
    public function add($data) {
        // Validate rating is between 1-5
        if (isset($data['rating'])) {
            if ($data['rating'] < 1 || $data['rating'] > 5) {
                throw new Exception('Rating must be between 1 and 5');
            }
        }

        // Ensure required fields
        if (!isset($data['car_id']) || !isset($data['user_id'])) {
            throw new Exception('car_id and user_id are required');
        }

        return $this->review_dao->insert($data);
    }

    // Update review
    public function update($id, $data) {
        // Validate rating if provided
        if (isset($data['rating'])) {
            if ($data['rating'] < 1 || $data['rating'] > 5) {
                throw new Exception('Rating must be between 1 and 5');
            }
        }

        return $this->review_dao->update($id, $data);
    }

    // Delete review
    public function delete($id) {
        return $this->review_dao->delete($id);
    }

    // Get reviews for specific car
    public function get_by_car($car_id) {
        return $this->review_dao->getByCarId($car_id);
    }
}
?>
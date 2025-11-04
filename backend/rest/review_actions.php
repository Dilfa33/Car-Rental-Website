<?php
header('Content-Type: application/json');

require_once __DIR__ . '/dao/CarreviewDao.php';

$reviewDao = new CarreviewDao();

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch($method) {
        case 'POST':
            // CREATE - Add new review
            $input = json_decode(file_get_contents('php://input'), true);

            $reviewData = [
                'car_id' => $input['car_id'],
                'user_id' => $input['user_id'],
                'rating' => $input['rating'],
                'comment' => $input['comment']
            ];

            $result = $reviewDao->insert($reviewData);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Review added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add review']);
            }
            break;

        case 'GET':
            // READ - Get review(s)
            if(isset($_GET['review_id'])) {
                // Get single review
                $review = $reviewDao->getById($_GET['review_id']);
                if($review) {
                    echo json_encode(['success' => true, 'data' => $review]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Review not found']);
                }
            } else {
                // Get all reviews
                $reviews = $reviewDao->getAll();
                echo json_encode(['success' => true, 'data' => $reviews]);
            }
            break;

        case 'PUT':
            // UPDATE - Edit review
            $input = json_decode(file_get_contents('php://input'), true);
            $reviewId = $_GET['review_id'] ?? null;

            if(!$reviewId) {
                echo json_encode(['success' => false, 'message' => 'Review ID required']);
                break;
            }

            $result = $reviewDao->update($reviewId, $input);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Review updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update review']);
            }
            break;

        case 'DELETE':
            // DELETE - Remove review
            $reviewId = $_GET['review_id'] ?? null;

            if(!$reviewId) {
                echo json_encode(['success' => false, 'message' => 'Review ID required']);
                break;
            }

            $result = $reviewDao->delete($reviewId);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete review']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

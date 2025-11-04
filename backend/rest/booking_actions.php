<?php
header('Content-Type: application/json');

require_once __DIR__ . '/dao/BookingDao.php';

$bookingDao = new BookingDao();

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch($method) {
        case 'POST':
            // CREATE - Add new booking
            $input = json_decode(file_get_contents('php://input'), true);

            $bookingData = [
                'user_id' => $input['user_id'],
                'car_id' => $input['car_id'],
                'start_date' => $input['start_date'],
                'end_date' => $input['end_date'],
                'total_price' => $input['total_price'],
                'status' => $input['status'] ?? 'pending'
            ];

            $result = $bookingDao->insert($bookingData);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Booking added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add booking']);
            }
            break;

        case 'GET':
            // READ - Get booking(s)
            if(isset($_GET['booking_id'])) {
                // Get single booking
                $booking = $bookingDao->getById($_GET['booking_id']);
                if($booking) {
                    echo json_encode(['success' => true, 'data' => $booking]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Booking not found']);
                }
            } else {
                // Get all bookings
                $bookings = $bookingDao->getAll();
                echo json_encode(['success' => true, 'data' => $bookings]);
            }
            break;

        case 'PUT':
            // UPDATE - Edit booking
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingId = $_GET['booking_id'] ?? null;

            if(!$bookingId) {
                echo json_encode(['success' => false, 'message' => 'Booking ID required']);
                break;
            }

            $result = $bookingDao->update($bookingId, $input);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
            }
            break;

        case 'DELETE':
            // DELETE - Remove booking
            $bookingId = $_GET['booking_id'] ?? null;

            if(!$bookingId) {
                echo json_encode(['success' => false, 'message' => 'Booking ID required']);
                break;
            }

            $result = $bookingDao->delete($bookingId);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Booking deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete booking']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

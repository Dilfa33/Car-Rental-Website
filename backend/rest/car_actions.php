<?php
header('Content-Type: application/json');

require_once __DIR__ . '/dao/CarDao.php';

$carDao = new CarDao();

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch($method) {
        case 'POST':
            // CREATE - Add new car
            $input = json_decode(file_get_contents('php://input'), true);

            $carData = [
                'brand' => $input['brand'],
                'model' => $input['model'],
                'year' => $input['year'],
                'transmission' => $input['transmission'],
                'fuel_type' => $input['fuel_type'],
                'daily_rate' => $input['daily_rate'],
                'image_url' => $input['image_url'] ?? null,
                'mileage' => $input['mileage'] ?? 0,
                'availability_status' => 'available'
            ];

            $result = $carDao->insert($carData);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Car added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add car']);
            }
            break;

        case 'GET':
            // READ - Get car(s)
            if(isset($_GET['car_id'])) {
                // Get single car
                $car = $carDao->getById($_GET['car_id']);
                if($car) {
                    echo json_encode(['success' => true, 'data' => $car]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Car not found']);
                }
            } else {
                // Get all cars
                $cars = $carDao->getAll();
                echo json_encode(['success' => true, 'data' => $cars]);
            }
            break;

        case 'PUT':
            // UPDATE - Edit car
            $input = json_decode(file_get_contents('php://input'), true);
            $carId = $_GET['car_id'] ?? null;

            if(!$carId) {
                echo json_encode(['success' => false, 'message' => 'Car ID required']);
                break;
            }

            $result = $carDao->update($carId, $input);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Car updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update car']);
            }
            break;

        case 'DELETE':
            // DELETE - Remove car
            $carId = $_GET['car_id'] ?? null;

            if(!$carId) {
                echo json_encode(['success' => false, 'message' => 'Car ID required']);
                break;
            }

            $result = $carDao->delete($carId);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'Car deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete car']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
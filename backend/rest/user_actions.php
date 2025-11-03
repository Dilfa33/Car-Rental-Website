<?php
header('Content-Type: application/json');

require_once __DIR__ . '/dao/UserDao.php';

$userDao = new UserDao();

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch($method) {
        case 'POST':
            // CREATE - Add new user
            $input = json_decode(file_get_contents('php://input'), true);

            $userData = [
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
                'phone' => $input['phone'] ?? null,
                'role' => $input['role'] ?? 'customer',
                'balance' => 0
            ];

            $result = $userDao->insert($userData);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'User added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add user']);
            }
            break;

        case 'GET':
            // READ - Get user(s)
            if(isset($_GET['user_id'])) {
                // Get single user
                $user = $userDao->getById($_GET['user_id']);
                if($user) {
                    echo json_encode(['success' => true, 'data' => $user]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                }
            } else {
                // Get all users
                $users = $userDao->getAll();
                echo json_encode(['success' => true, 'data' => $users]);
            }
            break;

        case 'PUT':
            // UPDATE - Edit user
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $_GET['user_id'] ?? null;

            if(!$userId) {
                echo json_encode(['success' => false, 'message' => 'User ID required']);
                break;
            }

            $result = $userDao->update($userId, $input);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update user']);
            }
            break;

        case 'DELETE':
            // DELETE - Remove user
            $userId = $_GET['user_id'] ?? null;

            if(!$userId) {
                echo json_encode(['success' => false, 'message' => 'User ID required']);
                break;
            }

            $result = $userDao->delete($userId);

            if($result) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

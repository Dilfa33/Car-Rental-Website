<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService extends BaseService {
    private $auth_dao;

    public function __construct() {
        $this->auth_dao = new AuthDao();
        parent::__construct($this->auth_dao);
    }

    public function register($data) {
        // Validate required fields
        if (empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        if (empty($data['first_name']) || empty($data['last_name'])) {
            return ['success' => false, 'error' => 'First name and last name are required.'];
        }

        // Check if email already exists
        $existingUser = $this->auth_dao->getUserByEmail($data['email']);
        if ($existingUser) {
            return ['success' => false, 'error' => 'Email already registered.'];
        }

        // Hash password
        $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        unset($data['password']); // Remove plain password

        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'customer';
        }

        // Set default balance
        if (!isset($data['balance'])) {
            $data['balance'] = 0;
        }

        // Insert user
        $userId = $this->auth_dao->insert($data);

        if ($userId) {
            // Get the created user
            $user = $this->auth_dao->getById($userId);
            unset($user['password_hash']); // Remove password from response

            return ['success' => true, 'data' => $user];
        }

        return ['success' => false, 'error' => 'Failed to create user.'];
    }

    public function login($data) {
        // Validate required fields
        if (empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        // Get user by email
        $user = $this->auth_dao->getUserByEmail($data['email']);

        if (!$user) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        // Verify password
        if (!password_verify($data['password'], $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        // Remove password from user data
        unset($user['password_hash']);

        // Create JWT payload
        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + Config::JWT_EXPIRATION()
        ];

        // Generate JWT token
        $token = JWT::encode(
            $jwt_payload,
            Config::JWT_SECRET(),
            'HS256'
        );

        return [
            'success' => true,
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ];
    }
}
?>
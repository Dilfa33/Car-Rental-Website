<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load configuration
require_once __DIR__ . '/config.php';

// Load services
require_once __DIR__ . '/services/CarService.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/services/BookingService.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CarReviewService.php';
require_once __DIR__ . '/services/TransactionService.php';

// Load middleware
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Register services with Flight
Flight::register('carService', 'CarService');
Flight::register('auth_service', 'AuthService');
Flight::register('auth_middleware', 'AuthMiddleware');
Flight::register('bookingService', 'BookingService');
Flight::register('userService', 'UserService');
Flight::register('carReviewService', 'CarReviewService');
Flight::register('transactionService', 'TransactionService');
// MIDDLEWARE - Token verification for protected routes
Flight::before('start', function() {
    // Public routes that don't need authentication
    $publicRoutes = [
        '/auth/login',
        '/auth/register'
    ];

    $requestUrl = Flight::request()->url;

    // Check if route is public
    $isPublic = false;
    foreach ($publicRoutes as $route) {
        if (strpos($requestUrl, $route) === 0) {
            $isPublic = true;
            break;
        }
    }

    // If not public, verify JWT token
    if (!$isPublic) {
        try {
            $token = Flight::request()->getHeader("Authentication");
            Flight::auth_middleware()->verifyToken($token);
        } catch (Exception $e) {
            Flight::halt(401, $e->getMessage());
        }
    }
});

// Load routes
require_once __DIR__ . '/routes/AuthRoutes.php';
require_once __DIR__ . '/routes/CarRoutes.php';
require_once __DIR__ . '/routes/BookingRoutes.php';
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/CarReviewRoutes.php';
require_once __DIR__ . '/routes/TransactionRoutes.php';

// Start FlightPHP
Flight::start();
?>
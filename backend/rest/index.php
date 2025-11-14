<?php

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Load services
require_once __DIR__ . '/services/CarService.php';

// Register services with Flight
Flight::register('carService', 'CarService');

// Load routes
require_once __DIR__ . '/routes/CarRoutes.php';

// Start FlightPHP
Flight::start();

?>
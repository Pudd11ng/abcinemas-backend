<?php

/**
 * ABC Cinemas Backend API
 * Main application entry point
 * 
 * This API provides endpoints for:
 * - Movie management
 * - User management
 * - Booking system
 * - Reviews and ratings
 * - Showtime management
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Tuupola\Middleware\CorsMiddleware;

// Autoload dependencies
require __DIR__ . '/../vendor/autoload.php';

// Load configuration
require_once __DIR__ . '/config/config.php';

// Create Slim app
$app = AppFactory::create();

// Add middleware
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

// CORS Configuration
$app->add(new CorsMiddleware([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE", "OPTIONS"],
    "headers.allow" => ["Authorization", "Content-Type", "Accept", "Origin"],
    "headers.expose" => ["Authorization"],
    "credentials" => true,
    "cache" => 86400, // 24 hours
]));

// Health check endpoint
$app->get('/', function (Request $request, Response $response, $args) {
    $data = [
        'message' => 'ABC Cinemas Backend API',
        'version' => '1.0.0',
        'status' => 'running',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});

// API Status endpoint
$app->get('/api/status', function (Request $request, Response $response, $args) {
    $db = new Database();
    $connection = $db->connect();
    
    $status = [
        'api' => 'online',
        'database' => $connection ? 'connected' : 'disconnected',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    $response->getBody()->write(json_encode($status));
    return $response->withHeader('Content-Type', 'application/json');
});

// Load route controllers
(require __DIR__ . '/controllers/UserController.php')($app);
(require __DIR__ . '/controllers/MovieController.php')($app);
(require __DIR__ . '/controllers/BookingController.php')($app);
(require __DIR__ . '/controllers/ReviewController.php')($app);
(require __DIR__ . '/controllers/ShowtimeController.php')($app);

// Run the application
$app->run();

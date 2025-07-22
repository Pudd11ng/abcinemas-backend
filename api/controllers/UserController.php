<?php

/**
 * User Controller
 * Handles all user-related API endpoints
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $db = new Database();

    // Get all users
    $app->get('/api/users', function (Request $request, Response $response, $args) use ($db) {
        try {
            $conn = $db->connect();
            if (!$conn) {
                throw new Exception("Database connection failed");
            }
              $sql = "SELECT user_id, full_name, email, phone_number, role, date_of_birth, registration_date FROM user";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $result,
                'count' => count($result)
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Get user by ID
    $app->get('/api/users/{id}', function (Request $request, Response $response, $args) use ($db) {
        try {
            $id = (int)$args['id'];
            $conn = $db->connect();
            if (!$conn) {
                throw new Exception("Database connection failed");
            }
              $sql = "SELECT user_id, full_name, email, phone_number, role, date_of_birth, registration_date FROM user WHERE user_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'data' => $user
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Create a new user
    $app->post('/api/users', function (Request $request, Response $response, $args) use ($db) {
        try {
            $conn = $db->connect();
            if (!$conn) {
                throw new Exception("Database connection failed");
            }
            
            $data = $request->getParsedBody();
            
            // Validate required fields
            $required = ['full_name', 'email', 'password', 'date_of_birth', 'phone_number'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            // Check if email already exists
            $checkSql = "SELECT user_id FROM user WHERE email = :email";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bindValue(':email', $data['email']);
            $checkStmt->execute();
            
            if ($checkStmt->fetch()) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Email already exists'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $sql = "INSERT INTO user (full_name, email, password, role, date_of_birth, phone_number) VALUES (:full_name, :email, :password, :role, :date_of_birth, :phone_number)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':full_name', $data['full_name']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':role', $data['role'] ?? 'user');
            $stmt->bindValue(':date_of_birth', $data['date_of_birth']);
            $stmt->bindValue(':phone_number', $data['phone_number']);
            
            if ($stmt->execute()) {
                $userId = $conn->lastInsertId();
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => [
                        'user_id' => $userId,
                        'email' => $data['email']
                    ]
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                throw new Exception("Failed to create user");
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Update a user
    $app->put('/api/users/{id}', function (Request $request, Response $response, $args) use ($db) {
        try {
            $id = (int)$args['id'];
            $conn = $db->connect();
            if (!$conn) {
                throw new Exception("Database connection failed");
            }
            
            $data = $request->getParsedBody();
            
            // Validate required fields
            $required = ['full_name', 'email', 'date_of_birth', 'phone_number'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }

            $sql = "UPDATE user SET full_name = :full_name, email = :email, date_of_birth = :date_of_birth, phone_number = :phone_number WHERE user_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':full_name', $data['full_name']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':date_of_birth', $data['date_of_birth']);
            $stmt->bindValue(':phone_number', $data['phone_number']);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'User updated successfully'
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                throw new Exception("Failed to update user");
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Delete a user
    $app->delete('/api/users/{id}', function (Request $request, Response $response, $args) use ($db) {
        try {
            $id = (int)$args['id'];
            $conn = $db->connect();
            if (!$conn) {
                throw new Exception("Database connection failed");
            }

            $sql = "DELETE FROM user WHERE user_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $response->getBody()->write(json_encode([
                        'success' => true,
                        'message' => 'User deleted successfully'
                    ]));
                    return $response->withHeader('Content-Type', 'application/json');
                } else {
                    $response->getBody()->write(json_encode([
                        'success' => false,
                        'error' => 'User not found'
                    ]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
                }
            } else {
                throw new Exception("Failed to delete user");
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // User login
    $app->post('/api/users/login', function (Request $request, Response $response, $args) use ($db) {
        try {
            $conn = $db->connect();
            if (!$conn) {
                throw new Exception("Database connection failed");
            }
            
            $data = $request->getParsedBody();
            
            if (!isset($data['email']) || !isset($data['password'])) {
                throw new Exception("Email and password are required");
            }

            $sql = "SELECT user_id, full_name, email, password, role FROM user WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':email', $data['email']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($data['password'], $user['password'])) {
                unset($user['password']); // Remove password from response
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => $user
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'error' => 'Invalid email or password'
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
};

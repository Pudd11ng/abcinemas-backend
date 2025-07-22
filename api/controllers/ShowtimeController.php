<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $db = new Database();

    // Get all showtimes
    $app->get('/api/showtimes', function (Request $request, Response $response) use ($db) {
        try {
            $conn = $db->connect();
            $params = $request->getQueryParams();
            $sql = "SELECT * FROM showtimes";
            $conditions = [];
            $bindings = [];
            
            // Filter by branch if provided
            if (isset($params['branch'])) {
                $conditions[] = "branch = :branch";
                $bindings[':branch'] = $params['branch'];
            }
            
            // Filter by movie if provided
            if (isset($params['movie_id'])) {
                $conditions[] = "movie_id = :movie_id";
                $bindings[':movie_id'] = $params['movie_id'];
            }
              // Filter by date if provided
            if (isset($params['date'])) {
                $conditions[] = "show_date = :date";
                $bindings[':date'] = $params['date'];
            }
            
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $sql .= " ORDER BY show_date, show_time";
            
            $stmt = $conn->prepare($sql);
            foreach ($bindings as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $showtimes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response->getBody()->write(json_encode(['showtimes' => $showtimes]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error fetching showtimes: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Get showtime by ID
    $app->get('/api/showtimes/{id}', function (Request $request, Response $response, array $args) use ($db) {
        try {
            $conn = $db->connect();
            $id = $args['id'];
            
            $sql = "SELECT * FROM showtimes WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $showtime = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($showtime) {
                $response->getBody()->write(json_encode($showtime));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(['error' => 'Showtime not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error fetching showtime: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Create a new showtime
    $app->post('/api/showtimes', function (Request $request, Response $response) use ($db) {
        try {
            $conn = $db->connect();
            $data = $request->getParsedBody();
              if (!isset($data['movie_id']) || !isset($data['branch']) || !isset($data['hall']) || 
                !isset($data['show_date']) || !isset($data['show_time'])) {
                $response->getBody()->write(json_encode(['error' => 'Missing required fields: movie_id, branch, hall, show_date, show_time']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $sql = "INSERT INTO showtimes (movie_id, branch, hall, show_date, show_time) VALUES (:movie_id, :branch, :hall, :show_date, :show_time)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':movie_id', $data['movie_id']);
            $stmt->bindValue(':branch', $data['branch']);
            $stmt->bindValue(':hall', $data['hall']);
            $stmt->bindValue(':show_date', $data['show_date']);
            $stmt->bindValue(':show_time', $data['show_time']);

            if ($stmt->execute()) {
                $response->getBody()->write(json_encode(['message' => 'Showtime created successfully']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Error creating showtime']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error creating showtime: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Update a showtime
    $app->put('/api/showtimes/{id}', function (Request $request, Response $response, array $args) use ($db) {
        try {
            $conn = $db->connect();
            $id = $args['id'];
            $data = $request->getParsedBody();
              $sql = "UPDATE showtimes SET movie_id = :movie_id, branch = :branch, hall = :hall, show_date = :show_date, show_time = :show_time WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':movie_id', $data['movie_id']);
            $stmt->bindValue(':branch', $data['branch']);
            $stmt->bindValue(':hall', $data['hall']);
            $stmt->bindValue(':show_date', $data['show_date']);
            $stmt->bindValue(':show_time', $data['show_time']);

            if ($stmt->execute()) {
                $response->getBody()->write(json_encode(['message' => 'Showtime updated successfully']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Error updating showtime']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error updating showtime: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Delete a showtime
    $app->delete('/api/showtimes/{id}', function (Request $request, Response $response, array $args) use ($db) {
        try {
            $conn = $db->connect();
            $id = $args['id'];
            
            $sql = "DELETE FROM showtimes WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            
            if ($stmt->execute()) {
                $response->getBody()->write(json_encode(['message' => 'Showtime deleted successfully']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Error deleting showtime']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error deleting showtime: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
};

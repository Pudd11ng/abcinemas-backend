<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $db = new Database();

    // Get all movies  
    $app->get('/api/movies', function (Request $request, Response $response) use ($db) {
        $conn = $db->connect();

        if (!$conn) {
            $response->getBody()->write(json_encode(['error' => 'Database connection failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $sql = "SELECT * FROM movies";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode(['movies' => $movies]));
        return $response->withHeader('Content-Type', 'application/json');
    });

    // Get movie by ID
    $app->get('/api/movies/{id}', function (Request $request, Response $response, $args) use ($db) {
        $conn = $db->connect();

        if (!$conn) {
            $response->getBody()->write(json_encode(['error' => 'Database connection failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $id = $args['id'];
        $sql = "SELECT * FROM movies WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($movie) {
            $response->getBody()->write(json_encode($movie));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['error' => 'Movie not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
    });

    // Create a new movie
    $app->post('/api/movies', function (Request $request, Response $response) use ($db) {
        $conn = $db->connect();

        if (!$conn) {
            $response->getBody()->write(json_encode(['error' => 'Database connection failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

        $data = $request->getParsedBody();
        $movieID = $data['id'];
        $title = $data['title'];
        $description = $data['description'];
        $genre = $data['genre'];
        $duration = $data['duration'];

        $sql = "INSERT INTO movies (id, title, description, genre, duration) VALUES (:id, :title, :description, :genre, :duration)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':id', $movieID);
        $stmt->bindValue(':title', $title);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':genre', $genre);
        $stmt->bindValue(':duration', $duration);

        if ($stmt->execute()) {
            $response->getBody()->write(json_encode(['message' => 'New movie has been created successfully']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Error creating movie']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
      // Update a movie
    $app->put('/api/movies/{id}', function (Request $request, Response $response, array $args) use ($db) {
        try {
            $conn = $db->connect();
            $data = $request->getParsedBody();
            $id = $data['id'];
            $title = $data['title'];
            $description = $data['description'];
            $genre = $data['genre'];
            $duration = $data['duration'];
            $sql = "UPDATE movies SET title = :title, description = :description, genre = :genre, duration = :duration WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':genre', $genre);
            $stmt->bindValue(':duration', $duration);
            $stmt->execute();
            $response->getBody()->write(json_encode(["message" => "Movies updated successfully"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["error" => "Error updating movies: " . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Delete a movie
    $app->delete('/api/movies/{id}', function (Request $request, Response $response, $args) use ($db) {
        try {
            $id = $args['id'];
            $conn = $db->connect();
            $sql = "DELETE FROM movies WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $response->getBody()->write(json_encode(["message" => "Movies deleted successfully"]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(["error" => "Error deleting movies: " . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
};

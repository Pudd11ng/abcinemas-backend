<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $db = new Database();

    // Create a new review
    $app->post('/api/reviews', function (Request $request, Response $response) use ($db) {
        $conn = $db->connect();

        if (!$conn) {
            $response->getBody()->write(json_encode(['error' => 'Database connection failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }        $data = $request->getParsedBody();
        
        // Validate required fields
        if (!isset($data['movieID']) || !isset($data['rating'])) {
            $response->getBody()->write(json_encode(['error' => 'movieID and rating are required']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        
        $movieID = $data['movieID'];
        $rating = (int)$data['rating'];
        $review = $data['review'] ?? '';
        
        // Validate rating range
        if ($rating < 1 || $rating > 5) {
            $response->getBody()->write(json_encode(['error' => 'Rating must be between 1 and 5']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $sql = "INSERT INTO rating_reviews (movieID, rating, review) VALUES (:movieID, :rating, :review)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':movieID', $movieID);
        $stmt->bindValue(':rating', $rating);
        $stmt->bindValue(':review', $review);

        if ($stmt->execute()) {
            $response->getBody()->write(json_encode(['message' => 'New review created successfully']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } else {
            $response->getBody()->write(json_encode(['error' => 'Error creating review']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);        }
    });

    // Get all reviews
    $app->get('/api/reviews', function (Request $request, Response $response) use ($db) {
        try {
            $conn = $db->connect();
            $params = $request->getQueryParams();
            
            if (isset($params['movieID'])) {
                // Get reviews for specific movie
                $movieID = $params['movieID'];
                $sql = "SELECT * FROM rating_reviews WHERE movieID = :movieID";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':movieID', $movieID);
            } else {
                // Get all reviews
                $sql = "SELECT * FROM rating_reviews";
                $stmt = $conn->prepare($sql);
            }
            
            $stmt->execute();
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response->getBody()->write(json_encode(['reviews' => $reviews]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error fetching reviews: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });

    // Get single review by ID
    $app->get('/api/reviews/{id}', function (Request $request, Response $response, array $args) use ($db) {
        try {
            $conn = $db->connect();
            $review_id = $args['id'];
            
            $sql = "SELECT * FROM rating_reviews WHERE review_id = :review_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':review_id', $review_id);
            $stmt->execute();
            $review = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($review) {
                $response->getBody()->write(json_encode($review));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(['error' => 'Review not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error fetching review: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
      // Update a review
    $app->put('/api/reviews/{id}', function (Request $request, Response $response, array $args) use ($db) {
        try {
            $conn = $db->connect();
            $review_id = $args['id'];
            $data = $request->getParsedBody();
            
            if (!isset($data['rating']) || !isset($data['review'])) {
                $response->getBody()->write(json_encode(['error' => 'Rating and review are required for updating']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $rating = $data['rating'];
            $review = $data['review'];

            $sql = "UPDATE rating_reviews SET rating = :rating, review = :review WHERE review_id = :review_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':rating', $rating);
            $stmt->bindValue(':review', $review);
            $stmt->bindValue(':review_id', $review_id);

            if ($stmt->execute()) {
                $response->getBody()->write(json_encode(['message' => 'Review updated successfully']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Error updating review']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error updating review: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });    // Delete a review
    $app->delete('/api/reviews/{id}', function (Request $request, Response $response, array $args) use ($db) {
        try {
            $conn = $db->connect();
            $review_id = $args['id'];

            $sql = "DELETE FROM rating_reviews WHERE review_id = :review_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':review_id', $review_id);

            if ($stmt->execute()) {
                $response->getBody()->write(json_encode(['message' => 'Review deleted successfully']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Error deleting review']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(['error' => 'Error deleting review: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    });
};

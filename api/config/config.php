<?php

/**
 * Database Configuration and Connection Handler
 * 
 * This class handles database connection configuration for ABC Cinemas Backend API
 * It uses environment variables for security when available, falls back to defaults
 */
class Database {
    private $host;
    private $user;
    private $password;
    private $dbname;
    private $connection = null;

    public function __construct() {
        // Load from environment variables or use defaults
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->user = $_ENV['DB_USER'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->dbname = $_ENV['DB_NAME'] ?? 'abcinemas';
    }

    /**
     * Establish database connection
     * @return PDO|null Returns PDO connection object or null on failure
     */
    public function connect() {
        if ($this->connection !== null) {
            return $this->connection;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $this->connection = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            return $this->connection;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get database connection instance
     * @return PDO|null
     */
    public function getConnection() {
        return $this->connect();
    }

    /**
     * Close database connection
     */
    public function disconnect() {
        $this->connection = null;
    }
}

// Legacy compatibility - maintain old class name
class db extends Database {}

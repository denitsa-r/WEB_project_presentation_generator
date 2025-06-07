<?php

class Database {
    private $pdo;

    public function __construct() {
        try {
            error_log("Attempting to connect to database: " . DB_HOST . ", " . DB_NAME);
            error_log("Database user: " . DB_USER);
            error_log("Database password length: " . strlen(DB_PASS));
            
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            error_log("DSN: " . $dsn);
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            error_log("PDO options: " . print_r($options, true));
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            error_log("PDO object created");
            
            // Проверяваме връзката
            $this->pdo->query("SELECT 1");
            error_log("Database connection test successful");
            
            error_log("Successfully connected to database");
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function query($sql, $params = []) {
        try {
            error_log("Executing SQL query: " . $sql);
            error_log("With parameters: " . print_r($params, true));
            
        $stmt = $this->pdo->prepare($sql);
            error_log("Statement prepared");
            
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $paramName = is_numeric($key) ? $key + 1 : $key;
                    error_log("Binding parameter: " . $paramName . " = " . (is_array($value) ? print_r($value, true) : $value));
                    $stmt->bindValue($paramName, $value);
                }
            }
            
            $result = $stmt->execute();
            error_log("Statement executed with result: " . ($result ? "success" : "failed"));
            
            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new PDOException("Query execution failed: " . implode(", ", $stmt->errorInfo()));
            }
            
            error_log("Query executed successfully");
        return $stmt;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("SQL: " . $sql);
            error_log("Parameters: " . print_r($params, true));
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function prepare($sql) {
        error_log("Preparing SQL statement: " . $sql);
        return $this->pdo->prepare($sql);
    }

    public function lastInsertId() {
        $id = $this->pdo->lastInsertId();
        error_log("Last insert ID: " . $id);
        return $id;
    }

    public function beginTransaction() {
        error_log("Starting database transaction");
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        error_log("Committing database transaction");
        return $this->pdo->commit();
    }

    public function rollBack() {
        error_log("Rolling back database transaction");
        return $this->pdo->rollBack();
    }
}

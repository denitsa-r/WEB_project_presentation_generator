-- Create the database
CREATE DATABASE IF NOT EXISTS presentation_generator;
USE presentation_generator;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create workspaces table
CREATE TABLE IF NOT EXISTS workspaces (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create user_workspaces table
CREATE TABLE IF NOT EXISTS user_workspaces (
    workspace_id INT,
    user_id INT,
    role ENUM('owner', 'editor', 'viewer') DEFAULT 'viewer',
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    PRIMARY KEY (workspace_id, user_id)
);

-- Create presentations table
CREATE TABLE IF NOT EXISTS presentations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    workspace_id INT,
    title VARCHAR(100) NOT NULL,
    language VARCHAR(10) DEFAULT 'en',
    theme VARCHAR(20) DEFAULT 'light',
    version VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(id)
);

-- Create slides table
CREATE TABLE IF NOT EXISTS slides (
    id INT PRIMARY KEY AUTO_INCREMENT,
    presentation_id INT,
    type VARCHAR(50) NOT NULL,
    order_number INT NOT NULL,
    style VARCHAR(50),
    content TEXT,
    navigation_next INT,
    navigation_prev INT,
    FOREIGN KEY (presentation_id) REFERENCES presentations(id),
    FOREIGN KEY (navigation_next) REFERENCES slides(id),
    FOREIGN KEY (navigation_prev) REFERENCES slides(id)
); 